<?php
namespace App\Http\Controllers;

use App\Models\Distribusi;
use App\Models\Kelompok;
use App\Models\BarangBantuan;
use App\Models\BiayaOperasional;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DistribusiController extends Controller
{
    private function bolehLihat(Distribusi $distribusi): bool
    {
        $user = auth()->user();
        if ($user->isAdmin()) return true;
        $distribusi->loadMissing('kelompok');
        if ($user->isKetuaKelompok()) {
            return (int) $user->kelompok_id === (int) $distribusi->kelompok_id;
        }
        $kelompok = $distribusi->kelompok;
        if (!$kelompok) return false;
        if ($user->wilayah_kabupaten && $kelompok->daerah !== $user->wilayah_kabupaten) return false;
        if ($user->wilayah_kecamatan && $kelompok->kecamatan !== $user->wilayah_kecamatan) return false;
        if ($user->wilayah_desa && $kelompok->desa !== $user->wilayah_desa) return false;
        return true;
    }

    public function index(Request $request)
    {
        $query = Distribusi::with(['kelompok' => fn ($q) => $q->withCount('penerima')->with('ketuaUser'), 'creator']);
        $user = auth()->user();
        if ($user->isKetuaKelompok()) {
            $query->where('kelompok_id', $user->kelompok_id);
        } elseif ($user->isRelawan()) {
            $query->whereHas('kelompok', function ($q) use ($user) {
                if ($user->wilayah_kabupaten) $q->where('daerah', $user->wilayah_kabupaten);
                if ($user->wilayah_kecamatan) $q->where('kecamatan', $user->wilayah_kecamatan);
                if ($user->wilayah_desa) $q->where('desa', $user->wilayah_desa);
            });
        }
        if ($request->status) $query->where('status', $request->status);
        $distribusi = $query->orderBy('tanggal', 'desc')->paginate(15);
        return view('distribusi.index', compact('distribusi'));
    }

    public function create()
    {
        $kelompoks = Kelompok::withCount('penerima')->with('ketuaUser')->get();
        $barang = BarangBantuan::all();
        return view('distribusi.form', compact('kelompoks', 'barang'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['kode_distribusi'] = 'DST-' . now()->format('Ymd') . '-' . strtoupper(Str::random(4));
        $data['created_by'] = auth()->id();
        Distribusi::create($data);
        return redirect()->route('distribusi.index')->with('success', 'Distribusi tersimpan & tampil di peta.');
    }

    public function edit(Distribusi $distribusi)
    {
        $kelompoks = Kelompok::withCount('penerima')->with('ketuaUser')->get();
        return view('distribusi.form', compact('distribusi', 'kelompoks'));
    }

    public function update(Request $request, Distribusi $distribusi)
    {
        $distribusi->update($this->validated($request));
        return redirect()->route('distribusi.index')->with('success', 'Distribusi diupdate.');
    }

    public function destroy(Distribusi $distribusi)
    {
        $distribusi->delete();
        return redirect()->route('distribusi.index')->with('success', 'Distribusi dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'nama_kegiatan' => 'required',
            'tanggal' => 'required|date',
            'lokasi' => 'required',
            'titik_koordinat' => 'required|regex:/^-?\d+(\.\d+)?,\s*-?\d+(\.\d+)?$/',
            'kelompok_id' => 'required|exists:kelompoks,id',
            'jenis_bantuan' => 'required',
            'jumlah_paket' => 'required|integer',
            'estimasi_nilai_total' => 'nullable|numeric',
            'sumber_dana' => 'nullable',
            'status' => 'required|in:direncanakan,berlangsung,selesai',
        ]);
    }

    public function show(Distribusi $distribusi)
    {
        abort_unless($this->bolehLihat($distribusi), 403, 'Distribusi di luar wilayah atau penugasan Anda.');
        $distribusi->load([
            'kelompok' => fn ($q) => $q->withCount('penerima')->with('ketuaUser'),
            'creator', 'penerimaDistribusi.penerima', 'items.barang', 'biayaOperasional'
        ]);
        return view('distribusi.show', compact('distribusi'));
    }

    public function terima(Distribusi $distribusi, $penerimaId)
    {
        abort_unless($this->bolehLihat($distribusi), 403, 'Distribusi di luar wilayah kerja Anda.');
        $updated = $distribusi->penerimaDistribusi()
            ->where('penerima_id', $penerimaId)
            ->update(['status' => 'diterima', 'tanda_terima' => true, 'received_by' => auth()->id(), 'received_at' => now()]);
        abort_if($updated === 0, 404, 'Penerima tidak terdaftar pada distribusi ini.');
        return back()->with('success', 'Tanda terima dicatat.');
    }

    public function selesai(Distribusi $distribusi)
    {
        abort_unless($this->bolehLihat($distribusi), 403, 'Distribusi di luar wilayah kerja Anda.');
        $distribusi->update(['status' => 'selesai']);
        return back()->with('success', 'Distribusi selesai.');
    }

    public function dataPeta()
    {
        $query = Distribusi::whereNotNull('titik_koordinat')
            ->with(['kelompok' => fn ($q) => $q->withCount('penerima')]);
        $user = auth()->user();
        if ($user->isKetuaKelompok()) {
            $query->where('kelompok_id', $user->kelompok_id);
        } elseif ($user->isRelawan()) {
            $query->whereHas('kelompok', function ($q) use ($user) {
                if ($user->wilayah_kabupaten) $q->where('daerah', $user->wilayah_kabupaten);
                if ($user->wilayah_kecamatan) $q->where('kecamatan', $user->wilayah_kecamatan);
                if ($user->wilayah_desa) $q->where('desa', $user->wilayah_desa);
            });
        }
        $distribusi = $query->get()
            ->map(function ($d) {
                $coord = explode(',', $d->titik_koordinat);
                return [
                    'name' => $d->nama_kegiatan,
                    'lat' => (float)($coord[0] ?? 0),
                    'lng' => (float)($coord[1] ?? 0),
                    'paket' => $d->jumlah_paket,
                    'nilai' => $d->estimasi_nilai_total,
                    'penerima' => $d->kelompok->penerima_count ?? 0,
                    'kelompok' => $d->kelompok->nama ?? '-',
                    'status' => $d->status,
                    'tgl' => is_object($d->tanggal) ? $d->tanggal->format('d M Y') : date('d M Y', strtotime($d->tanggal)),
                ];
            });
        return response()->json($distribusi);
    }
}

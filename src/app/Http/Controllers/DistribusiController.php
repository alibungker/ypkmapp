<?php
namespace App\Http\Controllers;

use App\Models\Distribusi;
use App\Models\Kelompok;
use App\Models\BarangBantuan;
use App\Models\BiayaOperasional;
use Illuminate\Http\Request;

class DistribusiController extends Controller
{
    public function index(Request $request)
    {
        $query = Distribusi::with('kelompok', 'creator');
        if ($request->status) $query->where('status', $request->status);
        $distribusi = $query->orderBy('tanggal', 'desc')->paginate(15);
        return view('distribusi.index', compact('distribusi'));
    }

    public function create()
    {
        $kelompoks = Kelompok::all();
        $barang = BarangBantuan::all();
        return view('distribusi.form', compact('kelompoks', 'barang'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_kegiatan' => 'required',
            'tanggal' => 'required|date',
            'lokasi' => 'required',
            'kelompok_id' => 'required|exists:kelompoks,id',
            'jenis_bantuan' => 'required',
            'jumlah_paket' => 'required|integer',
            'estimasi_nilai_total' => 'nullable|numeric',
        ]);
        $data['kode_distribusi'] = 'DST-' . now()->format('Ymd') . '-' . strtoupper(\Str::random(4));
        $data['status'] = 'direncanakan';
        $data['created_by'] = auth()->id();
        Distribusi::create($data);
        return redirect()->route('distribusi.index')->with('success', 'Distribusi direncanakan.');
    }

    public function show(Distribusi $distribusi)
    {
        $distribusi->load('kelompok', 'creator', 'penerimaDistribusi.penerima', 'items.barang', 'biayaOperasional');
        return view('distribusi.show', compact('distribusi'));
    }

    public function terima(Distribusi $distribusi, $penerimaId)
    {
        $distribusi->penerimaDistribusi()
            ->where('penerima_id', $penerimaId)
            ->update(['status' => 'diterima', 'tanda_terima' => true, 'received_by' => auth()->id(), 'received_at' => now()]);
        return back()->with('success', 'Tanda terima dicatat.');
    }

    public function selesai(Distribusi $distribusi)
    {
        $distribusi->update(['status' => 'selesai']);
        return back()->with('success', 'Distribusi selesai.');
    }

    public function dataPeta()
    {
        $distribusi = Distribusi::whereNotNull('titik_koordinat')
            ->with('kelompok')
            ->get()
            ->map(function ($d) {
                $coord = explode(',', $d->titik_koordinat);
                return [
                    'name' => $d->nama_kegiatan,
                    'lat' => (float)($coord[0] ?? 0),
                    'lng' => (float)($coord[1] ?? 0),
                    'paket' => $d->jumlah_paket,
                    'nilai' => $d->estimasi_nilai_total,
                    'penerima' => $d->penerimaDistribusi()->count(),
                    'kelompok' => $d->kelompok->nama ?? '-',
                    'status' => $d->status,
                    'tgl' => $d->tanggal->format('d M Y'),
                ];
            });
        return response()->json($distribusi);
    }
}

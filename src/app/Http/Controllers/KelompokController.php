<?php
namespace App\Http\Controllers;

use App\Models\Kelompok;
use App\Models\Penerima;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KelompokController extends Controller
{
    public function index()
    {
        $query = Kelompok::withCount('penerima')->with('ketuaUser')->orderBy('daerah');
        $user = auth()->user();

        if ($user->isKetuaKelompok()) {
            $query->whereKey($user->kelompok_id);
        } elseif ($user->isRelawan()) {
            if ($user->wilayah_kabupaten) $query->where('daerah', $user->wilayah_kabupaten);
            if ($user->wilayah_kecamatan) $query->where('kecamatan', $user->wilayah_kecamatan);
            if ($user->wilayah_desa) $query->where('desa', $user->wilayah_desa);
        }

        $kelompoks = $query->get();
        $kabupatens = DB::table('wilayah_boundaries')
            ->where('kode', 'LIKE', '11.%')
            ->orderBy('nama')
            ->pluck('nama', 'kode');
        return view('kelompok.index', compact('kelompoks', 'kabupatens'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required',
            'kode' => 'required|unique:kelompoks,kode',
            'daerah' => 'required',
            'kecamatan' => 'nullable',
            'desa' => 'nullable',
            'description' => 'nullable',
        ]);
        $data['jumlah_anggota'] = 0;
        Kelompok::create($data);
        return redirect()->route('kelompok.index')->with('success', 'Kelompok berhasil dibuat.');
    }

    public function update(Request $request, Kelompok $kelompok)
    {
        $data = $request->validate([
            'nama' => 'required',
            'kode' => 'required|unique:kelompoks,kode,' . $kelompok->id,
            'daerah' => 'required',
            'kecamatan' => 'nullable',
            'desa' => 'nullable',
            'ketua_id' => 'nullable|exists:penerimas,id',
            'description' => 'nullable',
        ]);
        $kelompok->update($data);
        return redirect()->route('kelompok.index')->with('success', 'Kelompok diupdate.');
    }

    public function destroy(Kelompok $kelompok)
    {
        if ($kelompok->penerima()->count() > 0) {
            return back()->with('error', 'Tidak bisa hapus — kelompok masih punya anggota.');
        }
        $kelompok->delete();
        return redirect()->route('kelompok.index')->with('success', 'Kelompok dihapus.');
    }

    private function bolehLihat(Kelompok $kelompok): bool
    {
        $user = auth()->user();
        if ($user->isAdmin()) return true;
        if ($user->isKetuaKelompok()) return (int) $user->kelompok_id === (int) $kelompok->id;
        if ($user->wilayah_kabupaten && $kelompok->daerah !== $user->wilayah_kabupaten) return false;
        if ($user->wilayah_kecamatan && $kelompok->kecamatan !== $user->wilayah_kecamatan) return false;
        if ($user->wilayah_desa && $kelompok->desa !== $user->wilayah_desa) return false;
        return true;
    }

    public function show(Kelompok $kelompok)
    {
        abort_unless($this->bolehLihat($kelompok), 403, 'Kelompok di luar wilayah atau penugasan Anda.');
        $kelompok->load(['distribusi', 'ketuaUser']);
        $kelompok->loadCount('penerima');
        $anggota = $kelompok->penerima()->orderBy('nama')->paginate(25)->withQueryString();
        return view('kelompok.show', compact('kelompok', 'anggota'));
    }

    public function anggota(Kelompok $kelompok)
    {
        abort_unless($this->bolehLihat($kelompok), 403, 'Kelompok di luar wilayah atau penugasan Anda.');
        $penerima = $kelompok->penerima()->orderBy('nama')->get(['id', 'nama', 'nik', 'status']);
        return response()->json($penerima);
    }
}

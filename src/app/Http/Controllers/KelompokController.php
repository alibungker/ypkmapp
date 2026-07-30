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
        $kelompoks = Kelompok::withCount('penerima')->with('ketua')->orderBy('daerah')->get();
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

    public function show(Kelompok $kelompok)
    {
        $kelompok->load('penerima', 'distribusi', 'ketua');
        return view('kelompok.show', compact('kelompok'));
    }

    public function anggota(Kelompok $kelompok)
    {
        $penerima = $kelompok->penerima()->orderBy('nama')->get();
        return response()->json($penerima);
    }
}

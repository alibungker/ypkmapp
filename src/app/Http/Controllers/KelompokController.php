<?php
namespace App\Http\Controllers;

use App\Models\Kelompok;
use App\Models\Penerima;
use Illuminate\Http\Request;

class KelompokController extends Controller
{
    public function index()
    {
        $kelompoks = Kelompok::withCount('penerima')->orderBy('daerah')->get();
        return view('kelompok.index', compact('kelompoks'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required',
            'kode' => 'required|unique:kelompoks,kode',
            'daerah' => 'required',
            'kecamatan' => 'nullable',
        ]);
        Kelompok::create($data);
        return redirect()->route('kelompok.index')->with('success', 'Kelompok dibuat.');
    }

    public function show(Kelompok $kelompok)
    {
        $kelompok->load('penerima', 'distribusi');
        return view('kelompok.show', compact('kelompok'));
    }

    public function anggota(Kelompok $kelompok)
    {
        $penerima = $kelompok->penerima()->orderBy('nama')->get();
        return response()->json($penerima);
    }
}

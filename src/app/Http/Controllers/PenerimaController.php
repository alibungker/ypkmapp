<?php
namespace App\Http\Controllers;

use App\Models\Penerima;
use App\Models\Kelompok;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class PenerimaController extends Controller
{
    public function index(Request $request)
    {
        $query = Penerima::with('kelompok');

        if ($request->search) {
            $query->where('nama', 'like', "%{$request->search}%")
                ->orWhere('nik', 'like', "%{$request->search}%");
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->sumber_data) {
            $query->where('sumber_data', $request->sumber_data);
        }
        if ($request->kabupaten) {
            $query->where('kabupaten', $request->kabupaten);
        }

        $penerima = $query->orderBy('created_at', 'desc')->paginate(20);
        $kelompoks = Kelompok::all();
        $kabupatens = DB::table('wilayah_boundaries')
            ->where('kode', 'LIKE', '11.%')
            ->orderBy('nama')
            ->pluck('nama', 'kode');
        return view('penerima.index', compact('penerima', 'kelompoks', 'kabupatens'));
    }

    public function create()
    {
        $kelompoks = Kelompok::all();
        return view('penerima.form', compact('kelompoks'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nik' => 'required|unique:penerimas,nik',
            'no_kk' => 'nullable',
            'nama' => 'required',
            'tempat_lahir' => 'nullable',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'nullable|in:L,P',
            'alamat' => 'required',
            'kabupaten' => 'required',
            'kecamatan' => 'required',
            'desa' => 'required',
            'phone' => 'required',
            'jumlah_keluarga' => 'nullable|integer',
            'kelompok_id' => 'required|exists:kelompoks,id',
            'sumber_data' => 'required|in:mandiri,relawan,ketua_kelompok',
        ]);

        $data['status'] = 'pending';
        $data['provinsi'] = 'Aceh';

        if ($request->hasFile('foto_ktp')) {
            $data['foto_ktp'] = $request->file('foto_ktp')->store('penerima/ktp', 'public');
        }

        Penerima::create($data);
        return redirect()->route('penerima.index')->with('success', 'Data penerima berhasil ditambahkan.');
    }

    public function show(Penerima $penerima)
    {
        $penerima->load('kelompok', 'verifikator', 'penerimaDistribusi.distribusi');
        return view('penerima.show', compact('penerima'));
    }

    public function edit(Penerima $penerima)
    {
        $kelompoks = Kelompok::all();
        return view('penerima.form', compact('penerima', 'kelompoks'));
    }

    public function update(Request $request, Penerima $penerima)
    {
        $data = $request->validate([
            'nik' => 'required|unique:penerimas,nik,' . $penerima->id,
            'nama' => 'required',
            'alamat' => 'required',
            'phone' => 'required',
            'kelompok_id' => 'required|exists:kelompoks,id',
        ]);

        $penerima->update($data);
        return redirect()->route('penerima.index')->with('success', 'Data penerima diupdate.');
    }

    public function verify(Penerima $penerima)
    {
        $penerima->update([
            'status' => request('status', 'terverifikasi'),
            'catatan_verifikasi' => request('catatan'),
            'verified_by' => auth()->id(),
            'verified_at' => now(),
        ]);
        return back()->with('success', 'Penerima diverifikasi.');
    }

    public function daftarMandiri(Request $request)
    {
        $data = $request->validate([
            'nik' => 'required|unique:penerimas,nik',
            'nama' => 'required',
            'alamat' => 'required',
            'phone' => 'required',
            'jumlah_keluarga' => 'nullable|integer',
        ]);
        $data['status'] = 'pending';
        $data['sumber_data'] = 'mandiri';
        $data['provinsi'] = 'Aceh';
        $data['kelompok_id'] = 1; // default group

        Penerima::create($data);
        return back()->with('success', 'Pendaftaran berhasil! Data akan diverifikasi.');
    }
}

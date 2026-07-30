<?php
namespace App\Http\Controllers;

use App\Models\Anggaran;
use App\Models\PembelianBarang;
use Illuminate\Http\Request;

class BarangController extends Controller
{
    public function index()
    {
        $anggarans = Anggaran::orderBy('id')->get();
        $pembelian = PembelianBarang::orderBy('id')->get();
        return view('barang.index', compact('anggarans', 'pembelian'));
    }

    // === KEGIATAN ===
    public function storeKegiatan(Request $request)
    {
        $data = $request->validate([
            'nama_anggaran' => 'required',
            'kategori' => 'required',
            'target_paket' => 'nullable|integer',
            'satuan' => 'nullable',
            'anggaran' => 'required|numeric',
            'realisasi' => 'required|numeric',
            'catatan' => 'nullable',
        ]);
        Anggaran::create($data);
        return redirect()->route('barang.index')->with('success', 'Kegiatan berhasil ditambahkan.');
    }

    public function updateKegiatan(Request $request, Anggaran $anggaran)
    {
        $data = $request->validate([
            'nama_anggaran' => 'required',
            'kategori' => 'required',
            'target_paket' => 'nullable|integer',
            'satuan' => 'nullable',
            'anggaran' => 'required|numeric',
            'realisasi' => 'required|numeric',
            'catatan' => 'nullable',
        ]);
        $anggaran->update($data);
        return redirect()->route('barang.index')->with('success', 'Kegiatan diupdate.');
    }

    public function destroyKegiatan(Anggaran $anggaran)
    {
        $anggaran->delete();
        return redirect()->route('barang.index')->with('success', 'Kegiatan dihapus.');
    }

    // === PEMBELIAN BARANG ===
    public function storePembelian(Request $request)
    {
        $data = $request->validate([
            'nama_barang' => 'required',
            'batch' => 'nullable',
            'qty_rencana' => 'required|integer',
            'qty_terbeli' => 'nullable|integer',
            'harga_satuan' => 'required|numeric',
            'anggaran' => 'required|numeric',
            'realisasi' => 'nullable|numeric',
        ]);
        $data['qty_terbeli'] = $data['qty_terbeli'] ?? 0;
        $data['realisasi'] = $data['realisasi'] ?? 0;
        $data['qty_belum'] = $data['qty_rencana'] - $data['qty_terbeli'];
        $data['sisa'] = $data['anggaran'] - $data['realisasi'];
        $data['persen_real'] = $data['anggaran'] > 0 ? round(($data['realisasi']/$data['anggaran'])*100, 1) : 0;
        PembelianBarang::create($data);
        return redirect()->route('barang.index')->with('success', 'Item barang berhasil ditambahkan.');
    }

    public function updatePembelian(Request $request, PembelianBarang $pembelian)
    {
        $data = $request->validate([
            'nama_barang' => 'required',
            'batch' => 'nullable',
            'qty_rencana' => 'required|integer',
            'qty_terbeli' => 'required|integer',
            'harga_satuan' => 'required|numeric',
            'anggaran' => 'required|numeric',
            'realisasi' => 'required|numeric',
        ]);
        $data['qty_belum'] = $data['qty_rencana'] - $data['qty_terbeli'];
        $data['sisa'] = $data['anggaran'] - $data['realisasi'];
        $data['persen_real'] = $data['anggaran'] > 0 ? round(($data['realisasi']/$data['anggaran'])*100, 1) : 0;
        $pembelian->update($data);
        return redirect()->route('barang.index')->with('success', 'Item barang diupdate.');
    }

    public function destroyPembelian(PembelianBarang $pembelian)
    {
        $pembelian->delete();
        return redirect()->route('barang.index')->with('success', 'Item barang dihapus.');
    }
}

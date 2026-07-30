<?php
namespace App\Http\Controllers;

use App\Models\DanaDonatur;
use App\Models\BiayaOperasional;
use App\Models\BarangBantuan;
use App\Models\StokBarang;
use App\Models\Distribusi;
use App\Models\Anggaran;
use Illuminate\Http\Request;

class KeuanganController extends Controller
{
    public function index()
    {
        $total_masuk = DanaDonatur::sum('jumlah');
        $total_biaya = BiayaOperasional::sum('jumlah');
        $total_bantuan = Distribusi::sum('estimasi_nilai_total');
        $sisa = $total_masuk - $total_biaya - $total_bantuan;

        $dana_masuk = DanaDonatur::with('pencatat')->orderBy('tanggal_masuk', 'desc')->get();
        $biaya = BiayaOperasional::with('distribusi', 'pencatat')->orderBy('tanggal', 'desc')->take(20)->get();
        $anggarans = Anggaran::with('distribusi')->get();

        $distribusi_list = Distribusi::whereIn('status', ['direncanakan', 'berlangsung'])->orderBy('tanggal', 'desc')->get();

        return view('keuangan.index', compact('total_masuk', 'total_biaya', 'total_bantuan', 'sisa', 'dana_masuk', 'biaya', 'anggarans', 'distribusi_list'));
    }

    public function storeDana(Request $request)
    {
        $data = $request->validate([
            'donatur' => 'required',
            'tanggal_masuk' => 'required|date',
            'jumlah' => 'required|numeric',
            'jenis' => 'required|in:uang_tunai,transfer,barang',
        ]);
        $data['dicatat_oleh'] = auth()->id();
        DanaDonatur::create($data);
        return back()->with('success', 'Dana donatur dicatat.');
    }

    public function storeBiaya(Request $request)
    {
        $data = $request->validate([
            'kategori' => 'required',
            'deskripsi' => 'required',
            'jumlah' => 'required|numeric',
            'tanggal' => 'required|date',
            'distribusi_id' => 'nullable|exists:distribusis,id',
        ]);
        $data['dicatat_oleh'] = auth()->id();
        BiayaOperasional::create($data);
        return back()->with('success', 'Biaya operasional dicatat.');
    }

    public function storeAnggaran(Request $request)
    {
        $data = $request->validate([
            'kategori' => 'required',
            'anggaran' => 'required|numeric',
            'distribusi_id' => 'nullable|exists:distribusis,id',
        ]);
        $data['realisasi'] = 0;
        Anggaran::create($data);
        return back()->with('success', 'Anggaran dibuat.');
    }

    public function rekap()
    {
        $dana_masuk = DanaDonatur::sum('jumlah');
        $total_biaya = BiayaOperasional::sum('jumlah');
        $total_bantuan = Distribusi::sum('estimasi_nilai_total');
        $sisa = $dana_masuk - $total_biaya - $total_bantuan;

        $per_daerah = Distribusi::selectRaw('kelompoks.daerah, sum(distribusis.jumlah_paket) as total_paket, sum(distribusis.estimasi_nilai_total) as total_nilai')
            ->join('kelompoks', 'kelompoks.id', '=', 'distribusis.kelompok_id')
            ->groupBy('kelompoks.daerah')
            ->get();

        return response()->json(compact('dana_masuk', 'total_biaya', 'total_bantuan', 'sisa', 'per_daerah'));
    }
}

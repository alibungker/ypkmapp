<?php
namespace App\Http\Controllers;

use App\Models\DanaDonatur;
use App\Models\BiayaOperasional;
use App\Models\BarangBantuan;
use App\Models\StokBarang;
use App\Models\Distribusi;
use App\Models\Anggaran;
use App\Models\PembelianBarang;
use Illuminate\Http\Request;

class KeuanganController extends Controller
{
    public function index()
    {
        $total_masuk = DanaDonatur::sum('jumlah');
        $total_biaya = BiayaOperasional::sum('jumlah');
        $total_bantuan = Distribusi::sum('estimasi_nilai_total');
        // Nilai bantuan sudah termasuk dalam biaya operasional (belanja paket),
        // sehingga tidak dikurangkan lagi dari sisa dana.
        $sisa = $total_masuk - $total_biaya;

        $dana_masuk = DanaDonatur::with('pencatat')->orderBy('tanggal_masuk', 'desc')->get();
        $biaya = BiayaOperasional::with('distribusi', 'pencatat')->orderBy('tanggal', 'desc')->take(50)->get();
        $anggarans = Anggaran::with('distribusi')->get();
        $pembelian = PembelianBarang::orderBy('id')->get();
        $total_anggaran_all = $anggarans->sum('anggaran') + $pembelian->sum('anggaran');
        $total_realisasi_all = $anggarans->sum('realisasi') + $pembelian->sum('realisasi');

        // Rekap biaya per batch
        $biayaBatch = BiayaOperasional::selectRaw('COALESCE(batch_kegiatan, "-") as batch, SUM(jumlah) as total, COUNT(*) as jumlah_transaksi')
            ->groupBy('batch')
            ->orderByDesc('total')
            ->get();

        $distribusi_list = Distribusi::orderBy('tanggal', 'desc')->get();

        return view('keuangan.index', compact('total_masuk', 'total_biaya', 'total_bantuan', 'sisa', 'dana_masuk', 'biaya', 'anggarans', 'distribusi_list', 'pembelian', 'total_anggaran_all', 'total_realisasi_all', 'biayaBatch'));
    }

    public function storeDana(Request $request)
    {
        $data = $request->validate([
            'donatur' => 'required',
            'tanggal_masuk' => 'required|date',
            'jumlah' => 'required|numeric',
            'jenis' => 'required|in:uang_tunai,transfer,barang',
            'keterangan' => 'nullable|string|max:500',
        ]);
        $data['dicatat_oleh'] = auth()->id();
        DanaDonatur::create($data);
        return back()->with('success', 'Dana donatur dicatat.');
    }

    public function updateDana(Request $request, $id)
    {
        $dana = DanaDonatur::findOrFail($id);
        $data = $request->validate([
            'donatur' => 'required',
            'tanggal_masuk' => 'required|date',
            'jumlah' => 'required|numeric',
            'jenis' => 'required|in:uang_tunai,transfer,barang',
            'keterangan' => 'nullable',
        ]);
        $dana->update($data);
        return back()->with('success', 'Data dana donatur diperbarui.');
    }

    public function destroyDana($id)
    {
        $dana = DanaDonatur::findOrFail($id);
        $dana->delete();
        return back()->with('success', 'Data dana donatur dihapus.');
    }

    public function storeBiaya(Request $request)
    {
        $data = $request->validate([
            'kategori' => 'required|string|max:100',
            'deskripsi' => 'required',
            'jumlah' => 'required|numeric',
            'tanggal' => 'required|date',
            'distribusi_id' => 'nullable|exists:distribusis,id',
            'batch_kegiatan' => 'nullable|string|max:100',
            'pihak_penerima' => 'nullable|string|max:150',
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

        $per_batch = BiayaOperasional::selectRaw('COALESCE(batch_kegiatan, "-") as batch, SUM(jumlah) as total, COUNT(*) as count')
            ->groupBy('batch')
            ->orderByDesc('total')
            ->get();

        return response()->json(compact('dana_masuk', 'total_biaya', 'total_bantuan', 'sisa', 'per_daerah', 'per_batch'));
    }

    public function rekapDistribusi($id)
    {
        $distribusi = Distribusi::findOrFail($id);
        $biaya = BiayaOperasional::where('distribusi_id', $id)->take(100)->get();
        $total = $biaya->sum('jumlah');
        return response()->json(compact('distribusi', 'biaya', 'total'));
    }

    public function topupIndex()
    {
        $topups = \DB::table('topup_anggarans')->orderByDesc('created_at')->get();
        $anggarans = Anggaran::orderBy('kategori')->get();
        return view('keuangan.topup', compact('topups', 'anggarans'));
    }

    public function topupStore(Request $request)
    {
        abort_unless(auth()->user()->canTopUp(), 403, 'Tidak dapat mengajukan top-up.');
        $data = $request->validate([
            'anggaran_id' => 'nullable|exists:anggarans,id',
            'nominal' => 'required|numeric|min:1',
            'tanggal' => 'required|date',
            'sumber_dana' => 'nullable|max:150',
            'nomor_referensi' => 'nullable|max:100',
            'keterangan' => 'nullable|max:500',
        ]);
        $data['diajukan_oleh'] = auth()->id();
        $data['status'] = 'diajukan';
        \DB::table('topup_anggarans')->insert($data + ['created_at' => now(), 'updated_at' => now()]);
        \DB::table('audit_logs')->insert(['user_id' => auth()->id(), 'aksi' => 'topup.create', 'model' => 'TopupAnggaran', 'detail' => json_encode($data), 'ip' => $request->ip(), 'created_at' => now(), 'updated_at' => now()]);
        return back()->with('success', 'Pengajuan top-up anggaran dibuat.');
    }

    public function topupApprove(Request $request, $id)
    {
        abort_unless(auth()->user()->canApproveTopUp(), 403, 'Tidak dapat menyetujui top-up.');
        $topup = \DB::table('topup_anggarans')->where('id', $id)->where('status', 'diajukan')->first();
        abort_unless($topup, 404, 'Pengajuan tidak ditemukan.');
        $oldAnggaran = $topup->anggaran_id ? Anggaran::find($topup->anggaran_id)->anggaran ?? 0 : 0;
        \DB::transaction(function () use ($topup, $id) {
            \DB::table('topup_anggarans')->where('id', $id)->update(['status' => 'disetujui', 'disetujui_oleh' => auth()->id(), 'updated_at' => now()]);
            if ($topup->anggaran_id) {
                Anggaran::where('id', $topup->anggaran_id)->increment('anggaran', $topup->nominal);
            }
            \DB::table('dana_donaturs')->insert(['donatur' => $topup->sumber_dana ?: 'Top-up Anggaran', 'tanggal_masuk' => $topup->tanggal, 'jumlah' => $topup->nominal, 'jenis' => 'transfer', 'keterangan' => 'Top-up: ' . $topup->keterangan, 'dicatat_oleh' => auth()->id(), 'created_at' => now(), 'updated_at' => now()]);
        });
        \DB::table('audit_logs')->insert(['user_id' => auth()->id(), 'aksi' => 'topup.approve', 'model' => 'TopupAnggaran', 'model_id' => $id, 'detail' => json_encode(['nominal' => $topup->nominal, 'old_anggaran' => $oldAnggaran, 'new_anggaran' => $oldAnggaran + $topup->nominal]), 'ip' => $request->ip(), 'created_at' => now(), 'updated_at' => now()]);
        return back()->with('success', 'Top-up disetujui. Anggaran dan dana masuk diperbarui.');
    }

    public function topupReject(Request $request, $id)
    {
        abort_unless(auth()->user()->canApproveTopUp(), 403, 'Tidak dapat menolak top-up.');
        $data = $request->validate(['alasan_penolakan' => 'required|max:500']);
        \DB::table('topup_anggarans')->where('id', $id)->where('status', 'diajukan')->update(['status' => 'ditolak', 'alasan_penolakan' => $data['alasan_penolakan'], 'updated_at' => now()]);
        \DB::table('audit_logs')->insert(['user_id' => auth()->id(), 'aksi' => 'topup.reject', 'model' => 'TopupAnggaran', 'model_id' => $id, 'detail' => $data['alasan_penolakan'], 'ip' => $request->ip(), 'created_at' => now(), 'updated_at' => now()]);
        return back()->with('success', 'Pengajuan top-up ditolak.');
    }
}

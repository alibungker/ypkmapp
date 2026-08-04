<?php
namespace App\Http\Controllers;

use App\Models\Penerima;
use App\Models\Kelompok;
use App\Models\Distribusi;
use App\Models\DanaDonatur;
use App\Models\BiayaOperasional;
use App\Models\PembelianBarang;
use App\Models\Anggaran;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $stats = [
            'penerima' => 0,
            'penerima_terverifikasi' => 0,
            'penerima_pending' => 0,
            'penerima_ditolak' => 0,
            'penerima_diterima' => 0,
            'penerima_belum_terima' => 0,
            'kelompok' => 0,
            'kelompok_aktif' => 0,
            'distribusi' => 0,
            'distribusi_selesai' => 0,
            'distribusi_berlangsung' => 0,
            'distribusi_rencana' => 0,
            'total_dana_masuk' => 0,
            'total_biaya' => 0,
            'total_nilai_bantuan' => 0,
            'total_paket_target' => 0,
            'total_paket_terkirim' => 0,
            'barang_stok_kritis' => 0,
            'kegiatan_aktif' => 0,
            'kegiatan_lunas' => 0,
            'kabupaten_terjangkau' => 0,
            'kecamatan_terjangkau' => 0,
            'desa_terjangkau' => 0,
        ];

        $role = 'staff';
        $kelompokNama = null;
        $wilayahLabel = null;
        $distribusiTerbaru = collect();

        if ($user->isAdmin() || $user->canViewKeuangan()) {
            $role = $user->isAdmin() ? 'admin' : 'keuangan';

            if ($role === 'keuangan') {
                $biayaSaya = BiayaOperasional::where('dicatat_oleh', $user->id);
                $stats['saldo_topup_saya'] = (float) ($user->saldo_topup ?? 0);
                $stats['total_biaya_saya'] = (clone $biayaSaya)->sum('jumlah');
                $stats['transaksi_saya'] = (clone $biayaSaya)->count();
                $stats['tanpa_bukti_saya'] = (clone $biayaSaya)
                    ->whereNull('bukti_foto')
                    ->whereDoesntHave('buktis')
                    ->count();
                $stats['pemakaian_dana_saya'] = ($stats['saldo_topup_saya'] + $stats['total_biaya_saya']) > 0
                    ? round($stats['total_biaya_saya'] / ($stats['saldo_topup_saya'] + $stats['total_biaya_saya']) * 100, 1)
                    : 0;
                $stats['pengeluaran_terbaru_saya'] = (clone $biayaSaya)
                    ->with('anggaran')
                    ->orderByDesc('tanggal')
                    ->take(5)
                    ->get();
            }

            $stats['penerima'] = Penerima::count();
            $stats['penerima_terverifikasi'] = Penerima::where('status', 'terverifikasi')->count();
            $stats['penerima_pending'] = Penerima::where('status', 'pending')->count();
            $stats['penerima_ditolak'] = Penerima::where('status', 'ditolak')->count();
            $stats['penerima_diterima'] = Penerima::where('terima_bantuan', true)->count();
            $stats['kelompok'] = Kelompok::count();
            $stats['distribusi'] = Distribusi::count();
            $stats['distribusi_selesai'] = Distribusi::where('status', 'selesai')->count();
            $stats['distribusi_berlangsung'] = Distribusi::where('status', 'berlangsung')->count();
            $stats['total_dana_masuk'] = DanaDonatur::sum('jumlah');
            $stats['total_biaya'] = BiayaOperasional::sum('jumlah');
            $stats['total_nilai_bantuan'] = Distribusi::sum('estimasi_nilai_total');
            $stats['total_paket_target'] = Distribusi::sum('jumlah_paket');
            $stats['total_paket_terkirim'] = Distribusi::where('status', 'selesai')->sum('jumlah_paket');
            $stats['sisa_dana'] = $stats['total_dana_masuk'] - $stats['total_biaya'];
            $stats['penerima_belum_terima'] = Penerima::where('terima_bantuan', false)->count();
            $stats['distribusi_rencana'] = Distribusi::where('status', 'direncanakan')->count();
            $stats['kabupaten_terjangkau'] = Penerima::whereNotNull('kabupaten')->distinct()->count('kabupaten');
            $stats['kecamatan_terjangkau'] = Penerima::whereNotNull('kecamatan')->distinct()->count('kecamatan');
            $stats['desa_terjangkau'] = Penerima::whereNotNull('desa')->distinct()->count('desa');
            $stats['kelompok_aktif'] = Kelompok::has('penerima')->count();
            $stats['kegiatan_aktif'] = Anggaran::where('realisasi', 0)->count();
            $stats['kegiatan_lunas'] = Anggaran::whereColumn('realisasi', '>=', 'anggaran')->count();
            $stats['barang_stok_kritis'] = DB::table('pembelian_barang as pb')
                ->selectRaw('pb.id, pb.nama_barang, pb.qty_terbeli - COALESCE(k.total,0) as sisa')
                ->leftJoinSub(
                    DB::table('kegiatan_barang')->selectRaw('pembelian_barang_id, SUM(jumlah) as total')->groupBy('pembelian_barang_id'),
                    'k', 'k.pembelian_barang_id', '=', 'pb.id'
                )->havingRaw('sisa <= 0')->get()->count();
            $stats['biaya_batch'] = BiayaOperasional::selectRaw('COALESCE(batch_kegiatan, "-") as batch, SUM(jumlah) as total, COUNT(*) as count')
                ->groupBy('batch')->orderByDesc('total')->take(6)->get();

            $distribusiTerbaru = Distribusi::with('kelompok')->orderBy('tanggal', 'desc')->take(5)->get();

        } elseif ($user->isKetuaKelompok()) {
            $role = 'ketua';
            $kelompokId = $user->kelompok_id;
            $kelompok = Kelompok::find($kelompokId);
            $kelompokNama = $kelompok?->nama;

            $stats['penerima'] = Penerima::where('kelompok_id', $kelompokId)->count();
            $stats['penerima_terverifikasi'] = Penerima::where('kelompok_id', $kelompokId)->where('status', 'terverifikasi')->count();
            $stats['penerima_pending'] = Penerima::where('kelompok_id', $kelompokId)->where('status', 'pending')->count();
            $stats['penerima_ditolak'] = Penerima::where('kelompok_id', $kelompokId)->where('status', 'ditolak')->count();
            $stats['penerima_diterima'] = Penerima::where('kelompok_id', $kelompokId)->where('terima_bantuan', true)->count();
            $stats['kelompok'] = 1;
            $stats['distribusi'] = Distribusi::where('kelompok_id', $kelompokId)->count();
            $stats['distribusi_selesai'] = Distribusi::where('kelompok_id', $kelompokId)->where('status', 'selesai')->count();
            $stats['distribusi_berlangsung'] = Distribusi::where('kelompok_id', $kelompokId)->where('status', 'berlangsung')->count();
            $stats['total_nilai_bantuan'] = Distribusi::where('kelompok_id', $kelompokId)->sum('estimasi_nilai_total');

            $distribusiTerbaru = Distribusi::with('kelompok')
                ->where('kelompok_id', $kelompokId)
                ->orderBy('tanggal', 'desc')->take(5)->get();

        } elseif ($user->isRelawan()) {
            $role = 'relawan';
            $wilayahLabel = trim(($user->wilayah_desa ?: $user->wilayah_kecamatan ?: $user->wilayah_kabupaten ?: '') . '');

            // Scope penerima by wilayah
            $pQuery = Penerima::query();
            if ($user->wilayah_kabupaten) $pQuery->where('kabupaten', $user->wilayah_kabupaten);
            if ($user->wilayah_kecamatan) $pQuery->where('kecamatan', $user->wilayah_kecamatan);
            if ($user->wilayah_desa) $pQuery->where('desa', $user->wilayah_desa);

            $stats['penerima'] = (clone $pQuery)->count();
            $stats['penerima_terverifikasi'] = (clone $pQuery)->where('status', 'terverifikasi')->count();
            $stats['penerima_pending'] = (clone $pQuery)->where('status', 'pending')->count();
            $stats['penerima_ditolak'] = (clone $pQuery)->where('status', 'ditolak')->count();
            $stats['penerima_diterima'] = (clone $pQuery)->where('terima_bantuan', true)->count();

            // Scope kelompok by wilayah (kolom: daerah=kabupaten, kecamatan, desa)
            $kQuery = Kelompok::query();
            if ($user->wilayah_kabupaten) $kQuery->where('daerah', $user->wilayah_kabupaten);
            if ($user->wilayah_kecamatan) $kQuery->where('kecamatan', $user->wilayah_kecamatan);
            if ($user->wilayah_desa) $kQuery->where('desa', $user->wilayah_desa);
            $stats['kelompok'] = $kQuery->count();

            // Scope distribusi via kelompok_id dari kelompok di wilayah ini
            $kelompokIds = $kQuery->pluck('id');
            $dQuery = Distribusi::with('kelompok')->whereIn('kelompok_id', $kelompokIds);
            $stats['distribusi'] = (clone $dQuery)->count();
            $stats['distribusi_selesai'] = Distribusi::whereIn('kelompok_id', $kelompokIds)->where('status', 'selesai')->count();
            $stats['distribusi_berlangsung'] = Distribusi::whereIn('kelompok_id', $kelompokIds)->where('status', 'berlangsung')->count();
            $stats['total_nilai_bantuan'] = Distribusi::whereIn('kelompok_id', $kelompokIds)->sum('estimasi_nilai_total');

            $distribusiTerbaru = $dQuery->orderBy('tanggal', 'desc')->take(5)->get();
        }

        return view('dashboard.index', compact('stats', 'distribusiTerbaru', 'role', 'kelompokNama', 'wilayahLabel'));
    }
}

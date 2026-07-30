<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PenerimaController;
use App\Http\Controllers\KelompokController;
use App\Http\Controllers\DistribusiController;
use App\Http\Controllers\KeuanganController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RelawanController;
use App\Models\Distribusi;

// ============================================================
// PUBLIK (tanpa login)
// ============================================================
Route::get('daftar', [PenerimaController::class, 'formDaftar'])->name('penerima.daftar.form');
Route::post('daftar', [PenerimaController::class, 'daftarMandiri'])->name('penerima.daftar');

// API Wilayah (publik — dipakai form registrasi mandiri juga)
Route::get('api/wilayah', function () {
    return response()->json(
        DB::table('wilayah_boundaries')
            ->where('kode', 'LIKE', '11.%')
            ->orWhere('kode', '11')
            ->orderBy('kode')
            ->get()
    );
});
Route::get('api/wilayah/kabupaten', function () {
    return response()->json(
        DB::table('wilayah')->whereRaw("kode LIKE '11.%' AND LENGTH(kode)=5")->orderBy('nama')->get(['kode', 'nama'])
    );
});
Route::get('api/wilayah/kecamatan/{kab}', function ($kab) {
    return response()->json(
        DB::table('wilayah')->whereRaw("kode LIKE ? AND LENGTH(kode)=8", [$kab . '.%'])->orderBy('nama')->get(['kode', 'nama'])
    );
});
Route::get('api/wilayah/desa/{kec}', function ($kec) {
    return response()->json(
        DB::table('wilayah')->whereRaw("kode LIKE ? AND LENGTH(kode)=13", [$kec . '.%'])->orderBy('nama')->get(['kode', 'nama'])
    );
});

// ============================================================
// SEMUA ROLE (login wajib): admin, relawan, ketua_kelompok
// ============================================================
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Penerima — semua role bisa lihat & input; verifikasi utk admin/relawan
    Route::resource('penerima', PenerimaController::class);
    Route::post('penerima/{penerima}/verify', [PenerimaController::class, 'verify'])->name('penerima.verify');
    Route::post('penerima/{penerima}/terima-bantuan', [PenerimaController::class, 'terimaBantuan'])->name('penerima.terima-bantuan');

    // Kelompok
    Route::resource('kelompok', KelompokController::class);
    Route::get('kelompok/{kelompok}/anggota', [KelompokController::class, 'anggota'])->name('kelompok.anggota');

    // Distribusi
    Route::resource('distribusi', DistribusiController::class);
    Route::post('distribusi/{distribusi}/terima/{penerima}', [DistribusiController::class, 'terima'])->name('distribusi.terima');
    Route::post('distribusi/{distribusi}/selesai', [DistribusiController::class, 'selesai'])->name('distribusi.selesai');
    Route::get('api/distribusi/peta', [DistribusiController::class, 'dataPeta'])->name('distribusi.peta.data');

    // Peta
    Route::get('peta', function () {
        $distribusi = Distribusi::whereNotNull('titik_koordinat')
            ->with('kelompok.ketua')
            ->get()
            ->map(function ($d) {
                $coord = explode(',', $d->titik_koordinat);
                return [
                    'name' => $d->nama_kegiatan,
                    'lat' => (float)($coord[0] ?? 0),
                    'lng' => (float)($coord[1] ?? 0),
                    'paket' => $d->jumlah_paket,
                    'nilai' => 'Rp ' . number_format($d->estimasi_nilai_total,0,',','.'),
                    'penerima' => $d->kelompok->jumlah_anggota ?? 0,
                    'kelompok' => $d->kelompok->nama ?? '-',
                    'ketua' => $d->kelompok->ketua->nama ?? '-',
                    'daerah' => $d->kelompok->daerah ?? '',
                    'status' => $d->status,
                    'tgl' => is_object($d->tanggal) ? $d->tanggal->format('d M Y') : date('d M Y', strtotime($d->tanggal)),
                ];
            });
        return view('peta.index', compact('distribusi'));
    })->name('peta.index');

    // ============================================================
    // KHUSUS RELAWAN: Verifikasi & Validasi
    // ============================================================
    Route::prefix('relawan')->name('relawan.')->middleware('auth')->group(function () {
        Route::get('/', [RelawanController::class, 'verifikasi'])->name('verifikasi');
    });

    // ============================================================
    // KHUSUS ADMIN: User Management, Keuangan & Laporan
    // ============================================================
    Route::middleware(\App\Http\Middleware\AdminOnly::class)->group(function () {
        // User Management (Ketua Kelompok & Relawan)
        Route::resource('users', UserController::class);

        // Manajemen Barang & Kegiatan (Batch 1-3 dst)
        Route::get('barang', [\App\Http\Controllers\BarangController::class, 'index'])->name('barang.index');
        Route::post('barang/kegiatan', [\App\Http\Controllers\BarangController::class, 'storeKegiatan'])->name('barang.kegiatan.store');
        Route::get('barang/kegiatan/{anggaran}/edit', function (\App\Models\Anggaran $anggaran) {
            $a = $anggaran;
            return view('barang.edit-kegiatan', compact('a'));
        })->name('barang.kegiatan.edit');
        Route::put('barang/kegiatan/{anggaran}', [\App\Http\Controllers\BarangController::class, 'updateKegiatan'])->name('barang.kegiatan.update');
        Route::delete('barang/kegiatan/{anggaran}', [\App\Http\Controllers\BarangController::class, 'destroyKegiatan'])->name('barang.kegiatan.destroy');
        Route::post('barang/pembelian', [\App\Http\Controllers\BarangController::class, 'storePembelian'])->name('barang.pembelian.store');
        Route::get('barang/pembelian/{pembelian}/edit', function (\App\Models\PembelianBarang $pembelian) {
            $p = $pembelian;
            return view('barang.edit-pembelian', compact('p'));
        })->name('barang.pembelian.edit');
        Route::put('barang/pembelian/{pembelian}', [\App\Http\Controllers\BarangController::class, 'updatePembelian'])->name('barang.pembelian.update');
        Route::delete('barang/pembelian/{pembelian}', [\App\Http\Controllers\BarangController::class, 'destroyPembelian'])->name('barang.pembelian.destroy');

        Route::prefix('keuangan')->name('keuangan.')->group(function () {
            Route::get('/', [KeuanganController::class, 'index'])->name('index');
            Route::post('dana', [KeuanganController::class, 'storeDana'])->name('dana.store');
            Route::post('dana/{id}/update', [KeuanganController::class, 'updateDana'])->name('dana.update');
            Route::post('dana/{id}/delete', [KeuanganController::class, 'destroyDana'])->name('dana.delete');
            Route::post('biaya', [KeuanganController::class, 'storeBiaya'])->name('biaya.store');
            Route::post('anggaran', [KeuanganController::class, 'storeAnggaran'])->name('anggaran.store');
            Route::get('rekap', [KeuanganController::class, 'rekap'])->name('rekap');
        });

        Route::get('laporan', function () { return view('laporan.index'); })->name('laporan.index');
    });
});

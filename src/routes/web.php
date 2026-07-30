<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PenerimaController;
use App\Http\Controllers\KelompokController;
use App\Http\Controllers\DistribusiController;
use App\Http\Controllers\KeuanganController;
use App\Http\Controllers;

// Dashboard
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Penerima
Route::resource('penerima', PenerimaController::class);
Route::post('penerima/{penerima}/verify', [PenerimaController::class, 'verify'])->name('penerima.verify');
Route::post('daftar', [PenerimaController::class, 'daftarMandiri'])->name('penerima.daftar');

// Kelompok
Route::resource('kelompok', KelompokController::class);
Route::get('kelompok/{kelompok}/anggota', [KelompokController::class, 'anggota'])->name('kelompok.anggota');

// Distribusi
Route::resource('distribusi', DistribusiController::class);
Route::post('distribusi/{distribusi}/terima/{penerima}', [DistribusiController::class, 'terima'])->name('distribusi.terima');
Route::post('distribusi/{distribusi}/selesai', [DistribusiController::class, 'selesai'])->name('distribusi.selesai');
Route::get('api/distribusi/peta', [DistribusiController::class, 'dataPeta'])->name('distribusi.peta.data');

// Keuangan
Route::prefix('keuangan')->name('keuangan.')->group(function () {
    Route::get('/', [KeuanganController::class, 'index'])->name('index');
    Route::post('dana', [KeuanganController::class, 'storeDana'])->name('dana.store');
    Route::post('biaya', [KeuanganController::class, 'storeBiaya'])->name('biaya.store');
    Route::post('anggaran', [KeuanganController::class, 'storeAnggaran'])->name('anggaran.store');
    Route::get('rekap', [KeuanganController::class, 'rekap'])->name('rekap');
});

// Barang
// Route::resource('barang', BarangController::class)->only(['index', 'store']);

// Peta
Route::view('peta', 'peta.index')->name('peta.index');

// Laporan
Route::view('laporan', 'laporan.index')->name('laporan.index');

<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CrmController;
use App\Http\Controllers\PenerimaController;
use App\Http\Controllers\KelompokController;
use App\Http\Controllers\DistribusiController;
use App\Http\Controllers\KeuanganController;
use App\Http\Controllers\LaporanController;
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
    Route::get('penerima-import/template', [PenerimaController::class, 'importTemplate'])->name('penerima.import.template');
    Route::post('penerima-import/preview', [PenerimaController::class, 'importPreview'])->name('penerima.import.preview');
    Route::post('penerima-import/store', [PenerimaController::class, 'importStore'])->name('penerima.import.store');
    Route::post('penerima-import/cancel', [PenerimaController::class, 'importCancel'])->name('penerima.import.cancel');
    Route::resource('penerima', PenerimaController::class);
    Route::middleware(\App\Http\Middleware\OperationalOnly::class)->group(function () {
        Route::post('penerima/{penerima}/verify', [PenerimaController::class, 'verify'])->name('penerima.verify');
        Route::post('penerima/{penerima}/terima-bantuan', [PenerimaController::class, 'terimaBantuan'])->name('penerima.terima-bantuan');
    });

    // Kelompok — semua role dapat melihat; hanya Admin yang dapat mengubah
    Route::resource('kelompok', KelompokController::class)->only(['index', 'show']);
    Route::get('kelompok/{kelompok}/anggota', [KelompokController::class, 'anggota'])->name('kelompok.anggota');

    // Distribusi — semua role dapat melihat; pengelolaan dibatasi di bawah
    Route::resource('distribusi', DistribusiController::class)->only(['index', 'show'])->whereNumber('distribusi');
    Route::middleware(\App\Http\Middleware\OperationalOnly::class)->group(function () {
        Route::post('distribusi/{distribusi}/terima/{penerima}', [DistribusiController::class, 'terima'])->name('distribusi.terima');
        Route::post('distribusi/{distribusi}/selesai', [DistribusiController::class, 'selesai'])->name('distribusi.selesai');
    });
    Route::get('api/distribusi/peta', [DistribusiController::class, 'dataPeta'])->name('distribusi.peta.data');

    // Peta
    Route::get('peta', function () {
        $query = Distribusi::whereNotNull('titik_koordinat')
            ->with(['kelompok' => fn ($q) => $q->withCount('penerima')->with('ketuaUser')]);
        $user = auth()->user();
        if ($user->isKetuaKelompok()) {
            $query->where('kelompok_id', $user->kelompok_id);
        } elseif ($user->isRelawan()) {
            $query->whereHas('kelompok', function ($q) use ($user) {
                if ($user->wilayah_kabupaten) $q->where('daerah', $user->wilayah_kabupaten);
                if ($user->wilayah_kecamatan) $q->where('kecamatan', $user->wilayah_kecamatan);
                if ($user->wilayah_desa) $q->where('desa', $user->wilayah_desa);
            });
        }
        $distribusi = $query->get()
            ->map(function ($d) {
                $coord = explode(',', $d->titik_koordinat);
                return [
                    'id' => $d->id,
                    'name' => $d->nama_kegiatan,
                    'lat' => (float)($coord[0] ?? 0),
                    'lng' => (float)($coord[1] ?? 0),
                    'paket' => (int) $d->jumlah_paket,
                    'nilai_raw' => (float) $d->estimasi_nilai_total,
                    'nilai' => 'Rp ' . number_format($d->estimasi_nilai_total,0,',','.'),
                    'penerima' => (int) ($d->kelompok->penerima_count ?? 0),
                    'kelompok' => $d->kelompok->nama ?? '-',
                    'ketua' => $d->kelompok->ketuaUser->name ?? '-',
                    'daerah' => $d->kelompok->daerah ?? '',
                    'kecamatan' => $d->kelompok->kecamatan ?? '',
                    'desa' => $d->kelompok->desa ?? '',
                    'lokasi' => $d->lokasi,
                    'status' => $d->status,
                    'tgl' => is_object($d->tanggal) ? $d->tanggal->format('d M Y') : date('d M Y', strtotime($d->tanggal)),
                    'url' => route('distribusi.show', $d),
                ];
            });

        $statusCounts = $distribusi->countBy('status');
        $perDaerah = $distribusi->groupBy('daerah')->map(function ($rows, $daerah) {
            return [
                'daerah' => $daerah ?: 'Tanpa wilayah',
                'paket' => $rows->sum('paket'),
                'nilai' => $rows->sum('nilai_raw'),
                'penerima' => $rows->sum('penerima'),
                'distribusi' => $rows->count(),
            ];
        })->sortByDesc('paket')->values();

        $daerahNames = $distribusi->pluck('daerah')->filter()->unique();
        $boundaryNames = $daerahNames->flatMap(fn ($nama) => [$nama, 'Kabupaten '.$nama, 'Kota '.$nama])->unique();
        $polygons = DB::table('wilayah_boundaries')
            ->whereIn('nama', $boundaryNames)
            ->get(['kode', 'nama', 'path'])
            ->map(fn ($row) => [
                'kode' => $row->kode,
                'nama' => $row->nama,
                'path' => json_decode($row->path, true),
            ])
            ->filter(fn ($row) => is_array($row['path']))
            ->values();

        $wilayahStats = [
            'kabupaten' => $distribusi->pluck('daerah')->filter()->unique()->count(),
            'kecamatan' => $distribusi->pluck('kecamatan')->filter()->unique()->count(),
            'desa' => $distribusi->pluck('desa')->filter()->unique()->count(),
        ];

        return view('peta.index', compact('distribusi', 'statusCounts', 'perDaerah', 'polygons', 'wilayahStats'));
    })->name('peta.index');

    // ============================================================
    // KHUSUS RELAWAN: Verifikasi & Validasi
    // ============================================================
    Route::prefix('relawan')->name('relawan.')->middleware(\App\Http\Middleware\OperationalOnly::class)->group(function () {
        Route::get('/', [RelawanController::class, 'verifikasi'])->name('verifikasi');
        Route::post('bulk-verify', [RelawanController::class, 'bulkVerify'])->name('bulk-verify');
        Route::post('bulk-terima', [RelawanController::class, 'bulkTerima'])->name('bulk-terima');
    });

    // ============================================================
    // KHUSUS ADMIN: User Management, Keuangan & Laporan
    // ============================================================
    Route::middleware(\App\Http\Middleware\AdminOnly::class)->group(function () {
        // Mutasi Kelompok hanya Admin
        Route::post('kelompok', [KelompokController::class, 'store'])->name('kelompok.store');
        Route::put('kelompok/{kelompok}', [KelompokController::class, 'update'])->name('kelompok.update');
        Route::patch('kelompok/{kelompok}', [KelompokController::class, 'update']);
        Route::delete('kelompok/{kelompok}', [KelompokController::class, 'destroy'])->name('kelompok.destroy');

        // Pengelolaan Distribusi hanya Admin
        Route::get('distribusi/create', [DistribusiController::class, 'create'])->name('distribusi.create');
        Route::post('distribusi', [DistribusiController::class, 'store'])->name('distribusi.store');
        Route::get('distribusi/{distribusi}/edit', [DistribusiController::class, 'edit'])->name('distribusi.edit');
        Route::put('distribusi/{distribusi}', [DistribusiController::class, 'update'])->name('distribusi.update');
        Route::patch('distribusi/{distribusi}', [DistribusiController::class, 'update']);
        Route::delete('distribusi/{distribusi}', [DistribusiController::class, 'destroy'])->name('distribusi.destroy');

        // User Management (Ketua Kelompok & Relawan)
        Route::resource('users', UserController::class);

        // Manajemen Barang & Kegiatan (Batch 1-3 dst)
        Route::get('barang', [\App\Http\Controllers\BarangController::class, 'index'])->name('barang.index');
        Route::post('barang/kegiatan', [\App\Http\Controllers\BarangController::class, 'storeKegiatan'])->name('barang.kegiatan.store');
        Route::get('barang/kegiatan/{anggaran}/edit', function (\App\Models\Anggaran $anggaran) {
            $anggaran->load('barangItems');
            $pembelian = \App\Models\PembelianBarang::orderBy('id')->get();
            $keKegiatan = DB::table('kegiatan_barang')
                ->selectRaw('pembelian_barang_id, SUM(jumlah) as total')
                ->groupBy('pembelian_barang_id')->pluck('total', 'pembelian_barang_id');
            $keDistribusi = DB::table('distribusi_pembelian_barang')
                ->selectRaw('pembelian_barang_id, SUM(jumlah) as total')
                ->groupBy('pembelian_barang_id')->pluck('total', 'pembelian_barang_id');
            foreach ($pembelian as $item) {
                // Distribusi mengambil stok yang sudah dialokasikan ke kegiatan,
                // sehingga tidak boleh dikurangi lagi dari stok bebas.
                $item->stok_tersedia = max(0, $item->qty_terbeli - (int)($keKegiatan[$item->id] ?? 0));
            }
            return view('barang.edit-kegiatan', compact('anggaran', 'pembelian'));
        })->name('barang.kegiatan.edit');
        Route::put('barang/kegiatan/{anggaran}', [\App\Http\Controllers\BarangController::class, 'updateKegiatan'])->name('barang.kegiatan.update');
        Route::delete('barang/kegiatan/{anggaran}', [\App\Http\Controllers\BarangController::class, 'destroyKegiatan'])->name('barang.kegiatan.destroy');
        Route::post('barang/pembelian', [\App\Http\Controllers\BarangController::class, 'storePembelian'])->name('barang.pembelian.store');
        Route::get('barang/pembelian/{pembelian}/edit', function (\App\Models\PembelianBarang $pembelian) {
            $p = $pembelian;
            $kategoriBarangs = \App\Models\KategoriBarang::aktif()->get();
            return view('barang.edit-pembelian', compact('p', 'kategoriBarangs'));
        })->name('barang.pembelian.edit');
        Route::put('barang/pembelian/{pembelian}', [\App\Http\Controllers\BarangController::class, 'updatePembelian'])->name('barang.pembelian.update');
        Route::delete('barang/pembelian/{pembelian}', [\App\Http\Controllers\BarangController::class, 'destroyPembelian'])->name('barang.pembelian.destroy');

    }); // akhir AdminOnly operasional

    Route::prefix('keuangan')->name('keuangan.')->middleware(\App\Http\Middleware\FinanceAccess::class)->group(function () {
            Route::get('/', [KeuanganController::class, 'index'])->name('index')->middleware(\App\Http\Middleware\FinanceAccess::class);
            Route::post('dana', [KeuanganController::class, 'storeDana'])->name('dana.store')->middleware(\App\Http\Middleware\FinanceAccess::class);
            Route::post('dana/{id}/update', [KeuanganController::class, 'updateDana'])->name('dana.update')->middleware(\App\Http\Middleware\FinanceAccess::class);
            Route::post('dana/{id}/delete', [KeuanganController::class, 'destroyDana'])->name('dana.delete')->middleware(\App\Http\Middleware\FinanceAccess::class);
            Route::post('biaya', [KeuanganController::class, 'storeBiaya'])->name('biaya.store')->middleware(\App\Http\Middleware\FinanceAccess::class);
            Route::post('anggaran', [KeuanganController::class, 'storeAnggaran'])->name('anggaran.store')->middleware(\App\Http\Middleware\FinanceAccess::class);
            Route::get('rekap', [KeuanganController::class, 'rekap'])->name('rekap')->middleware(\App\Http\Middleware\FinanceAccess::class);
            Route::get('topup', [KeuanganController::class, 'topupIndex'])->name('topup.index')->middleware(\App\Http\Middleware\FinanceAccess::class);
            Route::post('topup', [KeuanganController::class, 'topupStore'])->name('topup.store')->middleware(\App\Http\Middleware\FinanceAccess::class);
            Route::post('topup/{topup}/approve', [KeuanganController::class, 'topupApprove'])->name('topup.approve')->middleware(\App\Http\Middleware\AdminOnly::class);
            Route::post('topup/{topup}/reject', [KeuanganController::class, 'topupReject'])->name('topup.reject')->middleware(\App\Http\Middleware\AdminOnly::class);
        });

        // Profil mandiri (semua role)
        Route::get('profil', [UserController::class, 'profile'])->name('users.profile');
        Route::put('profil', [UserController::class, 'updateProfile'])->name('users.profile.update');

        Route::get('laporan', [LaporanController::class, 'index'])->name('laporan.index')->middleware(\App\Http\Middleware\FinanceAccess::class);
        Route::get('laporan/export-csv', [LaporanController::class, 'exportCsv'])->name('laporan.export-csv')->middleware(\App\Http\Middleware\FinanceAccess::class);

    Route::middleware(\App\Http\Middleware\AdminOnly::class)->group(function () {
        Route::get('crm', [CrmController::class, 'index'])->name('crm.index');
        Route::get('crm/{type}/create', [CrmController::class, 'create'])->name('crm.create');
        Route::post('crm/{type}', [CrmController::class, 'store'])->name('crm.store');
        Route::get('crm/{type}/{id}/edit', [CrmController::class, 'edit'])->name('crm.edit')->whereNumber('id');
        Route::put('crm/{type}/{id}', [CrmController::class, 'update'])->name('crm.update')->whereNumber('id');
        Route::delete('crm/{type}/{id}', [CrmController::class, 'destroy'])->name('crm.destroy')->whereNumber('id');
        Route::patch('crm/{type}/{id}', [CrmController::class, 'update'])->whereNumber('id');
    });
});

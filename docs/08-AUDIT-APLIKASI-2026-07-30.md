# Audit Aplikasi PEDULI YPKM

**Tanggal audit:** 30 Juli 2026  
**Produksi:** https://peduli.ypkm.info  
**Repository:** https://github.com/alibungker/ypkmapp  
**Commit yang diaudit:** `f5fe8e8d1f0ec9f4c32bccf1775acc33c9fd2a05`

## 1. Ringkasan Eksekutif

Source custom lokal, GitHub, dan server produksi berada pada commit yang sama. Seluruh file custom yang disalin oleh `deploy.sh` juga memiliki checksum yang sama dengan file runtime. Namun, aplikasi belum dapat dianggap siap produksi penuh karena masih terdapat satu halaman 500, celah otorisasi antar-role, inkonsistensi data kelompok/ketua, data statis pada peta dan laporan, serta deployment yang belum reproducible dari GitHub saja.

## 2. Hasil Sinkronisasi

- Local HEAD: sama dengan `origin/main`.
- Server HEAD: sama dengan `origin/main`.
- Branch: `main`.
- Models, controllers, middleware, views, migrations, dan public image: checksum source `src/` sama dengan runtime Laravel.
- Server working tree tetap memiliki hampir seluruh scaffold Laravel sebagai file untracked karena repository hanya menyimpan source custom di `src/`.

**Kesimpulan:** source custom sinkron, tetapi struktur deployment berisiko drift dan tidak reproducible penuh.

## 3. Kondisi Produksi

- Laravel: 13.23.0.
- PHP CLI: 8.3.30.
- Environment: production; debug OFF.
- Semua 12 migration custom tercatat `Ran`.
- Master wilayah: 6.810 record.
- Batas wilayah: 28 record.
- User: 4 (1 admin, 1 relawan, 2 ketua kelompok).
- Penerima: 342.
- Kelompok: 2.
- Distribusi: 1.
- Dana donatur: 11.
- Anggaran: 7.
- Pembelian barang: 8.

## 4. Smoke Test Terautentikasi

Pengujian GET dilakukan memakai feature test sementara terhadap database produksi tanpa melakukan perubahan data.

### Admin — berhasil HTTP 200

- Dashboard
- Daftar/tambah/detail/edit penerima
- Daftar kelompok dan API anggota
- Daftar/tambah/detail/edit distribusi
- Peta
- Users dan tambah user
- Keuangan
- Barang & Kegiatan
- Laporan
- API peta distribusi

### Gagal

- `GET /kelompok/1` menghasilkan HTTP 500 karena controller memanggil view `kelompok.show`, tetapi view tersebut tidak tersedia.

### Batas akses Ketua Kelompok

- `/users`, `/keuangan`, `/barang`: benar, HTTP 403.
- `/relawan`: salah, masih HTTP 200.
- CRUD kelompok dan distribusi berada di grup `auth` umum, sehingga bukan hanya admin/relawan yang dapat mengakses aksi mutasinya.

## 5. Temuan Prioritas

### P0 — Harus diperbaiki segera

1. **Otorisasi belum aman.** Route resource Penerima, Kelompok, dan Distribusi hanya memakai `auth`. Policy/gate per aksi belum tersedia. Ketua kelompok dapat membuka halaman Relawan dan secara route dapat mengakses aksi yang seharusnya bukan haknya.
2. **Halaman detail kelompok 500.** View `resources/views/kelompok/show.blade.php` belum ada, tetapi route dan controller aktif.
3. **Relasi ketua kelompok ganda dan tidak konsisten.** `users.kelompok_id` menghubungkan akun ketua ke kelompok, sedangkan `kelompoks.ketua_id` mengarah ke tabel penerima. Akibatnya tabel Kelompok menampilkan ketua `-` meskipun akun ketua sudah terhubung.
4. **Jumlah anggota Acut_Lancok salah.** Nilai tersimpan `jumlah_anggota=0`, tetapi jumlah aktual relasi penerima adalah 100. Peta mengambil nilai tersimpan sehingga statistik/popup dapat salah.

### P1 — Tinggi

5. **Peta hanya sebagian dinamis.** Marker mengambil data database, tetapi tabel, chart, legend, dan jangkauan wilayah masih berisi sample statis. Polygon batas wilayah belum dirender dengan `L.geoJSON`.
6. **Laporan sepenuhnya statis.** Data daerah dan keuangan hard-coded; tombol Export Excel/Cetak belum memiliki aksi.
7. **Distribusi dapat gagal ketika nilai opsional kosong.** Controller mengizinkan `estimasi_nilai_total` dan `sumber_dana` null, sedangkan schema produksi pernah menolak null. Log mencatat dua kegagalan insert.
8. **Upload file belum andal.** Log mencatat PHP `fileinfo` tidak aktif sehingga deteksi MIME gagal. Ini dapat memengaruhi foto user/KTP/bukti.
9. **Data master wilayah tidak reproducible.** Tabel `wilayah` dan `wilayah_boundaries` berisi data produksi, tetapi migration/seeder/import command untuk 6.810 record tidak tersedia di repository.
10. **Deployment tidak reproducible.** GitHub tidak menyimpan project Laravel penuh, `composer.json`, `composer.lock`, tests, atau konfigurasi runtime utama. Hampir seluruh root Laravel di server berstatus untracked.

### P2 — Menengah

11. **Filter Kecamatan dan Desa masih input teks**, belum cascading dropdown seperti form tambah/edit.
12. **Model domain digabung dalam `Models.php`.** Banyak class Eloquent dalam satu file tidak mengikuti struktur PSR-4 standar dan bergantung pada classmap/autoload produksi.
13. **Keamanan filesystem.** `storage` dan `bootstrap/cache` menggunakan mode 777; `.env` mode 644. Permission perlu dibatasi dan ownership distandardisasi.
14. **Data pribadi tersimpan plaintext.** NIK, nomor HP, dan alamat tidak memakai encrypted cast atau masking tampilan yang konsisten.
15. **Tidak ada automated test dalam GitHub.** Test plan menyebut PHPUnit/Dusk, tetapi file test aplikasi tidak tersedia.
16. **Dokumentasi tertinggal.** Dokumen terakhir diperbarui sebelum fitur user-kelompok, master wilayah, relawan, barang, anggaran, modal kelompok, dan filter terbaru. Beberapa klaim belum benar: import Excel, export Excel, PWA/offline, dashboard donatur, notifikasi WhatsApp, dan laporan real-time.
17. **README deployment tidak konsisten.** Contoh path `require` berbeda dengan `deploy.sh`, dan rekomendasi `chmod -R 777` terlalu permisif.

## 6. Konsistensi Data

- Tidak ada penerima tanpa kelompok.
- Tidak ada grup NIK duplikat.
- Juar - Sekerak: 242 anggota tersimpan dan 242 aktual.
- Acut_Lancok: 0 anggota tersimpan, 100 aktual.
- Kedua akun ketua sudah memiliki `kelompok_id`, tetapi `kelompoks.ketua_id` masih null.
- Distribusi aktif: 250 paket untuk kelompok dengan 242 anggota; perlu keputusan bisnis apakah selisih 8 paket adalah cadangan atau inkonsistensi.

## 7. Rekomendasi Urutan Perbaikan

1. Terapkan Policy/Gate per role dan per kelompok/wilayah.
2. Perbaiki detail kelompok 500 dan satukan model relasi ketua kelompok.
3. Hitung jumlah anggota dari relasi (`withCount`) atau sinkronkan otomatis; jangan mengandalkan kolom manual.
4. Jadikan seluruh peta dan laporan berbasis database; tambahkan polygon wilayah.
5. Samakan validasi Distribusi dengan schema database dan aktifkan PHP `fileinfo`.
6. Tambahkan migration/seeder/import command master wilayah.
7. Tambahkan feature test untuk auth, role, scope wilayah, CRUD, filter, dan halaman utama.
8. Migrasikan repository menjadi Laravel penuh atau buat build/deploy artifact yang deterministic.
9. Perbarui PRD, SRS, arsitektur, deployment guide, test plan, database design, dan API spec sesuai implementasi aktual.
10. Rotasi seluruh kredensial yang pernah dibagikan dan batasi permission file produksi.

## 8. Status Akhir Audit

**Sinkronisasi source custom:** LULUS.  
**Reproducibility deployment:** BELUM LULUS.  
**Smoke test halaman utama:** LULUS KECUALI detail kelompok.  
**RBAC/otorisasi:** BELUM LULUS.  
**Konsistensi data:** BELUM LULUS.  
**Dokumentasi:** BELUM SINKRON.  
**Kesiapan produksi penuh:** BELUM; diperlukan perbaikan P0 dan P1.

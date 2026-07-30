# Test Plan — PEDULI YPKM

**Kode:** TP-01 | **Versi:** 2.0 | **Diverifikasi:** 30 Juli 2026

## 1. Strategi

| Lapisan | Metode |
|---|---|
| Otomatis | PHPUnit feature tests, SQLite `:memory:`, `RefreshDatabase` |
| Reproducibility | Fresh clone → Composer → migration → master wilayah → test |
| Produksi | Test terisolasi + authenticated smoke test tanpa merusak data aktif |
| CI | GitHub Actions pada push/PR ke `main` |

Automated test tidak boleh memakai database produksi. Konfigurasi `phpunit.xml` memaksa `DB_CONNECTION=sqlite` dan `DB_DATABASE=:memory:`.

## 2. Automated tests aktif

### `PhaseOneAccessTest`

| ID | Skenario | Status |
|---|---|---|
| AT-01 | Ketua ditolak dari modul Relawan dan mutasi Admin | ✅ Lulus |
| AT-02 | Relawan dapat mengakses operasional tetapi bukan modul Admin | ✅ Lulus |
| AT-03 | Ketua hanya melihat kelompok sendiri | ✅ Lulus |
| AT-04 | Admin dapat mengakses modul pengelolaan | ✅ Lulus |

### `PhaseTwoFeaturesTest`

| ID | Skenario | Status |
|---|---|---|
| AT-05 | Distribusi menormalisasi nilai opsional | ✅ Lulus |
| AT-06 | Penerima terverifikasi dialokasikan ke Distribusi | ✅ Lulus |
| AT-07 | Bukti JPG/PNG/PDF tervalidasi dan tersimpan | ✅ Lulus |
| AT-08 | Peta dan laporan memakai data database | ✅ Lulus |
| AT-09 | Export laporan CSV dapat diunduh | ✅ Lulus |
| AT-10 | API cascading wilayah mengembalikan master yang benar | ✅ Lulus |

Hasil produksi terakhir: **8 test, 33 assertion, seluruhnya lulus**.

## 3. Fresh-clone test

```bash
bash setup-local.sh
```

Kriteria lulus:

- dependency dipasang dari `composer.lock`;
- migration `000000`–`000014` berhasil pada SQLite;
- seeder memverifikasi 6.810 wilayah dan 28 boundary;
- seluruh test lulus;
- working tree tracked tetap bersih.

## 4. Smoke test produksi

| Role/fitur | Expected |
|---|---|
| Admin `/peta`, `/laporan`, `/penerima`, `/distribusi/{id}` | HTTP 200 |
| Ketua `/peta` | HTTP 200, scoped kelompok |
| Ketua `/laporan` | HTTP 403 |
| Relawan `/peta`, `/relawan` | HTTP 200, scoped wilayah |
| Registrasi publik `/register` | HTTP 404 |
| Upload bukti Distribusi | File tersimpan, dapat direferensikan, dan terhapus bersama record |
| Laravel log | Tidak ada `production.ERROR` baru |

## 5. Skenario manual/regresi

- login sukses/gagal dan logout;
- CRUD Penerima, NIK unik, verifikasi, dan filter cascading;
- CRUD Kelompok dan penolakan penghapusan/ubah relasi yang tidak aman;
- tanda terima Distribusi serta larangan pindah kelompok setelah penerimaan;
- laporan periode/wilayah dan validasi angka dana, biaya, serta saldo;
- peta mobile, popup marker, boundary GeoJSON, dan link detail;
- upload file tidak valid/lebih dari 5 MB harus ditolak;
- akses ID langsung di luar kelompok/wilayah harus menghasilkan HTTP 403/404.

## 6. Kriteria rilis

- semua automated test dan CI hijau;
- migration dan route list berhasil;
- `HEAD`, `origin/main`, dan produksi sama;
- tidak ada tracked changes di produksi;
- backup sebelum migration tersedia;
- smoke test role dan log lulus;
- kredensial tidak berada dalam source, log, atau dokumentasi.

# Laporan Perbaikan Tahap 1 — Stabilitas dan RBAC

**Tanggal:** 30 Juli 2026  
**Commit produksi:** `05afbe7`  
**Status:** Selesai dan terverifikasi

## Ruang Lingkup

1. Pembatasan akses Admin, Relawan, dan Ketua Kelompok.
2. Scope data Ketua berdasarkan `users.kelompok_id`.
3. Scope Relawan berdasarkan wilayah kerja.
4. Relasi Ketua Kelompok menggunakan akun `User`, bukan penerima.
5. Jumlah anggota dihitung dari relasi penerima.
6. Halaman detail Kelompok.
7. Penghapusan route runtime lama/duplikat.
8. Automated feature test RBAC.
9. Permission minimum deployment.

## Matriks Akses Aktif

| Aksi | Admin | Relawan | Ketua Kelompok |
|---|:---:|:---:|:---:|
| Melihat penerima | Semua | Wilayah kerja | Kelompok sendiri |
| Menambah/mengubah penerima | ✅ | Wilayah kerja | Kelompok sendiri |
| Menghapus penerima | ✅ | ❌ | ❌ |
| Verifikasi/checklist | ✅ | Wilayah kerja | ❌ |
| Melihat kelompok | Semua | Wilayah kerja | Kelompok sendiri |
| Mengubah kelompok | ✅ | ❌ | ❌ |
| Melihat distribusi | Semua | Wilayah kerja | Kelompok sendiri |
| Mengubah distribusi | ✅ | ❌ | ❌ |
| Operasional distribusi | ✅ | Wilayah kerja | ❌ |
| User/keuangan/barang | ✅ | ❌ | ❌ |

## Perubahan Data

| Kelompok | Stored | Aktual | Ketua akun |
|---|---:|---:|---|
| Juar - Sekerak | 242 | 242 | Ketua Kelompok Juar |
| Acut_Lancok | 100 | 100 | wardi |

`jumlah_anggota` disinkronkan untuk kompatibilitas, tetapi tampilan aplikasi menggunakan `withCount('penerima')` sebagai sumber data aktual.

## Pengujian

Automated test: `tests/Feature/PhaseOneAccessTest.php`

- Ketua ditolak dari modul Relawan dan mutasi Admin.
- Relawan dapat membuka modul operasional, tetapi ditolak dari User/Keuangan.
- Ketua hanya dapat melihat kelompok sendiri.
- Admin dapat membuka modul pengelolaan.

**Hasil:** 4 test lulus, 12 assertion, durasi 0,48 detik.

## Verifikasi Deployment

- Local/GitHub/produksi berada pada commit yang sama.
- Migration `000012` berhasil.
- Total route aplikasi: 75.
- Route duplikat berdasarkan method+URI: 0.
- `.env`: mode 640.
- `storage` dan `bootstrap/cache`: mode 775.
- Backup sebelum deploy tersimpan di server pada direktori backup bertanggal.

## Catatan Tahap Berikutnya

Tahap 2 menangani validasi Distribusi, ekstensi upload, filter wilayah cascading, peta dinamis/polygon, dan laporan database nyata.

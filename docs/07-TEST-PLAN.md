# Test Plan (TP)
## PEDULI YPKM — Sistem Informasi Penyaluran Bantuan Yayasan Pelangi Kesejahteraan Masyarakat
**Kode:** TP-01 | **Versi:** 1.0

---

## 1. Skenario Testing

### 1.1 Autentikasi & Role
| ID | Skenario | Input | Expected |
|:---|:---|---:|:---|
| T-01 | Login Admin sukses | Email + password valid | Redirect ke dashboard admin |
| T-02 | Login gagal | Password salah | Pesan error |
| T-03 | Relawan akses admin panel | Relawan login | 403 Forbidden |
| T-04 | Logout | Klik logout | Redirect ke login |

### 1.2 Manajemen Penerima
| ID | Skenario | Expected |
|:---:|:---|---:|
| T-05 | Tambah penerima via form admin | Data tersimpan |
| T-06 | Tambah penerima dengan NIK duplikat | Tolak, pesan duplikat |
| T-07 | Verifikasi penerima | Status berubah jadi terverifikasi |
| T-08 | Tolak penerima | Status ditolak, catatan terisi |
| T-09 | Edit data penerima | Data berubah |
| T-10 | Hapus penerima | Data soft-deleted |
| T-11 | Cari penerima (nama/NIK) | Hasil sesuai filter |
| T-12 | Import Excel | 100 data masuk dalam < 5 detik |

### 1.7 Manajemen Keuangan
| ID | Skenario | Expected |
|:---:|:---|---|
| T-13 | Catat dana donatur baru (tunai) | Tersimpan, total dana bertambah |
| T-14 | Catat dana donatur 6x bertahap | Semua masuk, total akurat |
| T-15 | Buat anggaran per distribusi | Anggaran muncul |
| T-16 | Catat biaya operasional | Biaya tercatat |
| T-17 | Lihat rekap keuangan (dana masuk - pakai - sisa) | Angka balance |

### 1.8 Peta Distribusi
| ID | Skenario | Expected |
|:---:|:---|---|
| T-18 | Buka peta distribusi | Peta tampil dengan semua marker |
| T-19 | Klik marker distribusi | Popup muncul (paket, nilai, status) |
| T-20 | Filter peta berdasarkan status | Hanya marker sesuai status |
| T-21 | Peta di perangkat mobile | Responsif, marker tetap terlihat |

### 1.9 Distribusi
| ID | Skenario | Expected |
|:---:|:---|---|

### 1.3 Manajemen Kelompok
| ID | Skenario | Expected |
|:---:|:---|---:|
| T-13 | Buat kelompok baru | Kelompok muncul |
| T-14 | Pindahkan penerima ke kelompok lain | Relasi berubah |
| T-15 | Hapus kelompok (kosong) | Berhasil |
| T-16 | Hapus kelompok (ada anggota) | Ditolak |

### 1.4 Registrasi Mandiri (Publik)
| ID | Skenario | Expected |
|:---:|:---|---:|
| T-17 | Daftar via form publik | Sukses, status pending |
| T-18 | Submit tanpa NIK | Validasi error |
| T-19 | Submit dengan NIK duplikat | Pesan "sudah terdaftar" |

### 1.5 Distribusi
| ID | Skenario | Expected |
|:---:|:---|---:|
| T-20 | Buat jadwal distribusi baru | Jadwal muncul |
| T-21 | Relawan tandai penerima sudah terima | Status berubah |
| T-22 | Upload foto bukti | File tersimpan |
| T-23 | Selesaikan distribusi | Status selesai |

### 1.6 Laporan
| ID | Skenario | Expected |
|:---:|:---|---:|
| T-24 | Lihat rekap per daerah | Data muncul per kelompok |
| T-25 | Export Excel | File .xlsx terdownload |
| T-26 | Filter data per periode | Hanya data dalam rentang |

## 2. Alat Testing

| Tool | Untuk |
|:---|---|
| PHPUnit | Unit test & feature test |
| Laravel Dusk | Browser testing |
| Postman | API testing |
| Lighthouse | Performance audit |

## 3. Automated Test Aktif

File: `src/tests/Feature/PhaseOneAccessTest.php`

| ID | Skenario | Status produksi 30-07-2026 |
|---|---|---|
| AT-01 | Ketua ditolak dari modul Relawan dan mutasi Admin | ✅ Lulus |
| AT-02 | Relawan dapat mengakses operasional tetapi bukan User/Keuangan | ✅ Lulus |
| AT-03 | Ketua hanya melihat kelompok sendiri | ✅ Lulus |
| AT-04 | Admin dapat mengakses modul pengelolaan | ✅ Lulus |

Hasil terakhir: **4 test, 12 assertion, seluruhnya lulus** menggunakan database SQLite terisolasi melalui `RefreshDatabase`.

## 4. Kriteria Lolos

- Automated test yang sudah diterapkan wajib lulus.
- Tidak ada route method+URI duplikat.
- Tidak ada error HTTP 500 pada smoke test halaman utama.
- Scope kelompok/wilayah dan middleware role wajib menghasilkan 403 untuk akses tidak sah.
- Skenario fitur yang belum diterapkan (import/export, filter peta, upload bukti) tetap berstatus **rencana**, bukan dianggap lulus.
- Target render halaman < 3 detik dan tampilan mobile responsif.

**Warna Brand:** #00034a (Navy), #017723 (Green), #e5a820 (Gold)

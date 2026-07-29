# Test Plan (TP)
## Aplikasi Manajemen Distribusi Bantuan YPKM
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

## 3. Kriteria Lolos

- ✅ Semua test case T-01 sampai T-26 pass
- ✅ Waktu render halaman < 3 detik (Lighthouse)
- ✅ Tidak ada error 500
- ✅ Mobile responsif

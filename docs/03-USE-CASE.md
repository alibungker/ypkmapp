# Use Case & User Stories
## Aplikasi Manajemen Distribusi Bantuan YPKM
**Kode:** UC-01 | **Versi:** 1.0

---

## 1. Aktor

| Aktor | Deskripsi |
|:---|---|
| **Admin YPKM** | Pengelola utama sistem, verifikasi data, atur distribusi |
| **Relawan** | Petugas lapangan, input data, lakukan distribusi |
| **Ketua Kelompok** | Mewakili kelompok penerima, daftarkan anggota |
| **Penerima (Mandiri)** | Masyarakat yang mendaftar sendiri via form publik |

## 2. Use Case Diagram

```
┌─────────────────────────────────────────────────────┐
│                    SISTEM YPKM                       │
│                                                      │
│  ┌──────────┐   ┌──────────┐   ┌──────────┐        │
│  │ ADMIN    │   │ RELAWAN  │   │ KETUA    │        │
│  │          │   │          │   │ KELOMPOK │        │
│  └────┬─────┘   └────┬─────┘   └────┬─────┘        │
│       │              │              │               │
│  ┌────┴──────────────┴──────────────┴──────────┐    │
│  │           MANAJEMEN PENERIMA                │    │
│  │  (Verifikasi / Input / Import / Export)     │    │
│  └────────────────────┬───────────────────────┘    │
│                       │                            │
│  ┌────────────────────┴───────────────────────┐    │
│  │           MANAJEMEN DISTRIBUSI              │    │
│  │  (Jadwal / Eksekusi / Tanda Terima)         │    │
│  └────────────────────────────────────────────┘    │
└─────────────────────────────────────────────────────┘
```

## 3. User Stories

### Admin YPKM
| ID | Sebagai... | Saya ingin... | Sehingga... |
|:---|:---|---:|---|
| US-01 | Admin | memverifikasi pendaftaran penerima baru | data yang masuk valid |
| US-02 | Admin | mengelompokkan penerima per daerah | distribusi lebih teratur |
| US-03 | Admin | membuat jadwal distribusi | relawan tahu tugasnya |
| US-04 | Admin | melihat laporan real-time | tahu perkembangan distribusi |
| US-05 | Admin | mengekspor data ke Excel | laporan ke donatur |
| US-06 | Admin | menambah/mengedit data penerima | data selalu update |
| US-07 | Admin | mendeteksi duplikat NIK | tidak ada bantuan ganda |

### Relawan
| ID | Sebagai... | Saya ingin... | Sehingga... |
|:---|:---|---:|---|
| US-08 | Relawan | mendaftarkan penerima baru di lapangan | warga yang belum terdata bisa terbantu |
| US-09 | Relawan | melihat daftar distribusi yang ditugaskan | tahu jadwal dan lokasi |
| US-10 | Relawan | menandai penerima sudah terima bantuan | laporan akurat |
| US-11 | Relawan | mengupload foto bukti distribusi | ada dokumentasi |

### Ketua Kelompok
| ID | Sebagai... | Saya ingin... | Sehingga... |
|:---|:---|---:|---|
| US-12 | Ketua Kelompok | mendaftarkan anggota kelompok saya | warga binaan terdata |
| US-13 | Ketua Kelompok | melihat data anggota kelompok | tahu status mereka |
| US-14 | Ketua Kelompok | mendapat notifikasi jadwal distribusi | bisa menginformasikan anggota |

### Penerima (Mandiri)
| ID | Sebagai... | Saya ingin... | Sehingga... |
|:---|:---|---:|---|
| US-15 | Penerima | mendaftar via link WhatsApp | tidak perlu datang ke kantor |
| US-16 | Penerima | tahu status pendaftaran saya | tidak bingung |

## 4. Skenario Utama

### Skenario: Distribusi Bantuan
```
1. Admin login dan verifikasi data penerima
2. Admin buat jadwal distribusi baru
3. Sistem notifikasi ketua kelompok
4. Relawan login, lihat jadwal tugas
5. Relawan distribusikan bantuan
6. Relawan tandai penerima sudah terima
7. Sistem update status distribusi
8. Admin pantau progress real-time
```

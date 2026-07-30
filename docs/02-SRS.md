# Software Requirements Specification (SRS)
## PEDULI YPKM — Sistem Informasi Penyaluran Bantuan Yayasan Pelangi Kesejahteraan Masyarakat
**Kode:** SRS-01 | **Versi:** 1.0
**Standar:** ISO/IEC/IEEE 29148

---

## 1. Pendahuluan

### 1.1 Tujuan
Dokumen ini mendefinisikan kebutuhan fungsional dan non-fungsional untuk sistem manajemen distribusi bantuan YPKM.

### 1.2 Ruang Lingkup
Sistem mencakup: manajemen penerima, kelompok, distribusi, relawan, dan laporan.

## 2. Kebutuhan Fungsional

### 2.1 Manajemen Penerima

| ID | Requirement | Prioritas |
|:---|:---|---:|
| F-01 | Sistem harus menerima pendaftaran penerima dari 3 jalur: mandiri (form publik), relawan (dashboard), ketua kelompok | Tinggi |
| F-02 | Sistem harus menyimpan data penerima: nama, NIK, KK, alamat, kontak, jumlah anggota keluarga, titik koordinat | Tinggi |
| F-03 | Sistem harus memiliki status verifikasi: pending, terverifikasi, ditolak | Tinggi |
| F-04 | Sistem harus mendeteksi duplikasi data (berdasarkan NIK/KK) | Tinggi |
| F-05 | Sistem harus bisa import data dari Excel/CSV | Sedang |

### 2.2 Manajemen Kelompok

| ID | Requirement | Prioritas |
|:---|:---|---:|
| F-06 | Setiap penerima terdaftar dalam satu kelompok | Tinggi |
| F-07 | Setiap kelompok memiliki satu ketua kelompok | Tinggi |
| F-08 | Ketua kelompok melihat anggota kelompoknya sendiri | Tinggi |
| F-09 | Kelompok memiliki: nama, daerah, jumlah anggota | Tinggi |

### 2.3 Manajemen Distribusi

| ID | Requirement | Prioritas |
|:---|:---|---:|
| F-10 | Admin dapat membuat jadwal distribusi (tanggal, lokasi, jenis bantuan, kelompok sasaran) | Tinggi |
| F-11 | Status distribusi: direncanakan, berlangsung, selesai, dibatalkan | Tinggi |
| F-12 | Relawan menandai penerima sudah menerima bantuan | Tinggi |
| F-13 | Riwayat bantuan per penerima tercatat | Tinggi |
| F-14 | Jenis bantuan dapat bervariasi (sembako, uang, pakaian, dll) | Sedang |

### 2.4 Manajemen Relawan

| ID | Requirement | Prioritas |
|:---|:---|---:|
| F-15 | Data relawan: nama, kontak, daerah penugasan | Tinggi |
| F-16 | Relawan ditugaskan ke distribusi tertentu | Tinggi |
| F-17 | Relawan memiliki akses input data penerima | Tinggi |

### 2.5 Laporan

| ID | Requirement | Prioritas |
|:---|:---|---:|
| F-18 | Laporan jumlah penerima per daerah | Tinggi |
| F-19 | Laporan distribusi per periode | Tinggi |
| F-20 | Laporan real-time status bantuan | Sedang |
| F-21 | Export laporan ke PDF/Excel | Sedang |

### 2.6 Peta Distribusi

| ID | Requirement | Prioritas |
|:---|:---|---:|
| F-22 | Sistem harus menampilkan peta distribusi interaktif | Tinggi |
| F-23 | Marker di peta membedakan status: ✅ selesai, ⏳ berlangsung, 📋 rencana | Tinggi |
| F-24 | Click marker menampilkan popup: jumlah paket, nilai, tanggal | Tinggi |
| F-25 | Peta menampilkan statistik: total daerah, paket, penerima | Sedang |

### 2.7 Autentikasi & Role

| ID | Requirement | Prioritas |
|:---|:---|---:|
| F-22 | Login dengan email/username + password | Tinggi |
| F-23 | Role-based access: Admin, Relawan, Ketua Kelompok | Tinggi |
| F-24 | Admin bisa membuat akun relawan dan ketua kelompok | Tinggi |
| F-25 | Penerima mandiri tidak perlu login | Tinggi |

## 3. Kebutuhan Non-Fungsional

| ID | Requirement | Kategori |
|:---|:---|---:|
| NF-01 | Sistem harus responsif (mobile-first) | Usability |
| NF-02 | Sistem mendukung akses offline (PWA/minimal load) | Reliability |
| NF-03 | Waktu respon halaman < 3 detik | Performance |
| NF-04 | Mendukung 5.000+ penerima | Capacity |
| NF-05 | Enkripsi data sensitif (NIK, kontak) | Security |
| NF-06 | Backup database harian otomatis | Reliability |
| NF-07 | Log aktivitas pengguna | Auditability |

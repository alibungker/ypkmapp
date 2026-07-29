# Product Requirements Document (PRD)
## Aplikasi Manajemen Distribusi Bantuan YPKM
**Kode:** PRD-01 | **Versi:** 1.0 | **Tanggal:** 29 Juli 2026

---

## 1. Ringkasan Eksekutif

Aplikasi untuk Yayasan Pelangi Kesejahteraan Masyarakat (YPKM) guna mengelola pendataan penerima bantuan, distribusi sembako, dan pelaporan secara terintegrasi. Sistem mendukung 3 jalur pendaftaran (mandiri, relawan, ketua kelompok) dan proses distribusi dari hulu ke hilir.

## 2. Tujuan Bisnis

- Mempermudah pendataan penerima bantuan di daerah pelosok
- Transparansi distribusi bantuan dari donatur ke penerima
- Rekap laporan real-time untuk akuntabilitas
- Efisiensi operasional tim YPKM

## 3. Pemangku Kepentingan

| Stakeholder | Peran |
|---|---|
| Admin YPKM | Verifikasi data, kelola distribusi, laporan |
| Relawan | Input data penerima di lapangan, distribusi |
| Ketua Kelompok | Daftarkan anggota kelompoknya |
| Penerima Manfaat | Daftar mandiri, terima bantuan |
| Donatur | Pantau penyaluran bantuan (read-only) |

## 4. Fitur Utama

### MVP (Fase 1)
1. **Manajemen Penerima** — CRUD, import, 3 jalur registrasi
2. **Manajemen Kelompok** — kelompok penerima dengan ketua
3. **Manajemen Distribusi** — jadwal, eksekusi, status
4. **Manajemen Relawan** — data relawan, penugasan
5. **Laporan Dasar** — rekap per daerah, periode, status
6. **Autentikasi & Role** — Admin, Relawan, Ketua Kelompok

### Fase 2
7. **Registrasi Mandiri Publik** — form online
8. **Notifikasi WhatsApp** — otomatis via gateway
9. **Geospasial** — peta titik distribusi
10. **Dashboard Donatur** — lihat real-time penyaluran

## 5. Alur Bisnis

```
Registrasi (Mandiri/Relawan/Ketua)
        ↓
   Verifikasi Admin
        ↓
   Masuk Kelompok
        ↓
   Penjadwalan Distribusi
        ↓
   Konfirmasi Ketua Kelompok
        ↓
   Distribusi oleh Relawan
        ↓
   Tanda Terima (digital)
        ↓
   Laporan Tersimpan
```

## 6. Kriteria Sukses

- Waktu verifikasi penerima < 2 menit
- Input data offline-friendly
- Laporan siap < 5 detik
- Support 5.000+ penerima

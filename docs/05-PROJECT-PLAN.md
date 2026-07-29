# Project Plan & Timeline
## PEDULI YPKM — Sistem Informasi Penyaluran Bantuan Yayasan Pelangi Kesejahteraan Masyarakat
**Kode:** PP-01 | **Versi:** 1.0

---

## 1. Fase Pengembangan (OpenCode)

| Fase | Durasi | Output |
|:---|---:|:---|
| **Fase 0 — Setup** | 1 hari | Laravel project + database + auth |
| **Fase 1 — Master Data** | 2 hari | CRUD Penerima, Kelompok, Relawan |
| **Fase 2 — Distribusi** | 2 hari | Jadwal, Eksekusi, Tanda Terima |
| **Fase 3 — Registrasi Publik** | 1 hari | Form mandiri tanpa login |
| **Fase 4 — Laporan** | 1 hari | Rekap & export Excel |
| **Fase 5 — Deployment** | 1 hari | Deploy ke VPS |

**Total estimasi: ~8 hari kerja**

## 2. Urutan Build via OpenCode

```
Hari 1: laravel new ypkm + auth + database migration
Hari 2: model Penerima + Kelompok + CRUD
Hari 3: model Relawan + role permission
Hari 4: fitur distribusi + jadwal
Hari 5: tanda terima + bukti foto
Hari 6: form registrasi publik
Hari 7: laporan + export
Hari 8: deploy ke server
```

## 3. Prioritas Fitur

### Must Have (MVP)
- [ ] Autentikasi & Role (Admin, Relawan, Ketua)
- [ ] CRUD Penerima (3 jalur registrasi)
- [ ] Manajemen Kelompok
- [ ] Manajemen Distribusi
- [ ] Tanda Terima Digital
- [ ] Laporan Dasar

### Should Have (Fase 2)
- [ ] Form Registrasi Publik (tanpa login)
- [ ] Import Excel
- [ ] Export PDF/Excel
- [ ] Filter & Search lanjutan

### Nice to Have (Fase 3)
- [ ] Notifikasi WhatsApp otomatis
- [ ] Peta geospasial
- [ ] Dashboard donatur publik
- [ ] Multi-bahasa

## 4. Risiko & Mitigasi

| Risiko | Dampak | Mitigasi |
|:---|---:|:---|
| Data duplikat | Tinggi | Validasi NIK unik |
| Input offline | Sedang | PWA + cache |
| Server down | Tinggi | Backup harian, monitoring |
| Kehilangan data | Kritis | Backup database otomatis |

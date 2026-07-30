# Software Architecture Document (SAD)
## PEDULI YPKM — Sistem Informasi Penyaluran Bantuan Yayasan Pelangi Kesejahteraan Masyarakat
**Kode:** SAD-01 | **Versi:** 1.0

---

## 1. Arsitektur Sistem

```
┌──────────────────────────────────────────────────────────┐
│                     CLIENT LAYER                          │
│                                                          │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐  │
│  │  Browser     │  │  Mobile      │  │  WhatsApp    │  │
│  │  (Admin/User)│  │  (PWA)       │  │  (Notifikasi)│  │
│  └──────┬───────┘  └──────┬───────┘  └──────┬───────┘  │
└─────────┼─────────────────┼─────────────────┼───────────┘
          │                 │                 │
┌─────────┼─────────────────┼─────────────────┼───────────┐
│         ▼                 ▼                 ▼           │
│  ┌─────────────────────────────────────────────────┐   │
│  │              API GATEWAY (Laravel)               │   │
│  │  ┌──────────────────────────────────────────┐   │   │
│  │  │  REST API (JSON) / Web Routes            │   │   │
│  │  └──────────────────────────────────────────┘   │   │
│  └──────────────────────┬──────────────────────────┘   │
│                         │                              │
│  ┌──────────────────────┴──────────────────────────┐   │
│  │              APPLICATION LAYER                   │   │
│  │                                                  │   │
│  │  ┌────────────┐ ┌────────┐ ┌────────────┐      │   │
│  │  │ Auth & Role│ │Penerima│ │ Distribusi │      │   │
│  │  └────────────┘ └────────┘ └────────────┘      │   │
│  │  ┌────────────┐ ┌────────┐ ┌────────────┐      │   │
│  │  │  Kelompok  │ │Relawan │ │  Laporan   │      │   │
│  │  └────────────┘ └────────┘ └────────────┘      │   │
│  └──────────────────────┬──────────────────────────┘   │
│                         │                              │
│  ┌──────────────────────┴──────────────────────────┐   │
│  │              DATA LAYER                          │   │
│  │  ┌──────────────┐  ┌──────────────┐            │   │
│  │  │  MySQL       │  │  File Storage│             │   │
│  │  │  (Relasi)    │  │  (Foto/Doc)  │             │   │
│  │  └──────────────┘  └──────────────┘            │   │
│  └─────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────┘
```

## 2. Stack Teknologi

| Layer | Teknologi | Alasan |
|:---|---|:---|
| **Backend** | Laravel 11.x | Familiar, tool lengkap, ORM Eloquent |
| **Database** | MySQL 8+ | Reliabel, support geospasial |
| **Frontend Admin** | Laravel Blade + Tailwind CSS | Cepat, tanpa SPA |
| **Frontend Publik** | Blade + Tailwind | SEO friendly |
| **Mobile** | PWA (Service Worker) | Bisa offline, tanpa Play Store |
| **Storage** | Local server / S3 | Foto bukti distribusi |
| **Auth** | Laravel Breeze / Jetstream | Role bawaan |
| **Queue** | Laravel Queue + Database | Notifikasi async |
| **Map** | Leaflet.js + OpenStreetMap | Peta distribusi gratis |
| **Server** | VPS Proxmox (existing) | Sudah ada infra |

## 3. Struktur Database (Entity Overview)

```
┌──────────┐     ┌──────────┐     ┌──────────┐
│  USERS   │────>│ PENERIMA │<────│KELOMPOK  │
└──────────┘     └──────────┘     └──────────┘
     │                │                │
     │                │                │
     ▼                ▼                ▼
┌──────────┐     ┌──────────┐     ┌──────────┐
│ RELAWAN  │     │DISTRIBUSI│     │JADWAL    │
└──────────┘     └──────────┘     └──────────┘
                       │
                       ▼
                  ┌──────────┐
                  │  BUKTI   │
                  │  TERIMA  │
                  └──────────┘
```

## 4. Keamanan

### Kontrol aktif

- Middleware `AdminOnly` untuk fungsi administrasi.
- Middleware `OperationalOnly` untuk Admin/Relawan.
- Scope Ketua Kelompok berdasarkan `users.kelompok_id`.
- Scope Relawan berdasarkan kabupaten/kecamatan/desa kerja.
- CSRF protection Laravel.
- Satu akun Ketua per kelompok melalui unique index.
- Route runtime terversi dan pemeriksaan route duplikat.

### Kontrol yang masih direncanakan

- Enkripsi/transformasi data sensitif NIK dan nomor HP.
- Rate limiting khusus endpoint publik/API.
- Audit log aktivitas yang lengkap.
- PWA, queue notifikasi, dan integrasi WhatsApp belum menjadi fitur aktif.

**Domain:** peduli.ypkm.info

**Warna Brand:** Navy #00034a, Green #017723, Gold #e5a820

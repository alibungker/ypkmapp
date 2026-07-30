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

- Role-based middleware untuk setiap route
- Enkripsi data sensitif (NIK, no HP) di database
- CSRF protection
- Rate limiting API
- Logging aktivitas admin

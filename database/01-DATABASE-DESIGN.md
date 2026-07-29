# Database Design Document (DBD)
## PEDULI YPKM — Sistem Informasi Penyaluran Bantuan Yayasan Pelangi Kesejahteraan Masyarakat
**Kode:** DBD-01 | **Versi:** 1.0

---

## 1. Entity Relationship Diagram (ERD)

```
users
├── id (PK)
├── name
├── email (unique)
├── password
├── role: enum(admin,relawan,ketua_kelompok)
├── phone
├── foto
├── is_active
└── timestamps

penerima
├── id (PK)
├── user_id (FK -> users) [nullable]
├── kelompok_id (FK -> kelompok)
├── nik (unique)
├── no_kk
├── nama
├── tempat_lahir
├── tanggal_lahir
├── jenis_kelamin
├── alamat
├── provinsi
├── kabupaten
├── kecamatan
├── desa
├── rt_rw
├── phone
├── jumlah_keluarga
├── pekerjaan
├── penghasilan
├── titik_koordinat
├── foto_ktp
├── foto_kk
├── foto_rumah
├── sumber_data: enum(mandiri,relawan,ketua_kelompok)
├── status: enum(pending,terverifikasi,ditolak)
├── catatan_verifikasi
├── verified_by (FK -> users)
├── verified_at
└── timestamps

kelompok
├── id (PK)
├── nama
├── kode (unique)
├── daerah
├── kecamatan
├── ketua_id (FK -> penerima) [nullable]
├── jumlah_anggota
├── description
└── timestamps

distribusi
├── id (PK)
├── kode_distribusi (unique)
├── nama_kegiatan
├── tanggal
├── lokasi
├── titik_koordinat
├── kelompok_id (FK -> kelompok)
├── jenis_bantuan
├── jumlah_paket
├── sumber_dana
├── status: enum(direncanakan,berlangsung,selesai,dibatalkan)
├── created_by (FK -> users)
├── catatan
└── timestamps

penerima_distribusi (pivot)
├── id (PK)
├── penerima_id (FK -> penerima)
├── distribusi_id (FK -> distribusi)
├── status: enum(terjadwal,terkirim,diterima)
├── tanda_terima: boolean
├── foto_bukti
├── catatan
├── received_by (FK -> users) [relawan]
├── received_at
└── timestamps

relawan
├── id (PK)
├── user_id (FK -> users) unique
├── daerah_tugas
├── keahlian
├── status: enum(aktif,nonaktif)
└── timestamps

relawan_distribusi (pivot)
├── id (PK)
├── relawan_id (FK -> relawan)
├── distribusi_id (FK -> distribusi)
├── status: enum(ditugaskan,hadir,tidak_hadir)
└── timestamps

logs
├── id (PK)
├── user_id (FK -> users)
├── action
├── description
├── ip_address
├── user_agent
└── timestamps
```

## 2. Migration Command (Laravel)

```bash
# Buat migration
php artisan make:migration create_penerima_table
php artisan make:migration create_kelompok_table
php artisan make:migration create_distribusi_table
php artisan make:migration create_penerima_distribusi_table
php artisan make:migration create_relawan_table
php artisan make:migration create_relawan_distribusi_table
php artisan make:migration create_logs_table
```

## 3. Model Relationships

```php
// Penerima
belongsTo: User (verified_by)
belongsTo: Kelompok
hasMany: PenerimaDistribusi

// Kelompok
hasMany: Penerima
hasMany: Distribusi
belongsTo: Penerima (ketua)

// Distribusi
belongsTo: Kelompok
belongsTo: User (created_by)
hasMany: PenerimaDistribusi
belongsToMany: Relawan (pivot)

// Relawan
belongsTo: User
belongsToMany: Distribusi

// User (dari Laravel Breeze)
hasMany: Penerima (verified)
hasOne: Relawan
```

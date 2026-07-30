# PEDULI YPKM v1.1

**Pendataan & Distribusi Untuk Layanan Insani**
Yayasan Pelangi Kesejahteraan Masyarakat

## 🚀 Deploy via OpenCode

### 1. Clone ke Server

```bash
cd /www/wwwroot/peduli.ypkm.info

# Clone repo
git clone https://github.com/alibungker/ypkmapp.git .
# atau: git pull origin main
```

### 2. Buat Project Laravel

```bash
# Buat project Laravel baru
composer create-project laravel/laravel tmp
cp -r tmp/* ./
rm -rf tmp

# Install Breeze (autentikasi)
composer require laravel/breeze
php artisan breeze:install blade
npm install && npm run build
```

### 3. Copy Source Code Aplikasi

```bash
# Copy migrations
cp -r src/database/migrations/* database/migrations/

# Copy models
cp src/app/Models/*.php app/Models/

# Copy routes
cp src/routes/*.php routes/

# Copy controllers
cp -r src/app/Http/Controllers/* app/Http/Controllers/

# Copy views
cp -r src/resources/views/* resources/views/
```

### 4. Tambah Route ke routes/web.php

Buka `routes/web.php` dan tambahkan di bagian bawah:
```php
require __DIR__.'/../../src/routes/web.php';
```

### 4. Konfigurasi Database

Edit `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=peduli_ypkm
DB_USERNAME=root
DB_PASSWORD=yourpassword
```

```bash
# Buat database di phpMyAdmin/aaPanel
# Lalu jalankan:
php artisan migrate
```

### 5. Storage & Cache

```bash
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
chmod -R 777 storage bootstrap/cache
```

### 6. Selesai!

Akses: https://peduli.ypkm.info

## 📁 Struktur Source Code

```
src/
├── app/Models/          # Model Eloquent
├── database/migrations/ # Migrasi tabel
├── routes/              # Route files
├── resources/views/     # Blade templates
└── public/              # Static files
```

## 🗄️ Database Tables

| Table | Keterangan |
|---|---|
| users | Admin, relawan, ketua kelompok |
| penerimas | Data penerima bantuan |
| kelompoks | Kelompok penerima |
| distribusis | Jadwal distribusi |
| penerima_distribusi | Pivot penerima-distribusi |
| barang_bantuans | Katalog barang |
| stok_barangs | Stok inventory |
| distribusi_items | Barang per distribusi |
| dana_donaturs | Pemasukan donatur |
| biaya_operasionals | Biaya lapangan |
| anggarans | Rencana vs realisasi |
| relawans | Data relawan |
| logs | Audit trail |

## 🎨 Brand

- **Navy:** #00034a
- **Green:** #017723
- **Gold:** #e5a820
- **Domain:** peduli.ypkm.info

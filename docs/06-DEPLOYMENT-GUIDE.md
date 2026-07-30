# Deployment Guide — PEDULI YPKM

**Kode:** DEP-01 | **Versi:** 2.0 | **Diverifikasi:** 30 Juli 2026

## 1. Prasyarat

| Komponen | Minimum/ketentuan |
|---|---|
| PHP | 8.3+ |
| Ekstensi | `fileinfo`, `pdo_mysql`; `pdo_sqlite` untuk test |
| Composer | 2.x |
| Database | MySQL 8+ produksi; SQLite untuk development/test |
| Web server | Nginx/Apache, document root ke `public/` |
| Git | Working tree dan `.git` dapat ditulis user deployment |

## 2. Fresh clone development

```bash
git clone https://github.com/alibungker/ypkmapp.git
cd ypkmapp
bash setup-local.sh
```

Hasil yang wajib terlihat:

- migration `000000`–`000014` berhasil;
- master Aceh: 6.810 wilayah dan 28 boundary;
- seluruh feature test lulus.

Konfigurasi lokal menggunakan `.env.example` dan `database/database.sqlite`. Jangan menyalin `.env` produksi ke development.

## 3. Instalasi produksi baru

```bash
git clone https://github.com/alibungker/ypkmapp.git /www/wwwroot/peduli.ypkm.info
cd /www/wwwroot/peduli.ypkm.info
composer install --no-interaction --prefer-dist --optimize-autoloader
cp .env.example .env
php artisan key:generate
# Isi APP_ENV=production, APP_DEBUG=false, APP_URL, database, mail, dan session pada .env
bash deploy.sh
php artisan db:seed --class='Database\Seeders\WilayahAcehSeeder' --force
php artisan test --do-not-cache-result
```

## 4. Deployment pembaruan

### 4.1 Backup

Sebelum migration atau perubahan schema:

```bash
STAMP=$(date +%Y%m%d-%H%M%S)
mkdir -p /home/ali/backups/peduli-ypkm/$STAMP
# Dump database dengan akun yang memperoleh kredensial melalui .env/vault, bukan ditulis di command history.
tar -czf /home/ali/backups/peduli-ypkm/$STAMP/source-storage.tgz src storage/app/public
```

### 4.2 Deploy

```bash
cd /www/wwwroot/peduli.ypkm.info
git pull --ff-only origin main
bash deploy.sh
php artisan test --do-not-cache-result
```

`deploy.sh`:

1. memvalidasi `.env`, `vendor`, dan file wajib;
2. menyalin migration, model, controller, middleware, view, test, dan aset dari `src/`;
3. memasang entrypoint route versioned;
4. memperbarui Composer autoload;
5. menjalankan migration;
6. membersihkan cache;
7. memastikan storage link;
8. menerapkan permission minimum.

## 5. Ownership dan permission

Gunakan satu user deployment secara konsisten. Produksi aktif menggunakan `www:www` untuk root aplikasi, `.git`, `vendor`, `storage`, dan `bootstrap/cache`.

```bash
chown -R www:www /www/wwwroot/peduli.ypkm.info/.git \
    /www/wwwroot/peduli.ypkm.info/vendor \
    /www/wwwroot/peduli.ypkm.info/storage \
    /www/wwwroot/peduli.ypkm.info/bootstrap/cache
find storage bootstrap/cache -type d -exec chmod 775 {} \;
find storage bootstrap/cache -type f -exec chmod 664 {} \;
chmod 640 .env
```

> Jangan melakukan `chown -R` tanpa pengecualian terhadap file panel seperti `.user.ini`, dan jangan menggunakan `chmod -R 777`.

## 6. Verifikasi pascadeploy

```bash
php artisan migrate:status
php artisan route:list --except-vendor
php artisan test --do-not-cache-result
git rev-parse HEAD
git rev-parse origin/main
git status --porcelain --untracked-files=no
```

Smoke test wajib:

- Admin: `/peta`, `/laporan`, `/penerima`, detail Distribusi = HTTP 200;
- Ketua: data hanya kelompok sendiri; `/laporan` = HTTP 403;
- Relawan: `/peta` dan `/relawan` = HTTP 200 sesuai wilayah;
- `/register` = HTTP 404;
- upload dan penghapusan bukti Distribusi berhasil;
- tidak ada `production.ERROR` baru.

## 7. Rollback

```bash
cd /www/wwwroot/peduli.ypkm.info
git log --oneline -10
git checkout <commit-terakhir-yang-stabil>
bash deploy.sh
```

Jika migration mengubah data/schema, pulihkan database dari backup yang dibuat sebelum deployment. Jangan menjalankan `migrate:rollback` di produksi tanpa membaca method `down()` dan menilai dampak kehilangan data.

## 8. CI

Workflow `.github/workflows/ci.yml` berjalan pada push/PR ke `main` dan membangun aplikasi dari fresh checkout menggunakan SQLite terisolasi. Deployment tidak boleh dianggap selesai bila CI atau test produksi gagal.

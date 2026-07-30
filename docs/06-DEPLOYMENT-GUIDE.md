# Deployment Guide — OpenCode CLI
## PEDULI YPKM — Sistem Informasi Penyaluran Bantuan Yayasan Pelangi Kesejahteraan Masyarakat
**Kode:** DEP-01 | **Versi:** 1.0

---

## 1. Prasyarat Server

| Kebutuhan | Spesifikasi |
|:---|---|
| 1 | OS | Ubuntu 22.04 / Debian 12 |
| 2 | CPU | 2 core |
| 3 | RAM | 4 GB |
| 4 | Disk | 20 GB |
| 5 | Domain | peduli.ypkm.info |
| 6 | Web Server | Nginx |
| 7 | Database | MySQL 8+ |
| 8 | PHP | 8.2+ |
| 9 | Composer | Latest |
| 10 | Node.js | 20+ (untuk build asset) |

## 2. Lingkungan Pengembangan (via OpenCode)

```bash
# 1. Clone/Akses project di OpenCode terminal
cd /home/ali/hermes-workspace/ypkm-aplikasi

# 2. Buat project Laravel
composer create-project laravel/laravel .

# 3. Install packages
composer require laravel/breeze
php artisan breeze:install blade
npm install && npm run build

# 4. Konfigurasi database (cek .env)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ypkm
DB_USERNAME=ypkm_user
DB_PASSWORD=********
```

## 3. Struktur Database

```sql
CREATE DATABASE ypkm CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'ypkm_user'@'localhost' IDENTIFIED BY 'password';
GRANT ALL ON ypkm.* TO 'ypkm_user'@'localhost';
FLUSH PRIVILEGES;
```

```bash
# Jalankan migration
php artisan migrate
```

## 4. Deployment ke Production

### 4.1 Export dari Development
```bash
# Buat archive project
cd /home/ali/hermes-workspace
tar -czf peduli-ypkm.tar.gz ypkm-aplikasi/
```

### 4.2 Deploy via rsync/scp ke VPS
```bash
# Upload ke server
scp peduli-ypkm.tar.gz user@vps-server:/var/www/

# Di server VPS
cd /var/www
tar -xzf peduli-ypkm.tar.gz
cd peduli-ypkm

# Copy .env production
cp .env.example .env
# Edit: DB, APP_URL, APP_ENV=production, APP_DEBUG=false

# Install dependencies
composer install --optimize-autoloader --no-dev
npm install && npm run build

# Cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Storage link
php artisan storage:link

# Permission
chown -R www-data:www-data storage bootstrap/cache
chmod -R 755 storage bootstrap/cache

# Queue worker (background)
php artisan queue:work --daemon &
```

### 4.3 Konfigurasi Nginx

```nginx
server {
    listen 80;
    server_name peduli.ypkm.info;
    root /var/www/peduli-ypkm/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

```bash
# Aktifkan site
ln -s /etc/nginx/sites-available/ypkm /etc/nginx/sites-enabled/
nginx -t && systemctl reload nginx

# SSL (LetsEncrypt)
certbot --nginx -d peduli.ypkm.info
```

## 5. Backup

```bash
# Backup database
mysqldump -u ypkm_user -p ypkm > /backup/ypkm-$(date +%Y%m%d).sql

# Backup file
rsync -av /var/www/peduli-ypkm/storage /backup/storage-$(date +%Y%m%d)

# Cron job harian
0 3 * * * /usr/bin/mysqldump -u ypkm_user -p'password' ypkm > /backup/db/ypkm-$(date +\%Y\%m\%d).sql
```

## 6. Monitoring

```bash
# Cek log aplikasi
tail -f /var/www/peduli-ypkm/storage/logs/laravel.log

# Cek queue worker
ps aux | grep queue:work

# Cek resource
htop
```

## 7. Rollback

```bash
# Simpan versi sebelumnya
mv /var/www/peduli-ypkm /var/www/peduli-ypkm-rollback-$(date +%Y%m%d)

# Restore dari backup
tar -xzf /backup/peduli-ypkm-20260729.tar.gz -C /var/www/
```

# Deployment Guide — OpenCode CLI
## Aplikasi Manajemen Distribusi Bantuan YPKM
**Kode:** DEP-01 | **Versi:** 1.0

---

## 1. Prasyarat Server

| Kebutuhan | Spesifikasi |
|:---|---|
| OS | Ubuntu 22.04 / Debian 12 |
| CPU | 2 core |
| RAM | 4 GB |
| Disk | 20 GB |
| Domain | ypkm.acehprov.go.id (atau subdomain) |
| Web Server | Nginx |
| Database | MySQL 8+ |
| PHP | 8.2+ |
| Composer | Latest |
| Node.js | 20+ (untuk build asset) |

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
tar -czf ypkm-app.tar.gz ypkm-aplikasi/
```

### 4.2 Deploy via rsync/scp ke VPS
```bash
# Upload ke server
scp ypkm-app.tar.gz user@vps-server:/var/www/

# Di server VPS
cd /var/www
tar -xzf ypkm-app.tar.gz
cd ypkm-app

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
    server_name ypkm.acehprov.go.id;
    root /var/www/ypkm-app/public;

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
certbot --nginx -d ypkm.acehprov.go.id
```

## 5. Backup

```bash
# Backup database
mysqldump -u ypkm_user -p ypkm > /backup/ypkm-$(date +%Y%m%d).sql

# Backup file
rsync -av /var/www/ypkm-app/storage /backup/storage-$(date +%Y%m%d)

# Cron job harian
0 3 * * * /usr/bin/mysqldump -u ypkm_user -p'password' ypkm > /backup/db/ypkm-$(date +\%Y\%m\%d).sql
```

## 6. Monitoring

```bash
# Cek log aplikasi
tail -f /var/www/ypkm-app/storage/logs/laravel.log

# Cek queue worker
ps aux | grep queue:work

# Cek resource
htop
```

## 7. Rollback

```bash
# Simpan versi sebelumnya
mv /var/www/ypkm-app /var/www/ypkm-app-rollback-$(date +%Y%m%d)

# Restore dari backup
tar -xzf /backup/ypkm-app-20260729.tar.gz -C /var/www/
```

#!/usr/bin/env bash
# PEDULI YPKM — deployment source custom src/ ke runtime Laravel
set -Eeuo pipefail
umask 0027

APP_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$APP_ROOT"

required=(artisan composer.json src/routes/runtime-web.php src/routes/web.php)
for file in "${required[@]}"; do
    [[ -f "$file" ]] || { echo "ERROR: file wajib tidak ditemukan: $file" >&2; exit 2; }
done
[[ -f .env ]] || { echo "ERROR: .env tidak ditemukan. Salin .env.example dan isi konfigurasi aman." >&2; exit 2; }
[[ -f vendor/autoload.php ]] || { echo "ERROR: vendor belum tersedia. Jalankan composer install." >&2; exit 2; }

printf '🚀 PEDULI YPKM Deploy v1.2\n================================\n'

install -d app/Models app/Http/Controllers app/Http/Middleware \
    database/migrations database/seeders resources/views routes tests/Feature \
    public/img storage/app/public storage/framework/cache/data \
    storage/framework/sessions storage/framework/views storage/logs bootstrap/cache

printf '📦 Sinkronisasi source aplikasi...\n'
cp -a src/database/migrations/. database/migrations/
cp -a src/app/Models/. app/Models/
cp -a src/app/Http/Controllers/. app/Http/Controllers/
cp -a src/app/Http/Middleware/. app/Http/Middleware/
cp -a src/resources/views/. resources/views/
cp -a src/tests/Feature/. tests/Feature/
cp -a src/public/img/. public/img/
cp src/routes/runtime-web.php routes/web.php

printf '🧩 Memperbarui autoload...\n'
composer dump-autoload --optimize --no-interaction >/dev/null

printf '🗄️ Menjalankan migration...\n'
php artisan migrate --force

printf '🧹 Membersihkan cache...\n'
php artisan optimize:clear

printf '🔗 Memastikan storage link...\n'
php artisan storage:link 2>/dev/null || true

printf '🔐 Permission minimum...\n'
find storage bootstrap/cache -type d -exec chmod 775 {} \;
find storage bootstrap/cache -type f -exec chmod 664 {} \;
chmod 640 .env

printf '✅ Deploy selesai pada commit %s\n' "$(git rev-parse --short HEAD 2>/dev/null || echo non-git)"

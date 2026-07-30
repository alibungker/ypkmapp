#!/usr/bin/env bash
# Bootstrap fresh clone PEDULI YPKM pada lingkungan pengembangan.
set -Eeuo pipefail
umask 0027
cd "$(dirname "${BASH_SOURCE[0]}")"

command -v php >/dev/null || { echo 'ERROR: PHP tidak tersedia.' >&2; exit 2; }
command -v composer >/dev/null || { echo 'ERROR: Composer tidak tersedia.' >&2; exit 2; }
php -r 'exit(version_compare(PHP_VERSION,"8.3.0","<") ? 1 : 0);' || { echo 'ERROR: PHP minimal 8.3.' >&2; exit 2; }
php -r 'exit(extension_loaded("fileinfo") ? 0 : 1);' || { echo 'ERROR: ekstensi PHP fileinfo wajib aktif.' >&2; exit 2; }

# Siapkan directory wajib sebelum Composer menjalankan package discovery.
install -d app/Models database storage/app/public storage/framework/cache/data \
    storage/framework/sessions storage/framework/views storage/logs bootstrap/cache
cp src/app/Models/Models.php app/Models/Models.php
composer install --no-interaction --prefer-dist
[[ -f .env ]] || cp .env.example .env
[[ -f database/database.sqlite ]] || touch database/database.sqlite
php artisan key:generate --force
bash deploy.sh
php artisan db:seed --class='Database\Seeders\WilayahAcehSeeder' --force
php artisan test --do-not-cache-result

printf '✅ Fresh-clone setup dan seluruh test berhasil.\n'

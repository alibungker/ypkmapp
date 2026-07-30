#!/bin/bash
# ============================================================
# PEDULI YPKM — Deploy Script
# Jalankan dari folder root Laravel: bash deploy.sh
# ============================================================
set -e

echo "🚀 PEDULI YPKM Deploy v1.1"
echo "================================"
echo ""

# 1. Copy migrations
echo "📦 Copying migrations..."
cp -r src/database/migrations/* database/migrations/

# 2. Copy models
echo "📦 Copying models..."
cp src/app/Models/*.php app/Models/

# 3. Copy controllers
echo "📦 Copying controllers..."
mkdir -p app/Http/Controllers
cp src/app/Http/Controllers/*.php app/Http/Controllers/

# 3b. Copy middleware
echo "📦 Copying middleware..."
mkdir -p app/Http/Middleware
cp src/app/Http/Middleware/*.php app/Http/Middleware/ 2>/dev/null || true

# 4. Copy views
echo "📦 Copying views..."
cp -r src/resources/views/* resources/views/

# 4b. Copy automated tests
if [ -d src/tests ]; then
    echo "🧪 Copying tests..."
    mkdir -p tests/Feature
    cp -r src/tests/Feature/* tests/Feature/
fi

# 4c. Copy public assets (logo, img, dll)
echo "📦 Copying public assets..."
if [ -d src/public/img ]; then
    mkdir -p public/img
    cp -r src/public/img/* public/img/
fi

# 5. Add routes
echo "📦 Updating routes..."
# Cek apakah sudah ada require, jika belum tambahkan
if ! grep -q "require.*src/routes" routes/web.php 2>/dev/null; then
    echo "" >> routes/web.php
    echo "// Routes PEDULI YPKM" >> routes/web.php
    echo "require __DIR__.'/../src/routes/web.php';" >> routes/web.php
    echo "✅ Routes added"
else
    echo "⏩ Routes already exist"
fi

# 6. Run migrations
echo "🗄️ Running migrations..."
php artisan migrate --force

# 7. Cache clear
echo "🧹 Clearing cache..."
php artisan config:clear
php artisan route:clear
php artisan view:clear

# 8. Storage link
echo "🔗 Storage link..."
php artisan storage:link 2>/dev/null || true

# 9. Permission minimum (hindari chmod 777)
echo "🔐 Setting minimum permissions..."
find storage bootstrap/cache -type d -exec chmod 775 {} \; 2>/dev/null || true
find storage bootstrap/cache -type f -exec chmod 664 {} \; 2>/dev/null || true
chmod 640 .env 2>/dev/null || true

echo ""
echo "✅ Deploy selesai!"
echo "📌 Akses: https://peduli.ypkm.info"
echo "📌 Login: gunakan akun admin yang sudah dibuat"

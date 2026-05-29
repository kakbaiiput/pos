#!/bin/bash

# Script instalasi Arka POS untuk aaPanel
# Jalankan dari dalam folder project: bash install.sh

set -e

echo "========================================"
echo "  ARKA POS - Instalasi Otomatis"
echo "========================================"

# 1. Install PHP dependencies
echo ""
echo "[1/6] Install PHP dependencies (composer)..."
composer install --no-interaction --optimize-autoloader

# 2. Generate APP_KEY jika belum ada
echo ""
echo "[2/6] Generate APP_KEY..."
php artisan key:generate --force

# 3. Migrasi database + seed data awal
echo ""
echo "[3/6] Migrasi database + isi data awal..."
php artisan migrate:fresh --seed --force

# 5. Storage link
echo ""
echo "[5/6] Buat storage link..."
php artisan storage:link

# 6. Set permission
echo ""
echo "[6/6] Set permission folder..."
chmod -R 775 storage bootstrap/cache
chown -R www:www storage bootstrap/cache 2>/dev/null || chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true

# 7. Cache konfigurasi untuk production
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo ""
echo "========================================"
echo "  INSTALASI SELESAI!"
echo "========================================"
echo ""
echo "  Akses: https://pos.octolink.id"
echo ""
echo "  Login default:"
echo "  Super Admin : 26050001 / password"
echo "  Admin       : 26050002 / password"
echo "  Kasir       : 26050005 / password"
echo ""
echo "  !! Segera ganti password setelah login !!"
echo "========================================"

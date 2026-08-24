#!/bin/sh
set -e

# Bersihkan cache package stale dari host agar tidak bentrok dengan vendor container
rm -f /var/www/html/bootstrap/cache/packages.php /var/www/html/bootstrap/cache/services.php
php artisan package:discover --quiet 2>/dev/null || true

# Fix permissions pada volume yang di-mount (storage/app/public dari host)
# agar Apache (www-data) bisa baca/tulis file gambar
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Buat symlink public/storage -> storage/app/public
# --force agar tidak error jika sudah ada
php artisan storage:link --force 2>/dev/null || true

# Jalankan Apache
exec apache2-foreground

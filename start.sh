#!/usr/bin/env bash
set -e

# Railway sets PORT. Default to 80 for local runs.
PORT="${PORT:-80}"

echo "Starting Apache on PORT=$PORT"

# ✅ FIX: ensure ONLY ONE MPM module is enabled (prefork)
a2dismod mpm_event mpm_worker mpm_prefork >/dev/null 2>&1 || true
rm -f /etc/apache2/mods-enabled/mpm_event.* /etc/apache2/mods-enabled/mpm_worker.* /etc/apache2/mods-enabled/mpm_prefork.* || true
a2enmod mpm_prefork >/dev/null 2>&1 || true

# Update Apache listen port
sed -i "s/^Listen 80$/Listen ${PORT}/" /etc/apache2/ports.conf || true

# Update VirtualHost port (Debian apache default site)
sed -i "s/<VirtualHost \*:80>/<VirtualHost \*:${PORT}>/" /etc/apache2/sites-available/000-default.conf || true

# Ensure Laravel has correct permissions at runtime too
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache || true

# Laravel caches (optional)
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Start Apache
exec apache2-foreground

#!/usr/bin/env bash
set -e

PORT="${PORT:-8080}"
echo "Starting Laravel on Railway (PORT=$PORT)"

# Silence Apache server name warning
echo "ServerName localhost" > /etc/apache2/conf-available/servername.conf
a2enconf servername >/dev/null 2>&1 || true

# Force Apache to listen on Railway port (IPv4 explicit)
printf "Listen 0.0.0.0:%s\n" "$PORT" > /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:.*>/<VirtualHost \*:${PORT}>/" /etc/apache2/sites-available/000-default.conf || true

# DEBUG: Show what ports we are listening on
echo "DEBUG: ports.conf content:"
cat /etc/apache2/ports.conf
echo "DEBUG: 000-default.conf VirtualHost line:"
grep "VirtualHost" /etc/apache2/sites-available/000-default.conf

# Ensure correct permissions (CRITICAL)
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# 🔥 CRITICAL: clear stale Laravel caches (Railway injects env at runtime)
php artisan optimize:clear || true
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true

# Optional but recommended
php artisan storage:link || true

# Log environment sanity (shows in Railway logs)
echo "APP_ENV=$APP_ENV"
echo "DB_HOST=$DB_HOST"
echo "DB_DATABASE=$DB_DATABASE"

# Fix MPM Issue: Force remove conflicting modules at runtime
rm -f /etc/apache2/mods-enabled/mpm_event.load /etc/apache2/mods-enabled/mpm_event.conf
rm -f /etc/apache2/mods-enabled/mpm_worker.load /etc/apache2/mods-enabled/mpm_worker.conf

# Start Apache
exec apache2-foreground
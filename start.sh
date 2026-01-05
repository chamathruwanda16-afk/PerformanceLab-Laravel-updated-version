#!/usr/bin/env bash
set -e

PORT="${PORT:-8080}"
echo "Starting Laravel on Railway (PORT=$PORT)"

# Silence Apache server name warning
echo "ServerName localhost" > /etc/apache2/conf-available/servername.conf
a2enconf servername >/dev/null 2>&1 || true

# Force Apache to listen on Railway port
printf "Listen %s\n" "$PORT" > /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:.*>/<VirtualHost \*:${PORT}>/" /etc/apache2/sites-available/000-default.conf || true

# Ensure correct permissions (CRITICAL)
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# 🔥 CRITICAL: clear stale Laravel caches (Railway injects env at runtime)
php artisan config:clear || true
php artisan cache:clear || true
php artisan route:clear || true
php artisan view:clear || true

# Optional but recommended
php artisan storage:link || true

# Log environment sanity (shows in Railway logs)
echo "APP_ENV=$APP_ENV"
echo "DB_HOST=$DB_HOST"
echo "DB_DATABASE=$DB_DATABASE"

# Start Apache
exec apache2-foreground
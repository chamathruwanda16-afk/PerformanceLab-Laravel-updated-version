#!/usr/bin/env bash
set -e

PORT="${PORT:-8080}"
echo "Starting Laravel on Railway (PORT=$PORT)"

# Silence Apache server name warning
echo "ServerName localhost" > /etc/apache2/conf-available/servername.conf
a2enconf servername >/dev/null 2>&1 || true

# ✅ Force Apache to listen on Railway port (IPv4 explicit)
cat > /etc/apache2/ports.conf <<EOF
Listen 0.0.0.0:${PORT}
EOF

# ✅ Force VirtualHost to Railway port
sed -i "s/<VirtualHost \*:.*>/<VirtualHost \*:${PORT}>/" /etc/apache2/sites-available/000-default.conf || true

# ✅ Ensure DocumentRoot is Laravel public (prevents 502/404 issues)
sed -i "s#DocumentRoot .*#DocumentRoot /var/www/html/public#g" /etc/apache2/sites-available/000-default.conf || true

# DEBUG: Show what ports we are listening on
echo "DEBUG: ports.conf content:"
cat /etc/apache2/ports.conf
echo "DEBUG: 000-default.conf VirtualHost line:"
grep "VirtualHost" /etc/apache2/sites-available/000-default.conf || true
echo "DEBUG: 000-default.conf DocumentRoot line:"
grep "DocumentRoot" /etc/apache2/sites-available/000-default.conf || true

# ✅ FIX: Unconditionally use authoritative Railway MySQL variables
if [ -n "$MYSQLHOST" ]; then
    echo "Using Railway MYSQLHOST: $MYSQLHOST"
    export DB_HOST="$MYSQLHOST"
fi
if [ -n "$MYSQLDATABASE" ]; then
    echo "Using Railway MYSQLDATABASE: $MYSQLDATABASE"
    export DB_DATABASE="$MYSQLDATABASE"
fi
if [ -n "$MYSQLPORT" ]; then
    export DB_PORT="$MYSQLPORT"
fi
if [ -n "$MYSQLUSER" ]; then
    export DB_USERNAME="$MYSQLUSER"
fi
if [ -n "$MYSQLPASSWORD" ]; then
    export DB_PASSWORD="$MYSQLPASSWORD"
fi

# ✅ FIX: Force Laravel to use MySQL and avoid DB-backed cache/session on boot
export DB_CONNECTION=mysql
export CACHE_DRIVER=file
export SESSION_DRIVER=file
export QUEUE_CONNECTION=sync
export CACHE_STORE=file

# Ensure correct permissions (CRITICAL)
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# 🔥 Clear stale Laravel caches (Railway injects env at runtime)
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
echo "DB_PORT=$DB_PORT"
echo "DB_USERNAME=$DB_USERNAME"

# ✅ Fix MPM Issue: Force remove conflicting modules at runtime (safe)
rm -f /etc/apache2/mods-enabled/mpm_event.* || true
rm -f /etc/apache2/mods-enabled/mpm_worker.* || true

# Start Apache
exec apache2-foreground

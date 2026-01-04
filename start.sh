#!/usr/bin/env bash
set -e

PORT="${PORT:-80}"
echo "Starting Apache on PORT=$PORT"

# Silence the servername warning (optional)
echo "ServerName localhost" > /etc/apache2/conf-available/servername.conf
a2enconf servername >/dev/null 2>&1 || true

# ✅ HARD reset MPM modules (keep prefork only)
a2dismod mpm_event mpm_worker mpm_prefork >/dev/null 2>&1 || true
rm -f /etc/apache2/mods-enabled/mpm_event.* /etc/apache2/mods-enabled/mpm_worker.* /etc/apache2/mods-enabled/mpm_prefork.* || true
a2enmod mpm_prefork >/dev/null 2>&1 || true

# ✅ FORCE Apache to listen on Railway PORT (do not rely on sed)
printf "Listen %s\n" "$PORT" > /etc/apache2/ports.conf

# ✅ FORCE VirtualHost port
sed -i "s/<VirtualHost \*:.*>/<VirtualHost \*:${PORT}>/" /etc/apache2/sites-available/000-default.conf || true

# Debug (so we can confirm in Railway logs)
echo "---- ports.conf ----"
cat /etc/apache2/ports.conf || true
echo "---- vhost ----"
grep -n "VirtualHost" /etc/apache2/sites-available/000-default.conf || true

# Permissions
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache || true

# ✅ IMPORTANT: skip artisan cache at boot until site responds (you can re-enable later)
# php artisan config:cache || true
# php artisan route:cache || true
# php artisan view:cache || true

exec apache2-foreground

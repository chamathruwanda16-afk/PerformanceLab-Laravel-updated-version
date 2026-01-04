#!/usr/bin/env bash
set -e

PORT="${PORT:-80}"
echo "Starting Apache on PORT=$PORT"

# Silence servername warning (optional)
echo "ServerName localhost" > /etc/apache2/conf-available/servername.conf
a2enconf servername >/dev/null 2>&1 || true

# Ensure ONLY prefork MPM
a2dismod mpm_event mpm_worker mpm_prefork >/dev/null 2>&1 || true
rm -f /etc/apache2/mods-enabled/mpm_event.* /etc/apache2/mods-enabled/mpm_worker.* /etc/apache2/mods-enabled/mpm_prefork.* || true
a2enmod mpm_prefork >/dev/null 2>&1 || true

# Bind Apache to Railway PORT
sed -i "s/^Listen 80$/Listen ${PORT}/" /etc/apache2/ports.conf || true
sed -i "s/<VirtualHost \*:80>/<VirtualHost \*:${PORT}>/" /etc/apache2/sites-available/000-default.conf || true

# ✅ Print what Apache will actually use (debug)
echo "---- ports.conf ----"
cat /etc/apache2/ports.conf || true
echo "---- 000-default.conf VirtualHost ----"
grep -n "VirtualHost" /etc/apache2/sites-available/000-default.conf || true
echo "---- listening sockets (before start) ----"

# Permissions
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache || true

# ✅ IMPORTANT: do NOT run route/cache commands on deploy (they can hang/fail)
# If you want them later, do them once after site works.

exec apache2-foreground

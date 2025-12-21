#!/bin/sh
set -e

# Force ONLY prefork at runtime
a2dismod mpm_event mpm_worker 2>/dev/null || true
a2enmod mpm_prefork 2>/dev/null || true

# Bind Apache to Railway PORT
: "${PORT:=8080}"
sed -i "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf || true
sed -i "s/<VirtualHost \*:80>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf || true

echo "ServerName localhost" >> /etc/apache2/apache2.conf || true

exec apache2-foreground

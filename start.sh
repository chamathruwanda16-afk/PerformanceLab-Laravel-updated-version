#!/bin/sh
set -e

# Railway provides PORT at runtime
: "${PORT:=8080}"

# Update Apache to listen on the Railway port
sed -i "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf

# Also ensure default vhost listens on correct port (sometimes needed)
sed -i "s/<VirtualHost \*:80>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf

# Optional but helps avoid Apache warnings
echo "ServerName localhost" >> /etc/apache2/apache2.conf

exec apache2-foreground

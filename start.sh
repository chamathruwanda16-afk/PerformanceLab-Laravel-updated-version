
set -e

# Railway sets PORT. Default to 80 for local runs.
PORT="${PORT:-80}"

echo "Starting Apache on PORT=$PORT"

# Update Apache listen port
sed -i "s/^Listen 80$/Listen ${PORT}/" /etc/apache2/ports.conf || true

# Update VirtualHost port (Debian apache default site)
sed -i "s/<VirtualHost \*:80>/<VirtualHost \*:${PORT}>/" /etc/apache2/sites-available/000-default.conf || true

# (Optional but helpful) Ensure Laravel has correct permissions at runtime too
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache || true

# If you need Laravel caches (optional)
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Start Apache (official way for php:apache images)
exec apache2-foreground

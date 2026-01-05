FROM php:8.2-apache

# ✅ Force rebuild when value changes
ARG CACHE_BUST=7
RUN echo "cache bust: $CACHE_BUST"

# Install system deps + PHP extensions
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libpng-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo_mysql zip mbstring exif pcntl bcmath gd \
    && a2enmod rewrite

# ✅ Enable PHP module for Apache
RUN a2enmod php8.2

# ✅ HARD reset: ensure ONLY prefork MPM is enabled
RUN a2dismod mpm_event mpm_worker mpm_prefork || true \
 && rm -f /etc/apache2/mods-enabled/mpm_event.* || true \
 && rm -f /etc/apache2/mods-enabled/mpm_worker.* || true \
 && rm -f /etc/apache2/mods-enabled/mpm_prefork.* || true \
 && a2enmod mpm_prefork

# Build deps for PECL and install MongoDB extension (if needed)
RUN apt-get update && apt-get install -y $PHPIZE_DEPS \
    && pecl install mongodb-1.21.2 \
    && docker-php-ext-enable mongodb \
    && apt-get purge -y --auto-remove $PHPIZE_DEPS \
    && rm -rf /var/lib/apt/lists/*

# Set Laravel public folder as doc root
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
  /etc/apache2/sites-available/*.conf \
  /etc/apache2/apache2.conf \
  /etc/apache2/conf-available/*.conf

# Configure Apache to listen on Railway's PORT
RUN echo "Listen \${PORT}" > /etc/apache2/ports.conf \
 && echo "<IfModule ssl_module>" >> /etc/apache2/ports.conf \
 && echo "  Listen 443" >> /etc/apache2/ports.conf \
 && echo "</IfModule>" >> /etc/apache2/ports.conf \
 && echo "<IfModule mod_gnutls.c>" >> /etc/apache2/ports.conf \
 && echo "  Listen 443" >> /etc/apache2/ports.conf \
 && echo "</IfModule>" >> /etc/apache2/ports.conf

WORKDIR /var/www/html

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY . .

RUN composer install --no-dev --optimize-autoloader

RUN chown -R www-data:www-data storage bootstrap/cache

# Configure Apache logs
RUN echo "ErrorLog /proc/self/fd/2" >> /etc/apache2/apache2.conf \
 && echo "CustomLog /proc/self/fd/1 combined" >> /etc/apache2/apache2.conf

RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Use the PORT environment variable (Railway provides this)
CMD sed -i "s/Listen 80/Listen \${PORT}/g" /etc/apache2/ports.conf && \
    sed -i "s/Listen 8888/Listen \${PORT}/g" /etc/apache2/ports.conf && \
    docker-php-entrypoint apache2-foreground
FROM php:8.2-apache

# Install system deps + PHP extensions
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev \
    && docker-php-ext-install pdo_mysql zip \
    && a2enmod rewrite

# ✅ Fix: ensure only prefork MPM is enabled (disable both others safely)
RUN a2dismod mpm_event mpm_worker || true \
    && a2enmod mpm_prefork

# Build deps for PECL and install MongoDB extension
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

WORKDIR /var/www/html

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY . .

RUN composer install --no-dev --optimize-autoloader

RUN chown -R www-data:www-data storage bootstrap/cache

# ✅ Add startup script (sets Apache Listen port at runtime)
COPY start.sh /start.sh
RUN chmod +x /start.sh

CMD ["/start.sh"]

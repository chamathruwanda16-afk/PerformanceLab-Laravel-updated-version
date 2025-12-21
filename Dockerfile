FROM php:8.2-apache

# Install system deps + PHP extensions
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev \
    && docker-php-ext-install pdo_mysql zip \
    && a2enmod rewrite

# ✅ Fix: ensure only prefork MPM is enabled
RUN a2dismod mpm_event && a2enmod mpm_prefork

# Build deps for PECL and install MongoDB extension
RUN apt-get update && apt-get install -y $PHPIZE_DEPS \
    && pecl install mongodb-1.21.2 \
    && docker-php-ext-enable mongodb \
    && apt-get purge -y --auto-remove $PHPIZE_DEPS \
    && rm -rf /var/lib/apt/lists/*

# ✅ Railway uses dynamic PORT, not 80
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
  /etc/apache2/sites-available/*.conf \
  /etc/apache2/apache2.conf \
  /etc/apache2/conf-available/*.conf

RUN sed -ri "s/Listen 80/Listen \${PORT}/" /etc/apache2/ports.conf

WORKDIR /var/www/html

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY . .

RUN composer install --no-dev --optimize-autoloader

RUN chown -R www-data:www-data storage bootstrap/cache

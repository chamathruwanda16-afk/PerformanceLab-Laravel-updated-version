FROM php:8.2-apache

# 1) System deps + PHP extensions + Apache modules
RUN apt-get update && apt-get install -y \
    git zip unzip libpng-dev libjpeg-dev libfreetype6-dev libonig-dev \
    libxml2-dev libzip-dev curl gnupg \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql zip gd \
    && a2enmod rewrite \
    # ✅ Ensure ONLY prefork MPM is enabled (fixes "More than one MPM loaded")
    && a2dismod mpm_event mpm_worker || true \
    && a2enmod mpm_prefork \
    && rm -rf /var/lib/apt/lists/*

# 2) MongoDB PHP extension (pinned to match your composer.lock requirement)
RUN pecl install mongodb-1.21.2 \
    && docker-php-ext-enable mongodb

# 3) Set Apache docroot to Laravel /public
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
 && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

WORKDIR /var/www/html

# 4) Copy app
COPY . .

# 5) Install Composer + PHP dependencies
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
 && composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# 6) Permissions for Laravel
RUN mkdir -p storage/app/public \
 && chown -R www-data:www-data storage bootstrap/cache \
 && chmod -R 775 storage bootstrap/cache

EXPOSE 80
CMD ["apache2-foreground"]

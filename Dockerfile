FROM php:8.2-apache

# 1) System deps + PHP extensions + Node.js + MPM FIX
# We use 'rm -f' to remove conflicting default Apache modules preventing the crash
RUN apt-get update && apt-get install -y \
    git zip unzip libpng-dev libjpeg-dev libfreetype6-dev libonig-dev \
    libxml2-dev libzip-dev curl gnupg nodejs npm \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql zip gd \
    && rm -f /etc/apache2/mods-enabled/mpm_event.conf \
    && rm -f /etc/apache2/mods-enabled/mpm_event.load \
    && rm -f /etc/apache2/mods-enabled/mpm_worker.conf \
    && rm -f /etc/apache2/mods-enabled/mpm_worker.load \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

# 2) MongoDB (FIXED: Pinned to version 1.21.2 to match your composer.lock)
RUN pecl install mongodb-1.21.2 \
    && docker-php-ext-enable mongodb

# 3) Set Workdir
WORKDIR /var/www/html

# 4) Copy App
COPY . .

# 5) PHP Dependencies
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install --no-dev --optimize-autoloader

# 6) Frontend Build
RUN npm ci && npm run build

# 7) Setup Storage (mkdir instead of COPY to fix "file not found" error)
RUN mkdir -p storage/app/public \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# 8) Apache Config
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
 && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 9) Link Storage
RUN php artisan storage:link

EXPOSE 80
CMD ["apache2-foreground"]
FROM php:8.2-apache

# 1) System deps + PHP extensions + Node.js/NPM
# We include the "rm -f" commands here to fix the Apache MPM crash
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

# 2) MongoDB PHP driver
RUN pecl install mongodb \
    && docker-php-ext-enable mongodb

# 3) Set working directory
WORKDIR /var/www/html

# 4) Copy the app code
COPY . .

# 5) Install PHP dependencies
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install --no-dev --optimize-autoloader

# 6) Frontend Build (NPM)
# We do this AFTER composer to keep layers cached if only JS changes
RUN npm ci && npm run build

# 7) Apache Configuration
# Point Apache to the 'public' folder
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
 && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 8) Storage & Permissions
# Create the symlink for storage
RUN php artisan storage:link

# Fix permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80

CMD ["apache2-foreground"]
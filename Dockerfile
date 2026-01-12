FROM php:8.2-apache

ENV COMPOSER_ALLOW_SUPERUSER=1

# System deps + PHP extensions + SSL libs (needed for MongoDB Atlas TLS)
RUN apt-get update && apt-get install -y \
    git unzip zip libzip-dev libpng-dev libjpeg-dev libfreetype6-dev \
    libonig-dev libxml2-dev curl gnupg \
    ca-certificates openssl libssl-dev pkg-config \
 && docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install pdo_mysql zip gd \
 && a2enmod rewrite \
 && a2dismod mpm_event mpm_worker || true \
 && a2enmod mpm_prefork \
 && rm -rf /var/lib/apt/lists/*

# Install Node.js 20 (needed for Vite build)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
 && apt-get update && apt-get install -y nodejs \
 && node -v && npm -v \
 && rm -rf /var/lib/apt/lists/*

# MongoDB PHP extension (PINNED) - will compile WITH SSL now
RUN pecl install mongodb-1.21.2 \
 && docker-php-ext-enable mongodb \
 && php -r "echo 'MongoDB PHP extension: '.phpversion('mongodb').PHP_EOL;"

# Set Laravel public as docroot
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
 && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

WORKDIR /var/www/html
COPY . .

# Install Composer + dependencies
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
 && composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# Build frontend assets (creates public/build/manifest.json)
RUN npm ci && npm run build

# HARD CHECK: fail build if manifest missing
RUN ls -la public/build && test -f public/build/manifest.json

# Permissions + ensure Laravel session/cache dirs exist (prevents login loops)
RUN mkdir -p storage/app/public \
    storage/framework/sessions \
    storage/framework/views \
    storage/framework/cache \
 && chown -R www-data:www-data storage bootstrap/cache \
 && chmod -R 775 storage bootstrap/cache

# Storage link (safe if already exists)
RUN php artisan storage:link || true

EXPOSE 80
CMD ["apache2-foreground"]

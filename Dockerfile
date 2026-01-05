FROM php:8.2-cli

# ✅ Force rebuild
ARG CACHE_BUST=14
RUN echo "cache bust: $CACHE_BUST"

# Install PHP extensions (no Apache!)
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libpng-dev libonig-dev libxml2-dev \
    libssl-dev pkg-config \
    && docker-php-ext-install pdo pdo_mysql zip mbstring exif pcntl bcmath gd opcache \
    && pecl install mongodb-1.21.2 \
    && docker-php-ext-enable mongodb opcache \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY . .

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

# Set permissions and generate key
RUN chown -R www-data:www-data storage bootstrap/cache
RUN php artisan key:generate --force || true

# Optimize for production
RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

# Use PHP built-in server - NO APACHE ISSUES!
CMD php artisan serve --host=0.0.0.0 --port=${PORT}
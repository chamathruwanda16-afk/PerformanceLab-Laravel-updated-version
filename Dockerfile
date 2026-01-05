FROM php:8.2-apache

# ✅ Force rebuild when value changes
ARG CACHE_BUST=8
RUN echo "cache bust: $CACHE_BUST"

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libpng-dev libonig-dev libxml2-dev \
    libssl-dev pkg-config libcurl4-openssl-dev \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions for Laravel + MySQL
RUN docker-php-ext-install \
    pdo pdo_mysql \
    zip mbstring exif pcntl bcmath gd opcache \
    && docker-php-ext-enable opcache

# Install MongoDB extension
RUN pecl install mongodb-1.21.2 \
    && docker-php-ext-enable mongodb

# Enable Apache modules
RUN a2enmod rewrite headers
RUN a2enmod php8.2

# Configure Apache MPM prefork (required for PHP module)
RUN a2dismod mpm_event mpm_worker || true \
 && a2enmod mpm_prefork

# Set Laravel public folder as document root
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/*.conf \
    /etc/apache2/apache2.conf \
    /etc/apache2/conf-available/*.conf

# Configure Apache to use Railway's PORT
RUN echo 'Listen ${PORT}' > /etc/apache2/ports.conf \
 && echo '<IfModule ssl_module>' >> /etc/apache2/ports.conf \
 && echo '  Listen 443' >> /etc/apache2/ports.conf \
 && echo '</IfModule>' >> /etc/apache2/ports.conf

# Update Apache default site to use dynamic port
RUN sed -i 's/<VirtualHost \*:80>/<VirtualHost \*:${PORT}>/g' /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www/html

# Copy Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy application files
COPY . .

# Install dependencies (skip dev in production)
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress

# Generate Laravel key if not exists
RUN php artisan key:generate --force || true

# Set proper permissions
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Configure PHP for production
COPY php.ini /usr/local/etc/php/conf.d/php.ini

# Configure Apache logs
RUN echo "ErrorLog /proc/self/fd/2" >> /etc/apache2/apache2.conf \
 && echo "CustomLog /proc/self/fd/1 combined" >> /etc/apache2/apache2.conf \
 && echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Start Apache with Railway's PORT
CMD sed -i "s/\${PORT}/${PORT}/g" /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf && \
    docker-php-entrypoint apache2-foreground
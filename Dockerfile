FROM php:8.2-apache

# ✅ Force rebuild when value changes
ARG CACHE_BUST=10
RUN echo "cache bust: $CACHE_BUST"

# First, disable ALL MPM modules completely
RUN a2dismod mpm_event mpm_worker mpm_prefork || true

# Remove any MPM configuration files
RUN rm -f /etc/apache2/mods-enabled/mpm_*.load \
    /etc/apache2/mods-enabled/mpm_*.conf || true

# Now enable ONLY mpm_prefork
RUN a2enmod mpm_prefork

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

# Install and enable MongoDB extension
RUN pecl install mongodb-1.21.2 \
    && echo "extension=mongodb.so" > /usr/local/etc/php/conf.d/mongodb.ini

# Enable Apache modules (AFTER MPM is configured)
RUN a2enmod rewrite headers

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

# Install dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress

# Generate Laravel key if not exists
RUN php artisan key:generate --force || true

# Set proper permissions
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Configure PHP for production
RUN echo "memory_limit = 256M" >> /usr/local/etc/php/conf.d/custom.ini \
 && echo "upload_max_filesize = 64M" >> /usr/local/etc/php/conf.d/custom.ini \
 && echo "post_max_size = 64M" >> /usr/local/etc/php/conf.d/custom.ini \
 && echo "max_execution_time = 300" >> /usr/local/etc/php/conf.d/custom.ini \
 && echo "opcache.enable=1" >> /usr/local/etc/php/conf.d/custom.ini \
 && echo "opcache.memory_consumption=128" >> /usr/local/etc/php/conf.d/custom.ini \
 && echo "display_errors=Off" >> /usr/local/etc/php/conf.d/custom.ini

# Configure Apache logs
RUN echo "ErrorLog /proc/self/fd/2" >> /etc/apache2/apache2.conf \
 && echo "CustomLog /proc/self/fd/1 combined" >> /etc/apache2/apache2.conf \
 && echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Verify only one MPM is enabled (debug step)
RUN echo "=== Checking MPM modules ===" && ls -la /etc/apache2/mods-enabled/mpm_* 2>/dev/null || echo "No MPM modules found"

# Start Apache with Railway's PORT
CMD sed -i "s/\${PORT}/${PORT}/g" /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf && \
    docker-php-entrypoint apache2-foreground
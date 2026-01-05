FROM php:8.2-apache

# ✅ Force rebuild
ARG CACHE_BUST=11
RUN echo "cache bust: $CACHE_BUST"

# Force ONLY mpm_prefork by editing apache2.conf directly
RUN echo "" > /etc/apache2/mods-available/mpm_event.load \
 && echo "" > /etc/apache2/mods-available/mpm_worker.load \
 && echo "LoadModule mpm_prefork_module /usr/lib/apache2/modules/mod_mpm_prefork.so" > /etc/apache2/mods-available/mpm_prefork.load

# Disable all, then enable prefork
RUN a2dismod mpm_event mpm_worker mpm_prefork 2>/dev/null || true
RUN a2enmod mpm_prefork

# Install dependencies
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libpng-dev libonig-dev libxml2-dev \
    libssl-dev pkg-config \
    && docker-php-ext-install pdo pdo_mysql zip mbstring exif pcntl bcmath gd opcache \
    && pecl install mongodb-1.21.2 \
    && echo "extension=mongodb.so" > /usr/local/etc/php/conf.d/mongodb.ini \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

# Set document root
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/*.conf \
    /etc/apache2/apache2.conf \
    /etc/apache2/conf-available/*.conf

WORKDIR /var/www/html

# Copy and install
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY . .
RUN composer install --no-dev --optimize-autoloader
RUN chown -R www-data:www-data storage bootstrap/cache

# Use Railway PORT
CMD sed -i "s/80/${PORT}/g" /etc/apache2/ports.conf /etc/apache2/sites-available/*.conf && \
    docker-php-entrypoint apache2-foreground
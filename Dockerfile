FROM php:8.2-apache

# 1) System deps + PHP extensions
RUN apt-get update && apt-get install -y \
    git unzip zip libzip-dev libpng-dev libjpeg-dev libfreetype6-dev \
    libonig-dev libxml2-dev curl gnupg \
  && docker-php-ext-configure gd --with-freetype --with-jpeg \
  && docker-php-ext-install pdo_mysql zip gd \
  && a2enmod rewrite \
  && rm -rf /var/lib/apt/lists/*

# 2) MongoDB PHP extension (fixes your composer ext-mongodb error)
RUN pecl install mongodb-1.21.2 \
    && docker-php-ext-enable mongodb

    

# 3) Set Laravel public as Apache docroot
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
 && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

WORKDIR /var/www/html

# 4) Copy app
COPY . .

# 5) Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# 6) Install PHP deps (no-dev for production)
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# 7) Permissions for Laravel
RUN chown -R www-data:www-data storage bootstrap/cache \
 && chmod -R 775 storage bootstrap/cache

EXPOSE 80
CMD ["apache2-foreground"]

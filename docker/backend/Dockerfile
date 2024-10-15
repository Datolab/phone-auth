# docker/php/Dockerfile

FROM php:7.4-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy backend source code
COPY . /var/www/html

# Install PHP dependencies
RUN composer install --no-interaction --optimize-autoloader

# Ensure storage directory exists and has correct permissions
RUN mkdir -p storage \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 storage

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]
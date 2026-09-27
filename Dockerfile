FROM php:8.3-fpm-alpine

# Install system dependencies & PHP extensions for Laravel, SQLite, MySQL, and GD
RUN apk add --no-cache \
    curl \
    git \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip \
    sqlite \
    sqlite-dev \
    libzip-dev \
    oniguruma-dev

RUN docker-php-ext-install pdo pdo_sqlite pdo_mysql mbstring exif pcntl bcmath gd zip

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . .

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/bootstrap/cache \
    && touch database/database.sqlite \
    && chmod 777 database/database.sqlite \
    && chmod 777 database

EXPOSE 8000

CMD sh -c "composer install --no-interaction && php artisan key:generate --force && php artisan migrate --seed --force && php artisan serve --host=0.0.0.0 --port=8000"

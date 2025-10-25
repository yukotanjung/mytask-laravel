# Gunakan image resmi PHP 8.2 dengan FPM (FastCGI Process Manager) sebagai base image.
# FPM cocok untuk production karena akan di-handle oleh web server seperti Nginx.
FROM php:8.2-fpm

# Set direktori kerja di dalam container
WORKDIR /var/www

# Instal dependensi sistem yang dibutuhkan oleh Laravel dan ekstensi PHP.
# - lib...-dev: library untuk kompilasi ekstensi PHP seperti GD.
# - zip, unzip, git, curl: tools umum yang sering dibutuhkan.
# - libonig-dev, libxml2-dev, libzip-dev: dependensi untuk ekstensi mbstring dan zip.
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    unzip \
    git \
    curl \
    libonig-dev \
    libxml2-dev \
    libzip-dev


RUN apt-get clean && rm -rf /var/lib/apt/lists/*


RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip


RUN pecl install mongodb-1.19.2 && docker-php-ext-enable mongodb


COPY --from=composer:latest /usr/bin/composer /usr/bin/composer


COPY composer.json ./


RUN composer install --no-ansi --no-dev --no-interaction --no-scripts --optimize-autoloader


COPY . .


RUN chown -R www-data:www-data storage bootstrap/cache


EXPOSE 9000


CMD ["php", "artisan", "serve", "--host=0.0.0.0"]




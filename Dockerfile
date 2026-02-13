FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg62-turbo-dev \
    libwebp-dev \
    libfreetype6-dev \
    libzip-dev \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install gd pdo pdo_mysql mysqli zip opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN a2enmod rewrite headers expires deflate

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . /var/www/html/

WORKDIR /var/www/html

RUN composer install --no-dev --optimize-autoloader --no-interaction

RUN mkdir -p uploads/products uploads/gallery uploads/activities uploads/content logs cache \
    && chown -R www-data:www-data uploads logs cache config \
    && chmod -R 775 uploads logs cache \
    && chmod -R 755 config

RUN echo "upload_max_filesize = 10M\n\
post_max_size = 10M\n\
max_execution_time = 300\n\
max_input_time = 300\n\
memory_limit = 256M" > /usr/local/etc/php/conf.d/custom.ini

EXPOSE 80

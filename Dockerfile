FROM php:8.2-apache

# Install PDO MySQL extension and other dependencies
RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libwebp-dev \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) gd pdo pdo_mysql zip

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Enable Apache headers module (for security headers in .htaccess)
RUN a2enmod headers

# Enable Apache expires module (for caching in .htaccess)
RUN a2enmod expires

# Set working directory
WORKDIR /var/www/html

# Copy the application files to the container
COPY . /var/www/html/

# Set proper permissions for the uploads directory
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# The standard apache2-foreground command will run by default

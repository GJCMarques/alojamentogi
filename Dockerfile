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
    default-mysql-client \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) gd pdo pdo_mysql zip

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Enable Apache headers module (for security headers in .htaccess)
RUN a2enmod headers

# Enable Apache expires module (for caching in .htaccess)
RUN a2enmod expires

# Security lockdown: Bloquear acesso direto HTTP a pastas e ficheiros sensíveis
# mesmo que o .htaccess seja ignorado ou apagado acidentalmente
RUN { \
    echo '<DirectoryMatch "^/var/www/html/(config|core|models|includes|templates|database|logs|vendor)/">'; \
    echo '    Require all denied'; \
    echo '</DirectoryMatch>'; \
    echo '<FilesMatch "(^\.|\.(env|sql|md|json|lock|key|yml|yaml|log))$">'; \
    echo '    Require all denied'; \
    echo '</FilesMatch>'; \
} > /etc/apache2/conf-available/security-lockdown.conf \
    && a2enconf security-lockdown

# Set working directory
WORKDIR /var/www/html

# Copy the application files to the container
COPY . /var/www/html/

# Set proper permissions for the uploads directory
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# The standard apache2-foreground command will run by default

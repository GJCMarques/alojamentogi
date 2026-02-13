#!/bin/bash
set -e

# Create upload directories if they don't exist
mkdir -p /var/www/html/uploads/products \
         /var/www/html/uploads/gallery \
         /var/www/html/uploads/activities \
         /var/www/html/uploads/content \
         /var/www/html/uploads/heroes \
         /var/www/html/uploads/media \
         /var/www/html/uploads/accommodation \
         /var/www/html/logs \
         /var/www/html/cache

# Fix permissions
chown -R www-data:www-data /var/www/html/uploads \
                           /var/www/html/logs \
                           /var/www/html/cache \
                           /var/www/html/config

chmod -R 775 /var/www/html/uploads \
             /var/www/html/logs \
             /var/www/html/cache

# Start Apache
exec apache2-foreground

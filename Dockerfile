# Use PHP 8.2 for consistency
FROM composer:2.5 AS build
WORKDIR /app
COPY composer.json composer.lock* ./
RUN composer install --no-dev --optimize-autoloader --no-interaction
COPY . /app
RUN composer dump-autoload --optimize

# Use PHP 8.2 Apache
FROM php:8.2-apache
WORKDIR /var/www/html

# Install extensions
RUN docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy from build stage
COPY --from=build --chown=www-data:www-data /app /var/www/html

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Expose port
EXPOSE 8080

# Start Apache
CMD ["apache2-foreground"]

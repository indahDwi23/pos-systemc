# Use PHP 8.2 Composer image
FROM composer:2.5 AS build
WORKDIR /app
COPY . /app/
RUN composer update --no-dev --optimize-autoloader --no-interaction --no-scripts

# Use PHP 8.2 Apache
FROM php:8.2-apache
WORKDIR /var/www/html

# Install system dependencies & PHP extensions
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    pdo \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    && rm -rf /var/lib/apt/lists/*

# Fix Apache MPM - BEFORE any other Apache config
RUN rm -f /etc/apache2/mods-enabled/mpm_event.load \
    && rm -f /etc/apache2/mods-available/mpm_event.load \
    && a2enmod mpm_prefork \
    && a2enmod rewrite

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

FROM composer:2.5 as build
WORKDIR /app
COPY . /app/
RUN composer install --no-dev --optimize-autoloader

FROM php:8.2-apache
WORKDIR /var/www/html
COPY --from=build /app /var/www/html
RUN chown -R www-data:www-data /var/www/html \
    && a2enmod rewrite
RUN php artisan migrate --force \
    && php artisan db:seed --force \
    && php artisan key:generate \
    && php artisan storage:link
EXPOSE 8080
CMD ["apache2-foreground"]

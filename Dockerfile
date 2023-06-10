FROM php:8.0-apache

# .htaccess rewrite
RUN a2enmod rewrite

# permissions to write to database file
RUN mkdir /var/www/database/
RUN chown -R www-data:www-data /var/www/
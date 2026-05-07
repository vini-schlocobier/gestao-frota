FROM php:8.2-apache

RUN docker-php-ext-install pdo pdo_mysql
RUN a2enmod rewrite

COPY . /var/www/html

RUN sed -ri -e 's!DocumentRoot /var/www/html!DocumentRoot /var/www/html/public!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!<Directory /var/www/html>!<Directory /var/www/html/public>!g' /etc/apache2/sites-available/*.conf \
    && chown -R www-data:www-data /var/www/html

WORKDIR /var/www/html

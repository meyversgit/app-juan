FROM php:8.2-apache

# Extensión necesaria para que PDO pueda conectarse a MySQL en Railway.
RUN docker-php-ext-install pdo_mysql \
    && a2enmod rewrite

# El proyecto PHP está en la raíz del contenedor Apache.
COPY . /var/www/html/

RUN chown -R www-data:www-data /var/www/html

EXPOSE 80

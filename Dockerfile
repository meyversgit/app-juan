FROM php:8.2-apache

# Instalar PDO para MySQL
RUN docker-php-ext-install pdo_mysql

# Dejar solamente un MPM de Apache: prefork
RUN rm -f /etc/apache2/mods-enabled/mpm_*.load \
    && rm -f /etc/apache2/mods-enabled/mpm_*.conf \
    && a2enmod mpm_prefork \
    && a2enmod rewrite

# Copiar el proyecto
COPY . /var/www/html/

# Permisos
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80

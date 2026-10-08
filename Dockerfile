FROM php:8.2-apache

# Instalar PDO MySQL y asegurar que Apache use un solo MPM.
RUN docker-php-ext-install pdo_mysql \
    && a2dismod mpm_event mpm_worker 2>/dev/null || true \
    && a2enmod mpm_prefork \
    && a2enmod rewrite

# Copiar el proyecto PHP.
COPY . /var/www/html/

# Permisos.
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80

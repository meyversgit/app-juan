
FROM php:8.2-apache

# Instalar el controlador de MySQL para PHP
RUN docker-php-ext-install pdo_mysql

# Eliminar las configuraciones MPM habilitadas
# y activar únicamente mpm_prefork
RUN find /etc/apache2/mods-enabled -maxdepth 1 \
      -type l -name 'mpm_*.load' -delete \
    && find /etc/apache2/mods-enabled -maxdepth 1 \
      -type l -name 'mpm_*.conf' -delete \
    && a2enmod mpm_prefork rewrite \
    && echo "=== MPM activos ===" \
    && find /etc/apache2/mods-enabled -maxdepth 1 \
      -name 'mpm_*.load' -printf '%f\n' \
    && apache2ctl -M

# Copiar el proyecto
COPY . /var/www/html/

# Establecer permisos
RUN chown -R www-data:www-data /var/www/html

# Puerto de Apache
EXPOSE 80

# Iniciar Apache
CMD ["apache2-foreground"]

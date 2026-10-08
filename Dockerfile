
FROM php:8.2-apache

RUN docker-php-ext-install pdo_mysql

# Eliminar todas las configuraciones MPM habilitadas
RUN find /etc/apache2/mods-enabled -maxdepth 1 \
      \( -name 'mpm_*.load' -o -name 'mpm_*.conf' \) \
      -delete \
    && a2enmod mpm_prefork rewrite \
    && test "$(find /etc/apache2/mods-enabled -maxdepth 1 -name 'mpm_*.load' | wc -l)" -eq 1 \
    && apache2ctl -M

COPY . /var/www/html/

RUN chown -R www-data:www-data /var/www/html

EXPOSE 80

CMD ["apache2-foreground"]

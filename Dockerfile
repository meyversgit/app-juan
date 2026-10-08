
FROM php:8.2-apache

RUN docker-php-ext-install pdo_mysql

# Limpiar todos los MPM habilitados y activar solo prefork
RUN for f in /etc/apache2/mods-enabled/mpm_*.load /etc/apache2/mods-enabled/mpm_*.conf; do \
      [ ! -e "$f" ] || rm -f "$f"; \
    done \
    && a2enmod mpm_prefork rewrite \
    && apache2ctl -M 2>&1 | grep mpm

COPY . /var/www/html/

RUN chown -R www-data:www-data /var/www/html

EXPOSE 80

CMD ["apache2-foreground"]

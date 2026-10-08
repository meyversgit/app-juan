
FROM php:8.2-apache

RUN docker-php-ext-install pdo_mysql

RUN for f in /etc/apache2/mods-enabled/mpm_*.load /etc/apache2/mods-enabled/mpm_*.conf; do \
      [ ! -e "$f" ] || rm -f "$f"; \
    done \
    && a2enmod mpm_prefork rewrite

COPY . /var/www/html/

RUN chown -R www-data:www-data /var/www/html

EXPOSE 8080

CMD ["bash", "-c", "PORT=${PORT:-8080}; sed -i 's/^Listen 80$/Listen 8080/' /etc/apache2/ports.conf; sed -i 's/<VirtualHost \\*:80>/<VirtualHost *:8080>/' /etc/apache2/sites-available/000-default.conf; exec apache2-foreground"]

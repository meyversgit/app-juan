
FROM php:8.2-apache

RUN docker-php-ext-install pdo_mysql

# Comprobar qué MPM está habilitado en la imagen base
RUN echo "=== MPM originales ===" \
    && ls -l /etc/apache2/mods-enabled/mpm_* || true

# Deshabilitar los MPM disponibles y habilitar solo prefork
RUN a2dismod mpm_event mpm_worker mpm_prefork || true
RUN a2enmod mpm_prefork rewrite

# Verificar que solo quede un MPM habilitado
RUN echo "=== MPM finales ===" \
    && ls -l /etc/apache2/mods-enabled/mpm_* \
    && apache2ctl -M

COPY . /var/www/html/

RUN chown -R www-data:www-data /var/www/html

EXPOSE 80

CMD ["apache2-foreground"]

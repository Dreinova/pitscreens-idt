# IDT App — imagen para el servidor de producción (detrás de Traefik).
# Sirve app/ y admin/ como subcarpetas del docroot, igual que en XAMPP local.

FROM php:8.2-apache

RUN docker-php-ext-install mysqli curl gd \
    && a2enmod rewrite headers

COPY . /var/www/html/

RUN mkdir -p /var/www/html/admin/assets/galeria \
    && chown -R www-data:www-data /var/www/html/admin/assets/galeria

EXPOSE 80

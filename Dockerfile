# IDT App — imagen para el servidor de producción (detrás de Traefik).
# Sirve app/ y admin/ como subcarpetas del docroot, igual que en XAMPP local.

FROM php:8.2-apache

RUN apt-get update && apt-get install -y --no-install-recommends \
        libcurl4-openssl-dev \
        libpng-dev \
        libjpeg62-turbo-dev \
        libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install mysqli curl gd \
    && a2enmod rewrite headers \
    && rm -rf /var/lib/apt/lists/*

# Permite que el .htaccess de la raíz del proyecto controle las URLs
# amigables (por defecto Apache ignora .htaccess con AllowOverride None).
RUN printf '<Directory /var/www/html>\n\tAllowOverride All\n</Directory>\n' \
        > /etc/apache2/conf-available/allow-override.conf \
    && a2enconf allow-override

COPY . /var/www/html/

RUN mkdir -p /var/www/html/admin/assets/galeria \
    && chown -R www-data:www-data /var/www/html/admin/assets/galeria

EXPOSE 80

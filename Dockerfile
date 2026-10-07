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

# Por defecto PHP solo permite subir 2MB por archivo / 8MB por petición —
# muy por debajo del límite de 80MB que la app anuncia (admin/contenido.php,
# editar_configuracion.php). Sin esto, cualquier subida real excede
# post_max_size, PHP descarta la petición completa con una advertencia que
# sale ANTES de que el script corra, y eso rompe session_start()/header()
# igual que el bug del "?>" sobrante — mismo síntoma, causa distinta.
RUN { \
        echo 'upload_max_filesize = 90M'; \
        echo 'post_max_size = 100M'; \
        echo 'memory_limit = 256M'; \
        echo 'max_execution_time = 300'; \
        echo 'max_input_time = 300'; \
    } > /usr/local/etc/php/conf.d/uploads.ini

COPY . /var/www/html/

RUN mkdir -p /var/www/html/admin/assets/galeria \
    && chown -R www-data:www-data /var/www/html/admin/assets/galeria

EXPOSE 80

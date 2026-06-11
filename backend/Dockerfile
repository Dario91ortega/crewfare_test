# Imagen base con PHP 8.4 (CLI). Laravel 13 requiere PHP ^8.3.
FROM php:8.4-cli

# Dependencias del sistema necesarias para las extensiones de PHP.
RUN apt-get update && apt-get install -y \
        git \
        unzip \
        libonig-dev \
        libzip-dev \
    && rm -rf /var/lib/apt/lists/*

# Extensiones de PHP que Laravel + MySQL necesitan.
RUN docker-php-ext-install pdo_mysql mbstring bcmath zip

# Composer (copiado desde la imagen oficial) por si quieres instalar paquetes
# o correr "composer ..." dentro del contenedor durante la entrevista.
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Instalamos las dependencias de Composer DENTRO de la imagen para que el repo
# funcione al clonarlo (vendor/ está en .gitignore). En tiempo de ejecución, el
# código se monta desde ./backend y vendor/ se preserva con un volumen (ver
# docker-compose.yml). --no-scripts evita ejecutar artisan en el build.
COPY composer.json composer.lock ./
RUN composer install --no-scripts --no-interaction --no-progress --prefer-dist

EXPOSE 8000

# Servidor de desarrollo de Laravel, accesible desde otros contenedores (Traefik).
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]

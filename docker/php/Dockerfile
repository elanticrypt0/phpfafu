# ./docker/php/Dockerfile
FROM dunglas/frankenphp:php8.2-alpine

# Instalar dependencias del sistema
RUN apk add --no-cache \
    git \
    zip \
    unzip \
    libzip-dev \
    $PHPIZE_DEPS

# Instalar y habilitar extensiones PHP
RUN install-php-extensions \
    pdo \
    pdo_mysql \
    zip \
    xdebug

# Configurar Xdebug
RUN echo "xdebug.mode=develop,debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar PHP
COPY ./docker/php/php.ini /usr/local/etc/php/conf.d/custom.ini

# Configurar Caddyfile para FrankenPHP
COPY ./docker/php/Caddyfile /etc/frankenphp/Caddyfile

# Crear directorio para la aplicación
RUN mkdir -p /var/www/html \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

WORKDIR /var/www/html
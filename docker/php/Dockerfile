FROM php:8.1-fpm-alpine

WORKDIR /var/www/html

RUN apk add --no-cache \
    bash \
    icu-dev \
    oniguruma-dev \
    libzip-dev \
    unzip \
    zip \
    && docker-php-ext-install pdo pdo_mysql mbstring intl zip

# Composer binary (optional, useful for installing dependencies in container)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

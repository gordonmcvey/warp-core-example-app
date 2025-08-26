FROM php:8.4-fpm-alpine

RUN docker-php-ext-install opcache \
    && docker-php-ext-enable opcache

ENV DOCKER_ALLOW_SUPERUSER=1

COPY --from=composer:2.8 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY ./app/config/ ./config
COPY ./app/src/ ./src
COPY ./app/public ./public
COPY ./app/composer.json ./

RUN composer install  \
    --prefer-dist  \
    --no-dev  \
    --no-scripts  \
    --no-progress  \
    --no-interaction  \
    --optimize-autoloader

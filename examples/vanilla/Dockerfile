FROM php:8.4-fpm-alpine

RUN docker-php-ext-install opcache \
    && docker-php-ext-enable opcache

ENV DOCKER_ALLOW_SUPERUSER=1

COPY --from=composer:2.8 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Install just the dependencies for now
# (see https://medium.com/@softius/faster-docker-builds-with-composer-install-b4d2b15d0fff)
COPY ./app/composer.json ./
RUN composer install  \
    --prefer-dist  \
    --no-dev  \
    --no-scripts  \
    --no-progress  \
    --no-interaction

# Install the actual app
COPY ./app/src/ ./src
COPY ./app/public ./public
RUN composer dump-autoload --optimize

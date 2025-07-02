FROM php:8.1-cli

RUN apt-get update && apt-get install -y \
    git \
    unzip

RUN pecl install xdebug && docker-php-ext-enable xdebug

COPY --from=composer:2.8 /usr/bin/composer /usr/bin/composer

WORKDIR /usr/src/app

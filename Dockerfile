FROM composer:2 AS composer

FROM dunglas/frankenphp:1.12.7-php8.4@sha256:ed52b8e414756b0cd12144f16cedbf38bd1c5ede24c9837a8d4a80a4e946c86a AS base

RUN install-php-extensions \
    pdo_mysql \
    pdo_pgsql \
    pdo_sqlite \
    intl \
    zip \
    opcache

COPY --from=composer /usr/bin/composer /usr/bin/composer

WORKDIR /app

FROM base AS vendor

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

FROM base AS dev

RUN install-php-extensions xdebug
COPY docker/php.ini /usr/local/etc/php/conf.d/99-app.ini
COPY docker/xdebug.ini /usr/local/etc/php/conf.d/99-xdebug.ini

COPY composer.json composer.lock ./
RUN composer install --no-scripts --no-autoloader --prefer-dist

COPY . .
RUN composer dump-autoload

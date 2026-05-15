# syntax=docker/dockerfile:1.7

ARG FRANKENPHP_VERSION=1.12.2
ARG PHP_VERSION=8.5

FROM dunglas/frankenphp:${FRANKENPHP_VERSION}-php${PHP_VERSION} AS php-base

WORKDIR /app

RUN install-php-extensions \
    bcmath \
    exif \
    gd \
    intl \
    opcache \
    pcntl \
    pdo_mysql \
    redis \
    zip

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

FROM oven/bun:1 AS assets

WORKDIR /app

COPY package.json bun.lock ./
RUN bun install --frozen-lockfile

COPY resources ./resources
COPY public ./public
COPY vite.config.* ./
RUN bun run build

FROM php-base AS vendor

ENV COMPOSER_ALLOW_SUPERUSER=1

COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --prefer-dist \
    --no-interaction \
    --no-progress \
    --no-scripts \
    --optimize-autoloader

COPY . .
RUN composer dump-autoload --optimize \
    && php artisan package:discover --ansi

FROM php-base AS production

ENV APP_ENV=production \
    APP_DEBUG=false \
    LOG_CHANNEL=stderr \
    OCTANE_SERVER=frankenphp \
    FRANKENPHP_CONFIG="worker ./public/index.php"

COPY --from=vendor --chown=www-data:www-data /app /app
COPY --from=assets --chown=www-data:www-data /app/public/build /app/public/build

RUN mkdir -p storage/app storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R ug+rwX storage bootstrap/cache

USER www-data

EXPOSE 8000

CMD ["php", "artisan", "octane:frankenphp", "--host=0.0.0.0", "--port=8000", "--workers=auto", "--max-requests=500"]

# syntax=docker/dockerfile:1.7

ARG FRANKENPHP_VERSION=1.12.2
ARG PHP_VERSION=8.5

FROM docker.io/dunglas/frankenphp:${FRANKENPHP_VERSION}-php${PHP_VERSION} AS php-base

WORKDIR /app

RUN install-php-extensions \
    bcmath \
    exif \
    gd \
    intl \
    pcntl \
    pdo_mysql \
    redis \
    zip

COPY --from=docker.io/composer:2 /usr/bin/composer /usr/bin/composer

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

FROM docker.io/oven/bun:1 AS assets

WORKDIR /app

COPY package.json bun.lock ./
RUN bun install --frozen-lockfile

COPY --from=vendor /app/vendor ./vendor
COPY app/Filament ./app/Filament
COPY resources ./resources
COPY public ./public
COPY vite.config.* ./
RUN bun run build

FROM php-base AS production

ENV APP_ENV=production \
    APP_DEBUG=false \
    LOG_CHANNEL=stderr \
    OCTANE_SERVER=frankenphp \
    FRANKENPHP_CONFIG="worker ./public/index.php" \
    XDG_CONFIG_HOME=/app/storage/frankenphp/config \
    XDG_DATA_HOME=/app/storage/frankenphp/data

COPY --from=vendor --chown=www-data:www-data /app /app
COPY --from=assets --chown=www-data:www-data /app/public/build /app/public/build

RUN mkdir -p storage/app storage/framework/cache storage/framework/sessions storage/framework/views storage/logs storage/frankenphp/config storage/frankenphp/data bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R ug+rwX storage bootstrap/cache

USER www-data

EXPOSE 8000

CMD ["sh", "-lc", "mkdir -p \"$XDG_CONFIG_HOME\" \"$XDG_DATA_HOME\" && exec php artisan octane:frankenphp --host=0.0.0.0 --port=8000 --workers=auto --max-requests=500 --log-level=error --caddyfile=/app/docker/frankenphp/Caddyfile"]

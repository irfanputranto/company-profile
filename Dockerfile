# syntax=docker/dockerfile:1.7

ARG PHP_VERSION=8.2
ARG FRANKENPHP_VERSION=1
ARG NODE_VERSION=22

FROM node:${NODE_VERSION}-bookworm-slim AS frontend

WORKDIR /build

ENV NPM_CONFIG_AUDIT=false \
    NPM_CONFIG_FUND=false

COPY package.json package-lock.json ./
RUN npm ci

COPY resources ./resources
COPY app ./app
COPY public ./public
COPY vite.config.js ./vite.config.js

RUN npm run build

FROM dunglas/frankenphp:${FRANKENPHP_VERSION}-php${PHP_VERSION}-bookworm AS php-base

ARG APP_UID=1000
ARG APP_GID=1000

ENV COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_HOME=/tmp/composer \
    SERVER_NAME=:8080 \
    PHP_MEMORY_LIMIT=1024M \
    PHP_MAX_EXECUTION_TIME=600 \
    PHP_MAX_INPUT_TIME=600 \
    PHP_MAX_INPUT_VARS=100000 \
    PHP_MAX_FILE_UPLOADS=100 \
    PHP_POST_MAX_SIZE=512M \
    PHP_UPLOAD_MAX_FILESIZE=100M

WORKDIR /app

RUN apt-get update \
    && apt-get install -y --no-install-recommends ca-certificates \
    && install-php-extensions pdo_mysql pdo_sqlite bcmath intl zip gd exif opcache pcntl redis \
    && rm -rf /var/lib/apt/lists/* \
    && groupadd --gid "${APP_GID}" app \
    && useradd --uid "${APP_UID}" --gid app --create-home --shell /usr/sbin/nologin app \
    && mkdir -p /app /data /config \
    && chown -R app:app /app /data /config

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY docker/Caddyfile /etc/frankenphp/Caddyfile
COPY docker/entrypoint.sh /usr/local/bin/app-entrypoint
RUN chmod 755 /usr/local/bin/app-entrypoint

FROM php-base AS production-vendor

COPY composer.json composer.lock ./
RUN composer install \
        --no-dev \
        --no-interaction \
        --no-progress \
        --prefer-dist \
        --classmap-authoritative \
        --no-scripts

FROM php-base AS production

ENV APP_ENV=production \
    APP_DEBUG=false

COPY docker/php/production.ini ${PHP_INI_DIR}/conf.d/zz-app.ini
COPY --chown=app:app . .
COPY --from=production-vendor --chown=app:app /app/vendor ./vendor
COPY --from=frontend --chown=app:app /build/public/build ./public/build

RUN composer dump-autoload --no-dev --classmap-authoritative --no-interaction \
    && composer check-platform-reqs --no-dev \
    && mkdir -p storage/app/private storage/app/public storage/framework/cache/data \
        storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && chown -R app:app storage bootstrap/cache

USER app

EXPOSE 8080
STOPSIGNAL SIGTERM

ENTRYPOINT ["app-entrypoint"]
CMD ["frankenphp", "run", "--config", "/etc/frankenphp/Caddyfile"]

FROM php-base AS developer

ENV APP_ENV=local \
    APP_DEBUG=true

USER root

RUN apt-get update \
    && apt-get install -y --no-install-recommends git unzip \
    && rm -rf /var/lib/apt/lists/* \
    && cp "${PHP_INI_DIR}/php.ini-development" "${PHP_INI_DIR}/php.ini"

COPY docker/php/development.ini ${PHP_INI_DIR}/conf.d/zz-app.ini
COPY --chown=app:app . .

RUN composer install \
        --no-interaction \
        --no-progress \
        --prefer-dist \
    && mkdir -p storage/app/private storage/app/public storage/framework/cache/data \
        storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && chown -R app:app storage bootstrap/cache vendor

USER app

EXPOSE 8080
STOPSIGNAL SIGTERM

ENTRYPOINT ["app-entrypoint"]
CMD ["frankenphp", "run", "--config", "/etc/frankenphp/Caddyfile"]

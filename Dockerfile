# syntax=docker/dockerfile:1

# Production image: nginx + PHP-FPM in one container (serversideup/php).
# Listens on 8080. Migrations and Laravel caches run on start (AUTORUN_*).

# ---------------------------------------------------------------------------
# base: PHP runtime shared by all stages
# ---------------------------------------------------------------------------
FROM serversideup/php:8.3-fpm-nginx AS base

ENV AUTORUN_ENABLED=true \
    PHP_OPCACHE_ENABLE=1 \
    SHOW_WELCOME_MESSAGE=false

# ---------------------------------------------------------------------------
# vendor: composer dependencies (cached separately from the source code)
# ---------------------------------------------------------------------------
FROM base AS vendor

COPY --chown=www-data:www-data composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction

# ---------------------------------------------------------------------------
# assets: Vite build. vendor/ is needed because app.js imports Ziggy from it.
# ---------------------------------------------------------------------------
FROM node:22-alpine AS assets

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci --no-audit --no-fund

COPY . .
COPY --from=vendor /var/www/html/vendor ./vendor

ARG VITE_APP_NAME=laraPMS
ENV VITE_APP_NAME=$VITE_APP_NAME
RUN npm run build

# ---------------------------------------------------------------------------
# app: final image
# ---------------------------------------------------------------------------
FROM base AS app

COPY --chown=www-data:www-data --from=vendor /var/www/html/vendor ./vendor
COPY --chown=www-data:www-data . .
COPY --chown=www-data:www-data --from=assets /app/public/build ./public/build

RUN composer dump-autoload --optimize --no-dev --no-interaction

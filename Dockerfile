# syntax=docker/dockerfile:1


# ============================================================
# 1. Frontend build
# ============================================================
FROM node:22-alpine AS frontend

WORKDIR /app

COPY package.json package-lock.json ./

RUN npm ci

COPY . .

RUN npm run build


# ============================================================
# 2. PHP dependencies
# ============================================================
FROM composer:2 AS composer

WORKDIR /app

COPY composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --no-interaction \
    --no-progress \
    --prefer-dist \
    --optimize-autoloader


# ============================================================
# 3. Production PHP-FPM
# ============================================================
FROM php:8.4-fpm-alpine

WORKDIR /var/www/html

# System dependencies
RUN apk add --no-cache \
    icu-libs \
    libzip \
    oniguruma

# PHP extensions
RUN apk add --no-cache --virtual .build-deps \
        icu-dev \
        libzip-dev \
        oniguruma-dev \
    && docker-php-ext-install \
        bcmath \
        intl \
        mbstring \
        pdo_mysql \
        pcntl \
        zip \
    && apk del .build-deps

# Copy Composer dependencies
COPY --from=composer /app/vendor ./vendor

# Copy Laravel application
COPY . .

# Copy compiled frontend assets
COPY --from=frontend /app/public/build ./public/build

# Laravel writable directories
RUN mkdir -p \
        storage/framework/cache \
        storage/framework/sessions \
        storage/framework/views \
        storage/logs \
        bootstrap/cache \
    && chown -R www-data:www-data \
        storage \
        bootstrap/cache

EXPOSE 9000

CMD ["php-fpm"]

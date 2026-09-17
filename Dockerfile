# Stage 1: Build frontend assets
FROM node:18-alpine AS build

WORKDIR /app

# Copy package files and install dependencies
COPY package.json package-lock.json* ./
RUN npm install

# Copy source files and build
COPY . .
RUN npm run build

# Stage 2: Production PHP
FROM php:8.2-fpm-alpine AS production

# Install system dependencies
RUN apk add --no-cache \
    unzip \
    git \
    libzip-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    libwebp-dev \
    oniguruma-dev \
    zip \
    nodejs \
    npm

# Install PHP extensions
RUN docker-php-ext-install \
    bcmath \
    soap \
    gd \
    opcache \
    mbstring \
    pdo \
    pdo_mysql \
    zip

# Clear Laravel caches and install dependencies
WORKDIR /var/www/html
COPY --from=build /app/public /var/www/html/public
COPY --from=build /app/artisan /var/www/html/artisan
COPY --from=build /app/bootstrap /var/www/html/bootstrap
COPY --from=build /app/config /var/www/html/config
COPY --from=build /app/database /var/www/html/database
COPY --from=build /app/resources /var/www/html/resources
COPY --from=build /app/routes /var/www/html/routes
COPY --from=build /app/storage /var/www/html/storage

# Copy composer files
COPY composer.json composer.lock ./

# Set timezone and locale
RUN ln -s /usr/share/zoneinfo/Asia/Jakarta /etc/localtime \
    && echo 'LANG=fr_FR.UTF-8' > /etc/locale.gen \
    && locale-gen

# Clear cache and install dependencies
RUN composer clearcache || true \
    && composer install --no-dev --optimize-autoloader --no-interaction

# Copy source code
COPY . .
RUN composer dump-autoload --optimize --no-dev

# Set permissions
RUN mkdir -p storage/logs storage/framework/cache storage/framework/sessions storage/framework/testing storage/framework/views storage/framework/cache/data/storage/framework/cache/data \
    && chmod -R 775 storage bootstrap/cache

# Expose port 8000
EXPOSE 8000

# Default command
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]

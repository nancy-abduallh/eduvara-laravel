FROM php:8.2-fpm-alpine

# System deps + Node.js (for Vite asset build)
RUN apk add --no-cache \
    nginx \
    supervisor \
    curl \
    zip \
    unzip \
    git \
    libzip-dev \
    oniguruma-dev \
    postgresql-dev \
    nodejs \
    npm \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql mbstring zip opcache

WORKDIR /var/www/html

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1

COPY . .

COPY docker/ca.pem /etc/ssl/certs/aiven-ca.crt

# Storage dirs
RUN mkdir -p bootstrap/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/framework/cache \
    storage/logs \
    && chmod -R 775 storage bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

RUN composer install --no-dev --optimize-autoloader --no-interaction

# Build frontend assets so public/build/ exists in the image
# Using --no-audit --no-fund for faster install; no package-lock.json required
RUN npm install --no-audit --no-fund && npm run build && rm -rf node_modules

RUN cp .env.example .env \
    && php artisan key:generate

# Copy nginx + supervisor config
COPY docker/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/supervisord.conf /etc/supervisord.conf

EXPOSE 8080

# Supervisor runs both nginx (HTTP on 8080) and php-fpm (FastCGI on 9000)
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
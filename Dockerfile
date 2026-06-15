FROM php:8.3-cli-bookworm

ENV COMPOSER_ALLOW_SUPERUSER=1
ENV APP_ENV=production
ENV APP_DEBUG=false
ENV PORT=10000

WORKDIR /var/www/html

# Instalar dependencias del sistema + extensiones PHP
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    unzip \
    curl \
    nodejs \
    npm \
    libpq-dev \
    libzip-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libicu-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_pgsql \
        pgsql \
        zip \
        gd \
        opcache \
        intl \
    && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copiar proyecto
COPY . .

# Dependencias PHP
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress --no-scripts

# Dependencias Node + build
RUN npm ci
RUN npm run build

# Optimización Laravel
RUN php artisan package:discover --ansi \
    && php artisan optimize:clear \
    && php artisan config:cache \
    && php artisan view:cache \
    && php artisan route:cache || true

EXPOSE 10000

CMD sh -c "php artisan serve --host 0.0.0.0 --port ${PORT}"

FROM php:8.3-cli-bookworm

ENV COMPOSER_ALLOW_SUPERUSER=1
ENV APP_ENV=production
ENV APP_DEBUG=false
ENV PORT=10000

WORKDIR /var/www/html

# Dependencias del sistema
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    unzip \
    curl \
    gnupg \
    ca-certificates \
    libpq-dev \
    libzip-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libicu-dev \
    && rm -rf /var/lib/apt/lists/*

# Node.js 22
RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get update \
    && apt-get install -y nodejs \
    && node -v \
    && npm -v \
    && rm -rf /var/lib/apt/lists/*

# Extensiones PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_pgsql \
        pgsql \
        zip \
        gd \
        opcache \
        intl

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copiar proyecto
COPY . .

# Dependencias PHP
RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --no-progress \
    --no-scripts

# Dependencias Node y compilación Vite
RUN npm install

ENV NODE_OPTIONS=--max-old-space-size=2048

RUN npm run build

# Cache Laravel
RUN php artisan package:discover --ansi || true

RUN php artisan optimize:clear || true

RUN php artisan config:cache || true

RUN php artisan view:cache || true

RUN php artisan route:cache || true

EXPOSE 10000

CMD ["sh", "-c", "php artisan serve --host=0.0.0.0 --port=${PORT}"]

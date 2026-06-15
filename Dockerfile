# 1. Usamos la versión oficial con Apache para que sirva los assets de Filament (.css, .js)
FROM php:8.3-apache-bookworm

ENV COMPOSER_ALLOW_SUPERUSER=1
ENV APP_ENV=production
ENV APP_DEBUG=false
ENV PORT=10000
ENV NODE_OPTIONS=--max-old-space-size=2048

# 2. Configuramos Apache para que su raíz apunte a la carpeta 'public' de Laravel
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf
RUN sed -i 's|/var/www/|/var/www/html/public|g' /etc/apache2/apache2.conf
RUN a2enmod rewrite

# Cambiamos los puertos por defecto de Apache al 10000 que exige Render
RUN sed -i 's/Listen 80/Listen 10000/g' /etc/apache2/ports.conf
RUN sed -i 's/<VirtualHost \*:80>/<VirtualHost \*:10000>/g' /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www/html

# 3. Instalación de dependencias del sistema
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

# 4. Instalación de Node.js v22 para compilar Tailwind/Vite
RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get update \
    && apt-get install -y nodejs \
    && rm -rf /var/lib/apt/lists/*

# 5. Extensiones de PHP necesarias para Laravel y Filament
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_pgsql \
        pgsql \
        zip \
        gd \
        opcache \
        intl

# Copiamos Composer desde la imagen oficial
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copiamos todo el proyecto al contenedor
COPY . .

# 6. Creamos directorios de caché de Laravel y asignamos permisos al usuario de Apache (www-data)
RUN mkdir -p storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    bootstrap/cache \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# 7. Instalación de dependencias de PHP y compilación de assets (Vite/Tailwind)
RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --no-progress \
    --no-scripts

RUN npm install
RUN npm run build

# 8. Optimización de caché en Laravel
RUN php artisan package:discover --ansi || true
RUN php artisan optimize:clear || true
RUN php artisan config:cache || true
RUN php artisan view:cache || true
RUN php artisan filament:upgrade || true
RUN php artisan storage:link || true

EXPOSE 10000

# 9. Comando final: Iniciamos Apache directamente en primer plano (Sin pasar por entrypoint)
CMD ["apache2-foreground"]

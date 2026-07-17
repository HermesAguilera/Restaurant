#!/bin/sh
set -e

cd /var/www/html

mkdir -p storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    bootstrap/cache

chown -R www-data:www-data storage bootstrap/cache || true
chmod -R 775 storage bootstrap/cache || true

php artisan storage:link || true

if [ -n "${APP_KEY:-}" ]; then
    php artisan config:cache || true
    php artisan route:cache || true
    php artisan view:cache || true
    php artisan event:cache || true
    php artisan filament:cache-components || true
else
    echo "APP_KEY no definida: se omite el cacheo de configuración."
fi

run_migrations() {
    if [ "${RUN_SEEDERS_ON_STARTUP:-false}" = "true" ]; then
        php artisan migrate --seed --force --no-interaction
    else
        php artisan migrate --force --no-interaction
    fi
}

if [ "${RUN_MIGRATIONS_ON_STARTUP:-true}" = "true" ]; then
    echo "Running database migrations..."
    for attempt in 1 2 3 4 5; do
        if run_migrations; then
            echo "Migrations completed."
            break
        fi

        echo "Migration attempt ${attempt} failed, retrying..."
        if [ "$attempt" -eq 5 ]; then
            echo "Migrations failed after 5 attempts."
            exit 1
        fi

        sleep 5
    done
fi

exec "$@"

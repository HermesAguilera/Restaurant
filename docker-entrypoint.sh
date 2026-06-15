#!/bin/sh
set -e

mkdir -p storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    bootstrap/cache

chmod -R 775 storage bootstrap/cache || true

if [ -n "${APP_KEY:-}" ]; then
    php artisan config:cache || true
    php artisan view:cache || true
fi

run_migrations() {
    php artisan migrate --force --no-interaction
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

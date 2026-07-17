#!/usr/bin/env bash
# Despliegue de actualizaciones. Se ejecuta como el usuario `deploy` (sin root):
# solo el paso final necesita privilegios y va aislado en restaurante-postdeploy.
# Para la instalación inicial ver DEPLOY.md.
set -euo pipefail

APP_DIR="${APP_DIR:-/var/www/restaurante}"

cd "$APP_DIR"

php artisan down --retry=60 || true
restore_up() { php artisan up || true; }
trap restore_up EXIT

echo "==> Código"
git pull --ff-only

echo "==> Dependencias PHP"
composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# Hoy ninguna vista usa @vite y el tema de Filament se sirve desde public/css,
# así que este build no alimenta nada; se mantiene por si se añade Vite después.
echo "==> Assets"
npm ci
npm run build

echo "==> Migraciones"
php artisan migrate --force

echo "==> Cachés"
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
php artisan filament:cache-components
php artisan storage:link || true

echo "==> Permisos y recarga de PHP-FPM"
sudo /usr/local/bin/restaurante-postdeploy

php artisan up
trap - EXIT

echo "==> Despliegue completado"

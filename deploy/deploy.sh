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
# No view:cache: resources/views/filament/.../aperturar-caja.blade.php usa
# <x-filament::form> directo, y ese componente solo lo registra Filament dentro
# de un panel activo. Compilar todas las vistas fuera de ese contexto falla con
# "Unable to locate a class or view for component [filament::form]".
# filament:cache-components cubre el caché que Filament sí necesita.
php artisan event:cache
php artisan filament:cache-components
# Publica el JS de Livewire como archivo físico en public/vendor/livewire. Sin esto
# se sirve por la ruta /livewire/livewire.min.js, cuya URL nunca cambia entre
# versiones del asset: si un navegador llegó a cachear una mala respuesta de esa
# URL (pasó con un 404 cacheable por un año), queda roto hasta que expire. El
# archivo publicado usa otra URL (/vendor/livewire/...) y hay que re-publicarlo en
# cada deploy para que no quede desfasado del vendor.
php artisan livewire:publish --assets
php artisan storage:link || true

echo "==> Permisos y recarga de PHP-FPM"
sudo /usr/local/bin/restaurante-postdeploy

php artisan up
trap - EXIT

echo "==> Despliegue completado"

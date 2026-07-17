#!/usr/bin/env bash
# Respaldo comprimido de la base, con rotación. Pensado para cron (ver DEPLOY.md).
# Lee las credenciales del .env de la app para no duplicarlas en dos sitios.
set -euo pipefail

APP_DIR="${APP_DIR:-/var/www/restaurante}"
BACKUP_DIR="${BACKUP_DIR:-/var/backups/restaurante}"
RETENTION_DAYS="${RETENTION_DAYS:-14}"
MYSQLDUMP="${MYSQLDUMP:-mysqldump}"

env_get() {
    sed -n "s/^$1=//p" "$APP_DIR/.env" | tail -n1 | tr -d '"'"'"'\r'
}

DB_DATABASE="$(env_get DB_DATABASE)"
DB_USERNAME="$(env_get DB_USERNAME)"
DB_PASSWORD="$(env_get DB_PASSWORD)"
DB_HOST="$(env_get DB_HOST)"
DB_HOST="${DB_HOST:-127.0.0.1}"

if [ -z "$DB_DATABASE" ]; then
    echo "No se pudo leer DB_DATABASE de $APP_DIR/.env" >&2
    exit 1
fi

mkdir -p "$BACKUP_DIR"
chmod 700 "$BACKUP_DIR"

# Pasar la contraseña por --defaults-file y no por --password evita que quede
# visible en `ps` para cualquier usuario del sistema.
MYSQL_CNF="$(mktemp)"
cleanup() { rm -f "$MYSQL_CNF"; }
trap cleanup EXIT
chmod 600 "$MYSQL_CNF"
{
    echo "[client]"
    echo "user=${DB_USERNAME}"
    echo "password=${DB_PASSWORD}"
    echo "host=${DB_HOST}"
} > "$MYSQL_CNF"

STAMP="$(date +%Y%m%d-%H%M%S)"
FILE="$BACKUP_DIR/${DB_DATABASE}-${STAMP}.sql.gz"

# --single-transaction toma un snapshot consistente sin bloquear escrituras (InnoDB).
"$MYSQLDUMP" --defaults-file="$MYSQL_CNF" \
    --single-transaction \
    --quick \
    --routines \
    --events \
    --no-tablespaces \
    "$DB_DATABASE" | gzip -9 > "$FILE"

# Un respaldo truncado es peor que ninguno, porque da una falsa sensación de
# seguridad: si el gzip no está íntegro, se borra y el cron falla ruidosamente.
if [ ! -s "$FILE" ] || ! gzip -t "$FILE" 2>/dev/null; then
    echo "Respaldo inválido, se descarta: $FILE" >&2
    rm -f "$FILE"
    exit 1
fi

chmod 600 "$FILE"

find "$BACKUP_DIR" -name "${DB_DATABASE}-*.sql.gz" -type f -mtime "+${RETENTION_DAYS}" -delete

echo "Respaldo OK: $FILE ($(du -h "$FILE" | cut -f1))"

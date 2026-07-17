# Despliegue en droplet Ubuntu (DigitalOcean)

Stack: Nginx + PHP-FPM 8.3 + MySQL 8, todo en el mismo droplet.
Recomendado: **2 GB de RAM**. Con 1 GB el build de Vite muere por falta de memoria
(ver "Swap" más abajo si no puedes subir de tamaño).

## 1. Paquetes

```bash
sudo apt update && sudo apt upgrade -y
sudo apt install -y software-properties-common
sudo add-apt-repository -y ppa:ondrej/php
sudo apt update

sudo apt install -y nginx mysql-server git unzip curl \
  php8.3-fpm php8.3-mysql php8.3-mbstring php8.3-xml php8.3-curl \
  php8.3-zip php8.3-gd php8.3-intl php8.3-bcmath

# Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Node 22 (para compilar los assets)
curl -fsSL https://deb.nodesource.com/setup_22.x | sudo -E bash -
sudo apt install -y nodejs
```

Las extensiones no son opcionales: `gd` la usan dompdf y el generador de códigos de
barras, `zip` + `xml` las usa maatwebsite/excel, e `intl` la exige Filament.

## 2. Base de datos

```bash
sudo mysql_secure_installation
sudo mysql
```

```sql
CREATE DATABASE restaurante CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'restaurante'@'localhost' IDENTIFIED BY 'UNA_CONTRASEÑA_FUERTE';
GRANT ALL PRIVILEGES ON restaurante.* TO 'restaurante'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

## 3. Código

```bash
sudo mkdir -p /var/www/restaurante
sudo chown -R $USER:www-data /var/www/restaurante
git clone https://github.com/HermesAguilera/Restaurant.git /var/www/restaurante
cd /var/www/restaurante

cp .env.example .env
composer install --no-dev --optimize-autoloader
npm ci && npm run build
php artisan key:generate
```

Edita `.env`: `APP_URL` con tu dominio real (https), `DB_PASSWORD`, y revisa que
`APP_DEBUG=false` y `APP_ENV=production`. **Con `APP_DEBUG=true` cualquier error
muestra las credenciales de la base en pantalla.**

## 4. Migraciones y usuario inicial

```bash
php artisan migrate --force
php artisan db:seed --force   # crea permisos, rol root y el usuario root
php artisan storage:link
```

El seeder crea **`root@example.com` / `password`**. Entra a `/admin` y cámbiala
de inmediato, o crea tu propio usuario y borra ese.

## 5. Permisos de archivos

```bash
sudo chown -R www-data:www-data /var/www/restaurante
sudo chmod -R 775 /var/www/restaurante/storage /var/www/restaurante/bootstrap/cache
```

## 6. Nginx

```bash
sudo cp deploy/nginx.conf /etc/nginx/sites-available/restaurante
sudo sed -i 's/tu-dominio.com/TU_DOMINIO_REAL/g' /etc/nginx/sites-available/restaurante
sudo ln -s /etc/nginx/sites-available/restaurante /etc/nginx/sites-enabled/
sudo rm -f /etc/nginx/sites-enabled/default
sudo nginx -t && sudo systemctl reload nginx
```

PHP acepta 2M de subida por defecto y el formulario de fotos permite 2 MB, así que
las subidas al límite fallan. En `/etc/php/8.3/fpm/php.ini`:

```ini
upload_max_filesize = 12M
post_max_size = 12M
```

```bash
sudo systemctl restart php8.3-fpm
```

## 7. HTTPS

```bash
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d TU_DOMINIO -d www.TU_DOMINIO
```

La app ya confía en el proxy (`trustProxies` en `bootstrap/app.php`), que es lo que
evita que Filament y Livewire generen URLs `http://` detrás del certificado.

## 8. Cron del scheduler

`routes/console.php` tiene una tarea diaria que borra las órdenes de restaurante de
días anteriores. **No corre sola.** `sudo crontab -e`:

```cron
* * * * * cd /var/www/restaurante && php artisan schedule:run >> /dev/null 2>&1
```

## 9. Firewall

```bash
sudo ufw allow OpenSSH
sudo ufw allow 'Nginx Full'
sudo ufw enable
```

## 10. Cachés de producción

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
php artisan filament:cache-components
```

---

## 11. Usuario de despliegue (necesario para GitHub Actions)

No despliegues por SSH como `root`. Crea un usuario dedicado, dueño del código y
miembro de `www-data`:

```bash
sudo adduser --disabled-password --gecos "" deploy
sudo usermod -aG www-data deploy
sudo chown -R deploy:www-data /var/www/restaurante
```

Estar en el grupo `www-data` no es opcional: al final del despliegue `storage/`
queda en manos de `www-data`, y `deploy` necesita seguir pudiendo borrar el flag de
mantenimiento con `php artisan up`.

El único paso que requiere root va aislado en su propio script:

```bash
sudo cp deploy/restaurante-postdeploy /usr/local/bin/restaurante-postdeploy
sudo chown root:root /usr/local/bin/restaurante-postdeploy
sudo chmod 755 /usr/local/bin/restaurante-postdeploy

echo 'deploy ALL=(root) NOPASSWD: /usr/local/bin/restaurante-postdeploy' \
  | sudo tee /etc/sudoers.d/restaurante-deploy
sudo chmod 440 /etc/sudoers.d/restaurante-deploy
sudo visudo -c
```

Ese script lleva las rutas fijas por diseño. Si aceptara una ruta por argumento, el
usuario `deploy` podría hacer `chown` de cualquier directorio del sistema y escalar
a root.

## 12. Que el droplet pueda hacer `git pull`

`HermesAguilera/Restaurant` es público, así que `git pull` por HTTPS funciona sin
ninguna clave ni token — es lo que ya quedó configurado en el paso 3. No hace falta
nada más aquí.

Verifícalo como usuario `deploy`, que es quien correrá `deploy.sh`:

```bash
sudo -u deploy git -C /var/www/restaurante pull --ff-only
```

Si el repositorio pasa a privado en algún momento, ese comando empezará a pedir
credenciales y `deploy.sh` fallará en el `git pull`. En ese caso hace falta una
**deploy key de solo lectura** (Settings → Deploy keys → Add deploy key del repo) y
cambiar el remoto a SSH:

```bash
sudo -u deploy ssh-keygen -t ed25519 -C "droplet-restaurante" -f /home/deploy/.ssh/id_ed25519 -N ""
sudo cat /home/deploy/.ssh/id_ed25519.pub   # pegar como deploy key en GitHub
sudo -u deploy ssh -o StrictHostKeyChecking=accept-new -T git@github.com || true
sudo -u deploy git -C /var/www/restaurante remote set-url origin git@github.com:HermesAguilera/Restaurant.git
```

## 13. GitHub Actions

El workflow está en `.github/workflows/deploy.yml`: ante un push a `main` corre los
tests y, solo si pasan, entra por SSH y ejecuta `deploy/deploy.sh`.

Genera un par de claves **en tu máquina** para que Actions entre al droplet (esto
es solo para SSH hacia el servidor; no tiene relación con el acceso a GitHub del
paso 12):

```bash
ssh-keygen -t ed25519 -C "github-actions" -f ./gh_deploy_key -N ""
ssh-copy-id -i ./gh_deploy_key.pub deploy@IP_DEL_DROPLET
ssh-keyscan -H IP_DEL_DROPLET   # la salida va al secreto DEPLOY_KNOWN_HOSTS
```

En `github.com/HermesAguilera/Restaurant` → Settings → Secrets and variables →
Actions, crea:

| Secreto | Valor |
| --- | --- |
| `DEPLOY_SSH_KEY` | contenido de `gh_deploy_key` (la clave **privada**, completa) |
| `DEPLOY_HOST` | IP o dominio del droplet |
| `DEPLOY_USER` | `deploy` |
| `DEPLOY_KNOWN_HOSTS` | salida de `ssh-keyscan -H IP_DEL_DROPLET` |

Borra `gh_deploy_key` de tu máquina una vez cargado el secreto. `DEPLOY_KNOWN_HOSTS`
evita tener que aceptar a ciegas la huella del servidor en cada ejecución.

El primer despliegue automático requiere que los pasos 1–12 ya estén hechos a mano:
Actions actualiza un sitio que ya funciona, no provisiona el droplet desde cero.

## 14. Respaldos (no opcional)

El sistema guarda facturas con CAI y `routes/console.php` borra órdenes viejas todos
los días de forma permanente. En un solo droplet y sin respaldos, un disco corrupto,
una migración mala o un `DELETE` accidental se llevan la contabilidad sin vuelta atrás.

```bash
sudo cp deploy/backup-db.sh /usr/local/bin/restaurante-backup
sudo chown root:root /usr/local/bin/restaurante-backup
sudo chmod 700 /usr/local/bin/restaurante-backup
sudo mkdir -p /var/backups/restaurante && sudo chmod 700 /var/backups/restaurante
```

Pruébalo a mano una vez y luego agéndalo en el crontab de root (`sudo crontab -e`):

```cron
0 3 * * * /usr/local/bin/restaurante-backup >> /var/log/restaurante-backup.log 2>&1
```

Guarda 14 días con rotación automática, y si el dump sale truncado lo descarta y
falla en vez de dejar un respaldo inútil que parezca bueno.

### Restaurar

Comprueba esto **antes** de necesitarlo, no el día del incidente:

```bash
gunzip -c /var/backups/restaurante/restaurante-AAAAMMDD-HHMMSS.sql.gz \
  | mysql -u root -p restaurante
```

Para ensayar sin tocar producción, restaura en una base aparte
(`CREATE DATABASE restaurante_prueba;`) y compara.

### Fuera del droplet

Los respaldos viven en el mismo disco que la base: si el droplet muere, mueren con
él. Cubre eso con:

- **Backups de DigitalOcean** (semanales, en el panel del droplet) o snapshots.
- Copiar el `.sql.gz` a otra máquina o a Spaces con `scp`/`rclone`.
- Guardar el `.env` en un gestor de contraseñas: no está en git, y perder el
  `APP_KEY` invalida todas las sesiones activas.

### Renovación del certificado

Certbot instala su propio timer, pero conviene confirmarlo el primer día:

```bash
sudo certbot renew --dry-run
sudo systemctl list-timers | grep certbot
```

## Actualizaciones manuales

```bash
sudo -u deploy bash /var/www/restaurante/deploy/deploy.sh
```

## Swap (solo si el droplet tiene 1 GB)

`npm run build` es hoy el paso más pesado del despliegue y puede morir por falta de
memoria sin mensaje claro (aunque ahora mismo su salida no la usa nadie):

```bash
sudo fallocate -l 2G /swapfile
sudo chmod 600 /swapfile
sudo mkswap /swapfile && sudo swapon /swapfile
echo '/swapfile none swap sw 0 0' | sudo tee -a /etc/fstab
```

## Notas

- **Cambia `APP_KEY`**: el `.env` local ya tiene una y no debe reutilizarse en producción.
  `php artisan key:generate` genera una nueva.
- Si editas `.env` en el servidor, corre `php artisan config:cache` de nuevo: con la
  configuración cacheada, Laravel ignora los cambios del `.env`.

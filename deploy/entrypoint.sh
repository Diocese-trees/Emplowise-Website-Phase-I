#!/bin/sh
# Container start-up: make the app bootable on a fresh volume, then serve.
set -e

cd /var/www/html

# 1. Environment file -------------------------------------------------------
if [ ! -f .env ]; then
    if [ -f .env.example ]; then
        cp .env.example .env
    else
        touch .env
    fi
fi

# 2. Application key --------------------------------------------------------
if ! grep -q '^APP_KEY=base64:' .env && [ -z "${APP_KEY:-}" ]; then
    php artisan key:generate --force --no-interaction
fi

# 3. Writable paths ---------------------------------------------------------
mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache

# 4. Database ---------------------------------------------------------------
if [ "${DB_CONNECTION:-sqlite}" = "sqlite" ]; then
    DB_FILE="${DB_DATABASE:-/var/www/html/database/database.sqlite}"
    case "$DB_FILE" in
        /*) ;;
        *) DB_FILE="/var/www/html/$DB_FILE" ;;
    esac
    mkdir -p "$(dirname "$DB_FILE")"
    [ -f "$DB_FILE" ] || touch "$DB_FILE"
fi

php artisan migrate --force --no-interaction

# 5. Production caches ------------------------------------------------------
php artisan optimize:clear >/dev/null 2>&1 || true
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 6. Permissions ------------------------------------------------------------
chown -R www-data:www-data storage bootstrap/cache database 2>/dev/null || true

exec "$@"

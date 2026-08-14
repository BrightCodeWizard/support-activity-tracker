#!/bin/sh
set -e

: "${PORT:=80}"

mkdir -p storage/logs storage/framework/cache storage/framework/sessions storage/framework/views storage/framework/testing

# Generate an APP_KEY if Render has not provided one yet
if [ -z "${APP_KEY}" ]; then
    export APP_KEY="$(php artisan key:generate --show --force)"
fi

# Cache config/routes/views now that runtime env vars are available
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Keep schema and demo data up to date (seed is idempotent)
php artisan migrate --force
php artisan db:seed --force

# Render proxies to the port in $PORT; inject it into the nginx config
envsubst '${PORT}' < /etc/nginx/conf.d/default.conf.template > /etc/nginx/conf.d/default.conf

php-fpm &

exec nginx -g 'daemon off;'

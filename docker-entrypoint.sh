#!/bin/sh
set -e

echo "==> entrypoint: starting (PORT=${PORT:-unset})"

: "${PORT:=80}"

mkdir -p storage/logs storage/framework/cache storage/framework/sessions storage/framework/views storage/framework/testing

if [ -z "${APP_KEY}" ]; then
    echo "==> No APP_KEY provided; generating one"
    export APP_KEY="$(php artisan key:generate --show --force)"
fi

echo "==> DB_CONNECTION=${DB_CONNECTION:-unset}"
if [ -n "${DB_URL}" ]; then
    echo "==> DB_URL is set (using connection string)"
else
    echo "==> DB_URL is NOT set"
fi

echo "==> Caching config/views/routes"
php artisan config:cache || echo "    config:cache returned nonzero (continuing)"
php artisan route:cache || echo "    route:cache returned nonzero (continuing)"
php artisan view:cache || echo "    view:cache returned nonzero (continuing)"

echo "==> Running database migrations"
php artisan migrate --force

echo "==> Seeding database (idempotent)"
php artisan db:seed --force

echo "==> Writing nginx config for PORT=${PORT}"
envsubst '${PORT}' < /etc/nginx/conf.d/default.conf.template > /etc/nginx/conf.d/default.conf

echo "==> Starting php-fpm"
php-fpm &

echo "==> Starting nginx"
exec nginx -g 'daemon off;'

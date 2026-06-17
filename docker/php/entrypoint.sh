#!/bin/sh
set -eu

cd /var/www/html

mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache

if [ -f composer.lock ]; then
    current_hash="$(sha1sum composer.lock | awk '{print $1}')"
    stored_hash="$(cat vendor/.composer-lock-hash 2>/dev/null || true)"

    if [ ! -f vendor/autoload.php ] || [ "$current_hash" != "$stored_hash" ]; then
        composer install --no-interaction --prefer-dist --optimize-autoloader --no-scripts
        printf '%s' "$current_hash" > vendor/.composer-lock-hash
    fi
fi

if [ -f artisan ] && [ -f vendor/autoload.php ]; then
    php artisan package:discover --ansi >/dev/null 2>&1 || true
fi

chmod ug+rwx storage bootstrap/cache storage/framework storage/framework/cache storage/framework/sessions storage/framework/views 2>/dev/null || true

exec "$@"

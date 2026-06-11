#!/bin/sh
set -e

cd /var/www/html

# Clear stale build-time config cache
php artisan config:clear || true

# Rebuild config with live secrets — non-fatal in case of DB issues at cold start
php artisan config:cache || echo "config:cache failed, continuing anyway"
php artisan view:cache   || echo "view:cache failed, continuing anyway"

# Always start the server regardless
exec /usr/bin/supervisord -c /etc/supervisord.conf
#!/bin/bash
# Create log file for Laravel and give it write access
# www-data is a standard apache user that must have an
# access to the folder structure
if [ ! -f ".env" ]; then
    echo "Creating .env file"
    cp .env.example .env
else
echo "env file exists."
fi
chgrp -R www-data storage bootstrap/cache && \
chown -R www-data storage bootstrap/cache && \
chmod -R ug+rwx storage bootstrap/cache && \
chmod -R 776 storage/
#now running composer install and npm install
composer update  --ignore-platform-reqs #&& npm install
#finally rebuild npm to serve prod.
# npm run prod

APP_KEY_VALUE="$(grep -E '^APP_KEY=' .env 2>/dev/null | cut -d= -f2-)"
if [ -z "$APP_KEY_VALUE" ]; then
    php artisan key:generate
else
    echo "APP_KEY already configured."
fi

php artisan migrate --seed

php artisan config:clear && php artisan config:cache
echo "Permission setup Done... Project now ready to serve"

php artisan test

exec "$@"

#!/bin/bash
set -euo pipefail

# Set DEPLOY_DEBUG=1 to enable verbose command tracing during troubleshooting.
if [[ "${DEPLOY_DEBUG:-0}" =~ ^(1|true|TRUE|yes|YES)$ ]]; then
  set -x
fi

PHP_BIN="$(command -v php)"
APP_DIR="/var/app/current"

# Install imagick only if it is missing (prevents repeated pecl failures)
if ! "$PHP_BIN" -m | grep -qi '^imagick$'; then
  sudo pecl channel-update pecl.php.net
  sudo dnf install -y ImageMagick ImageMagick-devel ImageMagick-perl
  printf "\n" | sudo pecl install imagick
  sudo /bin/sed -i -e '/extension="imagick.so"/d' /etc/php.ini
  echo 'extension=imagick.so' | sudo tee /etc/php.d/20-imagick.ini >/dev/null
fi

cd "$APP_DIR"
# Ensure zip extension exists before trying to build it with pecl
if ! "$PHP_BIN" -m | grep -qi '^zip$'; then
  sudo dnf install -y libzip libzip-devel
  printf "\n" | sudo pecl install zip
fi

# Remove bundled vendor if present so composer install always starts clean
if [ -d "vendor" ]; then
  rm -rf vendor
fi

# Install PHP dependencies (avoids uploading vendor) only if composer exists
# Ensure composer exists (download PHAR once if necessary)
if ! command -v composer >/dev/null 2>&1; then
  echo "Composer not found; installing to /usr/local/bin/composer"
  if ! EXPECTED_SIGNATURE="$(curl --fail --silent --show-error --location --retry 3 --retry-delay 2 --connect-timeout 10 --max-time 30 https://composer.github.io/installer.sig)"; then
    rm -f composer-setup.php
    exit 1
  fi
  if ! curl --fail --silent --show-error --location --retry 3 --retry-delay 2 --connect-timeout 10 --max-time 60 https://getcomposer.org/installer -o composer-setup.php; then
    rm -f composer-setup.php
    exit 1
  fi
  ACTUAL_SIGNATURE="$("$PHP_BIN" -r 'echo hash_file("sha384", "composer-setup.php");')"
  if [ "$EXPECTED_SIGNATURE" != "$ACTUAL_SIGNATURE" ]; then
    >&2 echo 'ERROR: Invalid composer installer signature'
    rm -f composer-setup.php
    exit 1
  fi
  sudo "$PHP_BIN" composer-setup.php --install-dir=/usr/local/bin --filename=composer
  rm -f composer-setup.php
fi

sudo -u webapp COMPOSER_HOME="$APP_DIR" composer install --prefer-dist --no-dev --no-interaction --optimize-autoloader

# Set the correct ownership and permissions for Laravel's storage directory
mkdir -p "$APP_DIR/storage/logs"
chown -R webapp:webapp "$APP_DIR/storage" "$APP_DIR/bootstrap/cache"
chmod -R 775 "$APP_DIR/storage" "$APP_DIR/bootstrap/cache"
sudo -u webapp touch "$APP_DIR/storage/logs/laravel.log"

# Run migrations with seeders (allow failure if DB is unavailable)
# sudo -u webapp php artisan migrate --seed --force

mkdir -p "$APP_DIR/storage/app/private"
chown -R webapp:webapp "$APP_DIR/storage/app/private"
chmod -R 0770 "$APP_DIR/storage/app/private"

run_artisan() {
  sudo -u webapp "$PHP_BIN" artisan "$@"
}

run_artisan permission:cache-reset || true
run_artisan view:clear
run_artisan config:clear
run_artisan route:clear
run_artisan cache:clear

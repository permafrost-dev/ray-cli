#!/bin/sh
set -eu

# this script is used for building the ray.phar binary

THISDIR=$(realpath `dirname $0`)
TEMP_VENDOR_DIR="$THISDIR/vendor"
TEMP_COMPOSER_LOCK="$THISDIR/composer.lock"

PROJECT_DIR=$(realpath "$THISDIR/..")

cd "$THISDIR"

composer install --no-interaction --no-progress --optimize-autoloader

# require suggested packages
composer require --no-update symfony/polyfill-mbstring

# install package deps without dev-deps / remove already installed dev-deps
# box can ignore dev-deps, but dev-deps, when installed, may lower version of prod-deps
#composer update --no-interaction --no-progress --no-dev --prefer-stable --optimize-autoloader
composer info -D | sort

cd "$PROJECT_DIR"
mv "$PROJECT_DIR/vendor" "$PROJECT_DIR/vendor-bak"
mv "$PROJECT_DIR/composer.lock" "$PROJECT_DIR/composer.lock-bak"

composer install --no-interaction --no-progress --optimize-autoloader --no-dev

cd "$THISDIR"

#composer show -d dev-tools humbug/box -q || composer update -d dev-tools --no-interaction --no-progress

# build phar file
APP_ENV=BOX ./vendor/bin/box compile -c "$PROJECT_DIR/box.json.dist" --working-dir="$PROJECT_DIR"

rm -rf "$TEMP_VENDOR_DIR"
rm -f "$TEMP_COMPOSER_LOCK"

RELEASE_VERSION=$(git -C $PROJECT_DIR describe | awk -F'-' '{ print $1 }')

mv $PROJECT_DIR/bin/ray.phar $THISDIR/ray-$RELEASE_VERSION.phar

cd "$PROJECT_DIR"
rm -rf "$PROJECT_DIR/vendor"
rm -f "$PROJECT_DIR/composer.lock"
mv "$PROJECT_DIR/vendor-bak" "$PROJECT_DIR/vendor"
mv "$PROJECT_DIR/composer.lock-bak" "$PROJECT_DIR/composer.lock"

cd "$THISDIR"

# revert changes to composer
#git checkout composer.json
#composer update --no-interaction --no-progress -q

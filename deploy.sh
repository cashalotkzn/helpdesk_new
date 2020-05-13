#!/bin/sh
# activate maintenance mode
php7.3 artisan down
# update source code
git pull
# update PHP dependencies
composer-php7.3 install --no-interaction --no-dev --prefer-dist
# --no-interaction Do not ask any interactive question
# --no-dev  Disables installation of require-dev packages.
# --prefer-dist  Forces installation from package dist even for dev versions.
# update database
#php artisan migrate --force
# --force  Required to run when in production.
php7.3 artisan migrate:fresh --seed
# clear and cache config
php7.3 artisan config:cache
# clear and cache route
php7.3 artisan route:cache
# clear and cache view
php7.3 artisan view:cache
# webpack build
npm run prod
# stop maintenance mode
php7.3 artisan up

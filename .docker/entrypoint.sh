#!/bin/bash

#On error no such file entrypoint.sh, execute in terminal - dos2unix .docker\entrypoint.sh
npm config set cache /var/www/.npm-cache --global
cd /var/www/frontend && npm install && cd ..

cd backend
chown -R www-data:www-data .
composer install
php artisan key:generate
php artisan migrate

php-fpm

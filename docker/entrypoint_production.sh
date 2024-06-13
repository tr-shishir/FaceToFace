#!/usr/bin/env sh

php artisan optimize:clear
chmod -R 777 /opt/app/storage/* /opt/app/bootstrap/*

nginx
php-fpm
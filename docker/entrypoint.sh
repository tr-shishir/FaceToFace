#!/usr/bin/env bash

composer install
php artisan optimize:clear

chmod -R 777 /opt/app/storage/*

supervisord
service nginx start
php-fpm

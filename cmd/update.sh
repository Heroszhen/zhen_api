#! /bin/bash
#update on server

rm -rf var vendor
rm -rf public/build
php composer.phar install
php bin/console d:s:u -f
npm run build
#! /bin/bash
#update on server

rm -rf var vendor
rm -rf public/build
php composer.phar install
npm run build
#! /bin/bash
#update on server

rm -rf var vendor
rm -rf public/build public/bundles
php composer.phar install
php bin/console lexik:jwt:generate-keypair
php bin/console d:s:u -f
npm run build
php bin/console cache:clear
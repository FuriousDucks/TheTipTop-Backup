composer install --no-interaction
yarn install
yarn build
php bin/console d:d:c -n --if-not-exists
php bin/console d:m:m -n --no-interaction
php bin/console d:f:l -n --no-interaction
chmod -R 777 /public/images
exec apache2-foreground "$@"
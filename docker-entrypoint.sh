composer install --no-interaction
yarn install
yarn build
bin/console d:d:c -n --if-not-exists
bin/console d:m:m -n --no-interaction
bin/console d:f:l -n --no-interaction
chmod -R 777 /public/images
exec apache2-foreground "$@"
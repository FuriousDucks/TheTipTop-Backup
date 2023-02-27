composer install --no-interaction --no-progress --no-suggest --no-scripts --prefer-dist --optimize-autoloader
yarn install
yarn run build
bin/console d:d:c -n
bin/console d:m:m -n
bin/console d:f:l -n
chmod -R 777 /public/images
exec apache2-foreground "$@"
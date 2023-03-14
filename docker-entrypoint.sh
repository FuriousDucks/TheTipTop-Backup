composer install --no-interaction
yarn install
yarn build
php bin/console doctrine:database:create -n --if-not-exists
php bin/console doctrine:migration:migrate -n --no-interaction
php bin/console doctrine:fixture:load -n --no-interaction
chmod -R 777 /public/images
exec apache2-foreground "$@"
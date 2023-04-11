chmod -R 777 ./public/*
composer install --no-interaction
yarn install --no-interaction
yarn build --no-interaction
npm install --no-interaction
npm run build --no-interaction
php bin/console doctrine:database:create -n --if-not-exists
php bin/console doctrine:migration:migrate --no-interaction
php bin/console doctrine:fixtures:load --no-interaction
exec apache2-foreground "$@"
chmod -R 777 ./public/*
chmod -R 777 ./vendor/*
chmod -R 777 ./var/*
composer install -n
npm install --force
npm run build
php bin/console doctrine:database:create -n --if-not-exists
php bin/console doctrine:migration:migrate -n
php bin/console doctrine:fixtures:load -n
exec apache2-foreground "$@"
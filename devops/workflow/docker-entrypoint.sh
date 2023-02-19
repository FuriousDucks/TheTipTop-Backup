composer install -n
composer require api
npm install
npm run build
bin/console doctrine:database:create -n
bin/console doctrine:migrations:migrate -n
bin/console doctrine:fixtures:load -n
chmod -R 777 /public/images
exec apache2-foreground "$@"
FROM php:8.2-apache

RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf
RUN apt-get update \
    && apt-get install -y --no-install-recommends locales apt-utils git libicu-dev g++ libpng-dev libxml2-dev libzip-dev libonig-dev libxslt-dev;

RUN docker-php-ext-configure intl
RUN docker-php-ext-install pdo pdo_mysql gd opcache intl zip calendar dom mbstring gd xsl
RUN pecl install apcu && docker-php-ext-enable apcu
RUN pecl install xdebug  && docker-php-ext-enable  xdebug \
    && echo "xdebug.mode=coverage" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

RUN apt-get update && apt-get install -y \
    curl \
    nodejs \
    npm \ 
    git \ 
    zip \
    unzip 

RUN  curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN curl -sL https://deb.nodesource.com/setup_16.x | bash - \
    && apt-get install -y nodejs

RUN npm install -g yarn

# RUN curl -sS https://get.symfony.com/cli/installer | bash

# RUN echo "export PATH=$HOME/.symfony/bin:$PATH" >> ~/.bashrc

# RUN mv /root/.symfony/bin/symfony /usr/local/bin/symfony

# RUN chmod +x /usr/local/bin/symfony

# RUN symfony server:ca:install

COPY ./docker-entrypoint.sh /docker-entrypoint.sh

RUN chmod +x /docker-entrypoint.sh

COPY ./thetiptop /var/www/html/thetiptop

RUN chown -R www-data:www-data /var/www/html/thetiptop

COPY ./php/vhosts/vhosts.conf /etc/apache2/sites-available/000-default.conf

RUN a2enmod rewrite

RUN a2enmod rewrite headers

WORKDIR /var/www/html/thetiptop

COPY ./wait-for-it.sh /wait-for-it.sh

RUN chmod +x /wait-for-it.sh

RUN echo "memory_limit=-1" > $PHP_INI_DIR/conf.d/php.ini

# RUN npm install --force && \
#     npm run build

# RUN composer install

# RUN symfony console doctrine:database:create

# RUN symfony console doctrine:migrations:migrate

# RUN symfony console doctrine:fixtures:load

# RUN symfony console cache:clear

# RUN symfony console cache:warmup

# RUN symfony console assets:install

# ENTRYPOINT ["bash", "/docker-entrypoint.sh"]

# EXPOSE 80

# CMD ["apache2-foreground"]

FROM php:apache

RUN apt-get update && apt-get -y --no-install-recommends install git \
    && php -r "readfile('http://getcomposer.org/installer');" | php -- --install-dir=/usr/bin/ --filename=composer \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo_mysql mysqli opcache \
&& docker-php-ext-enable mysqli

#Add the project
COPY . /srv/app 
COPY .docker/apache/vhost.conf /etc/apache2/sites-available/000-default.conf
COPY .docker/php/php.ini /usr/local/etc/php/php.ini

WORKDIR /srv/app

RUN chown -R www-data:www-data /srv/app/public \
    && a2enmod rewrite \
    && chown -R 775 /srv/app/public \
    && ls -al /srv/app/public

#RUN composer require nesbot/carbon
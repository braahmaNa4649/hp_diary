FROM php:8.2-fpm

RUN apt-get update \
  && apt-get install -y zlib1g-dev mariadb-client vim libzip-dev nodejs npm
RUN docker-php-ext-install zip pdo_mysql

ADD ./zz-docker.conf /usr/local/etc/php-fpm.d
RUN mkdir -p /var/run/php-fpm
RUN mkdir -p /var/php-fpm

#Composer install
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php composer-setup.php
RUN php -r "unlink('composer-setup.php');"
RUN mv composer.phar /usr/local/bin/composer

ENV COMPOSER_ALLOW_SUPERUSER 1

ENV COMPOSER_HOME /composer

ENV PATH $PATH:/composer/vendor/bin


RUN mkdir -p /var/www/app
WORKDIR /var/www/app

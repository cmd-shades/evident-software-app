FROM php:8-fpm-alpine3.12

RUN apk --no-cache add composer curl gd  \
                       # build tools
                       autoconf g++ gcc make \
                       # lib tools
                       bzip2-dev freetype-dev gettext-dev icu-dev imagemagick-dev libintl libjpeg-turbo-dev \
                       #  libmcrypt-dev
                       libpng-dev libxslt-dev libzip-dev;

RUN docker-php-ext-install pdo pdo_mysql zip gd

RUN docker-php-source extract && \
       pecl install redis && \
       pecl install xdebug && \
       docker-php-ext-enable redis && \
       docker-php-ext-enable xdebug && \
       docker-php-source delete

# copy custom php.ini and xdebug.ini
COPY conf.d/php.ini /usr/local/etc/php/php.ini
COPY conf.d/xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Add node js
RUN apk add --update nodejs nodejs-npm

WORKDIR /var/www/app

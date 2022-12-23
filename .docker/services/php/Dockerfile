FROM php:7.3

RUN apt-get update && apt-get install --no-install-recommends -y \
        autoconf \
        g++ \
        make \
        file \
        icu-devtools \
        libicu-dev \
        libxml2-dev \
        zlib1g-dev \
        git \
        zip \
        unzip \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libzip-dev

RUN docker-php-ext-install mysqli pdo_mysql bcmath zip gd xml mbstring
RUN apt-get autoremove -y
RUN rm -rf /var/lib/apt/lists/*

COPY --from=composer:1.10.17 /usr/bin/composer /usr/bin/composer
RUN composer global require hirak/prestissimo

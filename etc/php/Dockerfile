FROM php:7.4-fpm-alpine3.13

RUN apk --update --no-cache add git

RUN docker-php-ext-install pdo_mysql

COPY --from=composer /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

CMD composer install ;  php-fpm

EXPOSE 9000

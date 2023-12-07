ARG version

FROM php:$version

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/bin/

RUN install-php-extensions intl zip bz2 intl mysqli pdo_mysql mcrypt xdebug

COPY --from=composer/composer:2-bin --link /composer /usr/bin/composer

RUN apk add --no-cache bash
RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.alpine.sh' | bash
RUN apk add symfony-cli

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"
COPY www.conf /usr/local/etc/php-fpm/www.conf

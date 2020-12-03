ARG PHP_VERSION

FROM php:$PHP_VERSION

ARG USER_ID
ARG GROUP_ID

RUN apt-get update > /dev/null \
    && apt-get install -y \
        apt-utils \
    && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libbz2-dev \
        zlib1g-dev  \
        libzip-dev \
        # for composer
        unzip \
        libmcrypt-dev \
        libpng-dev zlib1g-dev libicu-dev g++ libmagickwand-dev --no-install-recommends libxml2-dev \
        libicu-dev > /dev/null \
    && docker-php-ext-configure intl \
    && docker-php-ext-install -j$(nproc) mbstring zip gd pdo_mysql bz2 intl mysqli \
    && pecl install mcrypt-1.0.2 xdebug-2.9.4 \
    && docker-php-ext-enable mcrypt xdebug \
    && pecl install imagick \
    && docker-php-ext-enable imagick \
    && apt-get clean > /dev/null

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php \
    && php -r "unlink('composer-setup.php');" \
    && mv ./composer.phar /usr/local/bin/composer.phar \
    && ln -s /usr/local/bin/composer.phar /bin/composer

RUN usermod -u $USER_ID www-data && groupmod -g $GROUP_ID www-data

# for composer
RUN mkdir /www && chown www-data:www-data -R /www

WORKDIR /www

USER "$USER_ID:$GROUP_ID"

ADD ./configs/php.ini /usr/local/etc/php/php.ini
ADD ./configs/www.conf /usr/local/etc/php-fpm.d/www.conf

CMD ["php-fpm"]

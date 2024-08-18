FROM php:8.3-fpm-alpine

RUN apk add --no-cache autoconf g++ make \
    && apk add bash \
    && apk add vim \
    && apk add libmcrypt-dev \
    && apk add libpng-dev \
    && apk add zip libzip-dev \
    && apk add icu-dev \
    && apk add icu-dev \
    && rm -rf /tmp/pear

RUN docker-php-ext-configure intl
RUN docker-php-ext-install intl
RUN docker-php-ext-install pdo pdo_mysql
RUN docker-php-ext-install opcache

COPY ./conf.d/opcache.ini /usr/local/etc/php/conf.d
COPY ./conf.d/xdebug.ini /usr/local/etc/php/conf.d

#RUN docker-php-ext-install xdebug

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
# https://getcomposer.org/doc/03-cli.md#composer-allow-superuser
ENV COMPOSER_ALLOW_SUPERUSER=1

RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.alpine.sh' | bash
RUN apk add symfony-cli

COPY ./ /app

WORKDIR /app

#VOLUME /var/

#COPY --link --chmod=755 frankenphp/docker-entrypoint.sh /usr/local/bin/docker-entrypoint

#ENTRYPOINT ["docker-entrypoint"]

#RUN set -eux; \
#	mkdir -p var/cache var/log; \
#	composer dump-autoload --classmap-authoritative --no-dev; \
#	composer dump-env prod; \
#	composer run-script --no-dev post-install-cmd; \
#	chmod +x bin/console; sync;

#COPY --link composer.* symfony.* ./
#RUN set -eux; \
#	composer install --no-cache --prefer-dist --no-dev --no-autoloader --no-scripts --no-progress

CMD php-fpm

# Expose the port that the application listens on.
EXPOSE 9000
ARG PHP_VERSION='7.4'

FROM php:${PHP_VERSION}-cli

RUN apt-get update && apt-get install -y zip libzip-dev
RUN docker-php-ext-configure zip --with-libzip && docker-php-ext-install zip || docker-php-ext-install zip
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN touch /usr/local/etc/php/php.ini
RUN composer global require phpunit/phpunit

RUN export PATH=/root/.composer/vendor/bin:$PATH

COPY . /var/www/bundle

WORKDIR /var/www/bundle

RUN composer validate
RUN composer install
RUN composer phpcs
RUN composer phpstan

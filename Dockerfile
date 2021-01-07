# PHPUnit Docker Container.
FROM php:7.4-cli-buster

MAINTAINER Ahmed Waleed <ahmed_waleed1@hotmail.com>

ENV XDEBUG_MODE coverage

RUN apt-get update \
    && apt-get install -y curl ca-certificates zip unzip git python2

# Install xdebug for phpunit code coverage
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

# Goto temporary directory.
WORKDIR /tmp

# Install Docker
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Run composer and phpunit installation.
RUN composer require "phpunit/phpunit" && \
  ln -s /tmp/vendor/bin/phpunit /usr/local/bin/phpunit

# Set up the application directory.
VOLUME ["/app"]
WORKDIR /app

# Set up the command arguments.
ENTRYPOINT ["/usr/local/bin/phpunit"]
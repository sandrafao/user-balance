FROM php:7.2-cli

RUN apt-get update \
    && apt-get install git zlib1g-dev unzip -y --no-install-recommends \
    && docker-php-ext-install pdo_mysql zip bcmath sockets \
    && curl -sS https://getcomposer.org/installer \
      | php -- --install-dir=/usr/local/bin --filename=composer \
    && mkdir /usr/src/user-balance-app

COPY . /usr/src/user-balance-app

WORKDIR /usr/src/user-balance-app

CMD composer install

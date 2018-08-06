FROM php:7.2-cli

RUN apt-get update \
    && apt-get install git -y --no-install-recommends \
    && curl -sS https://getcomposer.org/installer \
      | php -- --install-dir=/usr/local/bin --filename=composer \
    && mkdir /usr/src/user-balance-app

COPY . /usr/src/user-balance-app

WORKDIR /usr/src/user-balance-app

CMD composer install

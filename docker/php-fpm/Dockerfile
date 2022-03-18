FROM php:7.4-fpm

ARG DEBIAN_FRONTEND=noninteractive
ARG USER_ID
ARG GROUP_ID

RUN echo "Europe/London" > /etc/timezone

RUN docker-php-ext-install pdo_mysql

RUN pecl install apcu

RUN apt-get update \
    && apt-get -y --no-install-recommends install git ssh ffmpeg nano vim zlib1g-dev libzip-dev \
    && apt-get update \
    && apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

RUN docker-php-ext-install \
    zip \
    bcmath \
    pdo_mysql

RUN docker-php-ext-enable apcu

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN userdel -f www-data
RUN if getent group www-data ; then groupdel www-data; fi

RUN groupadd -g $GROUP_ID www-data
RUN useradd -d /home/www-data -s /bin/bash -u $USER_ID -g $GROUP_ID www-data
RUN mkdir /home/www-data
RUN chown -R www-data:www-data /home/www-data

USER www-data

WORKDIR /var/www

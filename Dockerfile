FROM php:8.2-apache

RUN apt-get update
RUN apt-get install -y --no-install-recommends
RUN docker-php-ext-install mysqli pdo pdo_mysql

RUN cp /usr/share/zoneinfo/America/Sao_Paulo /etc/localtime

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN a2enmod rewrite
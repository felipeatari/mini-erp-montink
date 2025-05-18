FROM php:8.2-apache

# Define timezone
RUN cp /usr/share/zoneinfo/America/Sao_Paulo /etc/localtime

# Atualiza pacotes e instala dependências
RUN apt-get update && apt-get install -y --no-install-recommends \
    unzip \
    zip \
    git \
    libzip-dev \
    && docker-php-ext-install zip mysqli pdo pdo_mysql \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* \
    && git config --global --add safe.directory /var/www/html

# Instala o Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Ativa o mod_rewrite do Apache
RUN a2enmod rewrite
FROM php:5.6-fpm
WORKDIR /var/www/file_manager
RUN useradd -ms /bin/bash admin \
    && apt-get update \
    && docker-php-ext-install pdo pdo_mysql
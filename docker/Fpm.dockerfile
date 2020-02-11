FROM php:7.3-fpm
WORKDIR /var/www/html
RUN useradd -ms /bin/bash admin \
    && apt-get update \
    && docker-php-ext-install pdo pdo_mysql \
    && apt-get install -y libmagickwand-dev --no-install-recommends \
    && apt-get install -y ghostscript \
    && pecl install imagick \
    && docker-php-ext-enable imagick \
    && usermod -aG www-data admin \
    && usermod -aG admin www-data \
    && mkdir -p public/uploads/pdf \
    && mkdir -p public/uploads/thumbnails \
    && chmod 0777 -R public/uploads
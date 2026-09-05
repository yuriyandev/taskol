FROM php:7.4-fpm

# 1. Устанавливаем системные пакеты для сборки расширений
RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libwebp-dev \
    libzip-dev \
    unzip \
    git \
    autoconf \
    gcc \
    make \
    libc-dev \
    pkg-config \
    && rm -rf /var/lib/apt/lists/*

# 2. Устанавливаем расширение zip для PHP
RUN docker-php-ext-install zip

# 3. Устанавливаем Xdebug
RUN pecl install xdebug-2.9.8 \
    && docker-php-ext-enable xdebug

# 4. Конфигурируем GD
RUN docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
        --with-webp

# 5. Устанавливаем остальные PHP-расширения
RUN docker-php-ext-install -j$(nproc) \
        gd \
        mysqli \
        pdo \
        pdo_mysql \
        zip

RUN mkdir -p /var/log/php \
    && touch /var/log/php/php_errors.log \
    && chown -R www-data:www-data /var/log/php

RUN touch /tmp/xdebug.log \
    && chown www-data:www-data /tmp/xdebug.log

# Подключаем кастомный php.ini
COPY php.ini /usr/local/etc/php/
COPY conf.d/dev.ini /usr/local/etc/php/conf.d/dev.ini
COPY www.conf /usr/local/etc/php-fpm.d/www.conf

WORKDIR /var/www/html
COPY . /var/www/html

RUN sed -i 's|^listen = .*|listen = 0.0.0.0:9000|' /usr/local/etc/php-fpm.d/www.conf
CMD ["php-fpm", "-F"]
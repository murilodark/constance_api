FROM php:8.3-fpm

ARG user=constance
ARG uid=1000

# Controla o Xdebug (default: off)
ARG XDEBUG_MODE=off
ENV XDEBUG_MODE=${XDEBUG_MODE}

RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    default-mysql-client \
 && docker-php-ext-install \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    sockets \
    opcache \
 && pecl install redis xdebug \
 && docker-php-ext-enable redis xdebug \
 && apt-get clean \
 && rm -rf /var/lib/apt/lists/* /tmp/pear


# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Usuário não-root
RUN useradd -G www-data,root -u $uid -d /home/$user $user \
    && mkdir -p /home/$user/.composer \
    && chown -R $user:$user /home/$user

WORKDIR /var/www

# Configurações PHP
COPY docker/php/custom.ini /usr/local/etc/php/conf.d/custom.ini

USER $user

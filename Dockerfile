FROM php:8.3-cli

# Installe les dépendances système
RUN apt-get update && apt-get install -y \
    git unzip zip libicu-dev libonig-dev libzip-dev \
    libxml2-dev libpq-dev libjpeg-dev libpng-dev libfreetype6-dev \
    curl libcurl4-openssl-dev libssl-dev gnupg \
    && docker-php-ext-install pdo pdo_mysql intl zip opcache

# Installe Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

FROM php:8.3-fpm

# Install necessary dependencies
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libmariadb-dev-compat \
    libmariadb-dev \
    && docker-php-ext-install pdo pdo_mysql

# Install Composer

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

CMD ["php-fpm"]
FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    libmagickwand-dev zip unzip \
    libjpeg62-turbo-dev libpng-dev libwebp-dev \
    libfreetype6-dev \
    --no-install-recommends \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql gd \
    && pecl install imagick \
    && docker-php-ext-enable imagick

COPY ./docker/php.ini /usr/local/etc/php/php.ini

# Install composer (php package manager)
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy existing application directory contents to the working directory
COPY . /var/www

# Assign permissions of the working directory to the www-data user
RUN chown -R www-data:www-data  \
    /var/www

# Switch to www-data user
USER www-data

RUN chmod -R 775 \
    /var/www/storage \
    /var/www/bootstrap/cache
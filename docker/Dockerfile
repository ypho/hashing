FROM php:8.3-alpine

WORKDIR /var/www/html

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Install PHP Extensions
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/bin/
RUN install-php-extensions xdebug mbstring pcov

# Copy config files
COPY docker/docker-php-ext-xdebug.ini /usr/local/etc/php/conf.d/
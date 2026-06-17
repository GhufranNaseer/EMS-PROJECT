# Use an official PHP runtime
FROM php:8.2-apache
# Enable Apache modules
RUN a2enmod rewrite
# Install any extensions you need
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Install GD extension with JPEG and PNG support
RUN apt-get update \
    && apt-get install -y libfreetype6-dev libjpeg62-turbo-dev libpng-dev libonig-dev libzip-dev unzip git \
    # configure the GD extension to include support for JPEG and PNG image formats
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd

RUN docker-php-ext-install mbstring zip

# Install sendmail for PHP's mail() function
RUN apt-get update && apt-get install -y sendmail && rm -rf /var/lib/apt/lists/*

# Automatically start sendmail and configure /etc/hosts on container startup
RUN sed -i '/#!\/bin\/sh/aservice sendmail restart' /usr/local/bin/docker-php-entrypoint
RUN sed -i '/#!\/bin\/sh/aecho "$(hostname -i)\t$(hostname) $(hostname).localhost" >> /etc/hosts' /usr/local/bin/docker-php-entrypoint

# Install Composer inside the image
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# modify php ini
ADD ./custom-php.ini /usr/local/etc/php/conf.d/custom-php.ini

# Set the working directory to /var/www/html
WORKDIR /var/www/html

# Copy composer files and install dependencies
COPY ./www/composer.json ./
RUN composer install --no-interaction --no-scripts --no-autoloader --prefer-dist

# Copy the source code in /www into the container at /var/www/html
COPY ./www .

# Finish composer autoload generation
RUN composer dump-autoload --optimize
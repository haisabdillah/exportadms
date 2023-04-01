FROM php:8.1-fpm

ENV PHP_MEMORY_LIMIT=128M

WORKDIR /var/www  

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    nginx \
    zip \
    unzip

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

RUN cd /usr/local/etc/php/conf.d/ && \
  echo 'memory_limit = -1' >> /usr/local/etc/php/conf.d/docker-php-ram-limit.ini && \
  echo 'max_execution_time = 600' >> /usr/local/etc/php/conf.d/docker-php-ram-limit.ini

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
COPY . /var/www/

# Copy directory project permission ke container
COPY --chown=www-data:www-data . /var/www/
RUN chown -R www-data:www-data /var/www

ENV COMPOSER_ALLOW_SUPERUSER=1 
RUN composer install

# Expose port 9000
EXPOSE 9000

USER www-data

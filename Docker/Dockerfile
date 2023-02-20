FROM php:8.1-fpm

# Arguments defined in docker-compose.yml
ARG user
ARG uid

ENV PHP_MEMORY_LIMIT=128M

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

RUN cd /usr/local/etc/php/conf.d/ && \
  echo 'memory_limit = -1' >> /usr/local/etc/php/conf.d/docker-php-ram-limit.ini && \
  echo 'max_execution_time = 600' >> /usr/local/etc/php/conf.d/docker-php-ram-limit.ini


# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create system user to run Composer and Artisan Commands
RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

# Set working directory
WORKDIR /var/www

USER $user

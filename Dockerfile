FROM php:8.2-apache

# Arguments for environment variables
ARG APP_NAME
ARG APP_ENV
ARG APP_KEY
ARG APP_DEBUG
ARG APP_URL
ARG DB_CONNECTION
ARG DB_HOST
ARG DB_PORT
ARG DB_DATABASE
ARG DB_USERNAME
ARG DB_PASSWORD
ARG FILESYSTEM_DISK
ARG SESSION_DRIVER
ARG CACHE_DRIVER
ARG QUEUE_CONNECTION
ARG MAIL_MAILER
ARG MAIL_HOST
ARG MAIL_PORT
ARG MAIL_USERNAME
ARG MAIL_PASSWORD
ARG MAIL_ENCRYPTION
ARG MAIL_FROM_ADDRESS
ARG MAIL_FROM_NAME

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libgd-dev \
    libcurl4-openssl-dev

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    xml

# Configure PHP
RUN echo "allow_url_fopen = On" >> /usr/local/etc/php/conf.d/custom.ini \
    && echo "max_execution_time = 600" >> /usr/local/etc/php/conf.d/custom.ini \
    && echo "max_input_time = 600" >> /usr/local/etc/php/conf.d/custom.ini \
    && echo "post_max_size = 1G" >> /usr/local/etc/php/conf.d/custom.ini \
    && echo "memory_limit = 1024M" >> /usr/local/etc/php/conf.d/custom.ini \
    && echo "upload_max_filesize = 1G" >> /usr/local/etc/php/conf.d/custom.ini

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Create storage directory structure
RUN mkdir -p /var/www/html/core/storage/framework/{sessions,views,cache} \
    && mkdir -p /var/www/html/core/storage/logs \
    && mkdir -p /var/www/html/core/public \
    && chown -R www-data:www-data /var/www/html

# Copy application files
COPY . /var/www/html/

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && find /var/www/html/core/storage -type d -exec chmod 755 {} \; \
    && find /var/www/html/core/storage -type f -exec chmod 644 {} \; \
    && find /var/www/html/core/public -type d -exec chmod 755 {} \; \
    && find /var/www/html/core/public -type f -exec chmod 644 {} \;

# Install dependencies
WORKDIR /var/www/html/core
USER www-data
RUN composer install --no-interaction --no-dev --optimize-autoloader
USER root

# Configure Apache
RUN echo '<VirtualHost *:80>\n\
    ServerAdmin webmaster@localhost\n\
    DocumentRoot /var/www/html/core/public\n\
    <Directory /var/www/html/core/public>\n\
        Options Indexes FollowSymLinks\n\
        AllowOverride All\n\
        Require all granted\n\
    </Directory>\n\
    ErrorLog ${APACHE_LOG_DIR}/error.log\n\
    CustomLog ${APACHE_LOG_DIR}/access.log combined\n\
</VirtualHost>' > /etc/apache2/sites-available/000-default.conf

# Enable Apache modules
RUN a2enmod rewrite

# Generate environment file from arguments
RUN echo "APP_NAME=\"${APP_NAME}\"\n\
APP_ENV=${APP_ENV}\n\
APP_KEY=${APP_KEY}\n\
APP_DEBUG=${APP_DEBUG}\n\
APP_URL=${APP_URL}\n\
DB_CONNECTION=${DB_CONNECTION}\n\
DB_HOST=${DB_HOST}\n\
DB_PORT=${DB_PORT}\n\
DB_DATABASE=${DB_DATABASE}\n\
DB_USERNAME=${DB_USERNAME}\n\
DB_PASSWORD=${DB_PASSWORD}\n\
FILESYSTEM_DISK=${FILESYSTEM_DISK}\n\
SESSION_DRIVER=${SESSION_DRIVER}\n\
CACHE_DRIVER=${CACHE_DRIVER}\n\
QUEUE_CONNECTION=${QUEUE_CONNECTION}\n\
MAIL_MAILER=${MAIL_MAILER}\n\
MAIL_HOST=${MAIL_HOST}\n\
MAIL_PORT=${MAIL_PORT}\n\
MAIL_USERNAME=${MAIL_USERNAME}\n\
MAIL_PASSWORD=${MAIL_PASSWORD}\n\
MAIL_ENCRYPTION=${MAIL_ENCRYPTION}\n\
MAIL_FROM_ADDRESS=${MAIL_FROM_ADDRESS}\n\
MAIL_FROM_NAME=\"${MAIL_FROM_NAME}\"" > /var/www/html/core/.env

EXPOSE 80

CMD ["apache2-foreground"]

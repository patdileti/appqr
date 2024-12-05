FROM php:8.2-apache

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

# Copy application files
COPY . /var/www/html/

# Install dependencies
WORKDIR /var/www/html/core
RUN composer install --no-interaction

# Set the correct DocumentRoot and Directory settings
RUN sed -i 's!/var/www/html!/var/www/html/core/public!g' /etc/apache2/sites-available/000-default.conf \
    && sed -i 's#Directory /var/www/>#Directory /var/www/html/core/public>#g' /etc/apache2/apache2.conf

# Enable Apache modules
RUN a2enmod rewrite

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/core/storage

EXPOSE 80

CMD ["apache2-foreground"]

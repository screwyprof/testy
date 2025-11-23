ARG PHP_VERSION=8.3
FROM php:${PHP_VERSION}-apache

# Set working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && export DEBIAN_FRONTEND=noninteractive \
    && apt-get install -y \
    mariadb-client \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libicu-dev \
    zip \
    unzip \
    curl \
    git \
    && apt-get clean -y && rm -rf /var/lib/apt/lists/*

# Update user args for file permissions
ARG USER_UID=1000
ARG USER_GID=$USER_UID
RUN if [ "$USER_GID" != "1000" ] || [ "$USER_UID" != "1000" ]; then \
        groupmod --gid $USER_GID www-data && \
        usermod --uid $USER_UID --gid $USER_GID www-data; \
    fi

# Configure GD with JPEG support first
RUN docker-php-ext-configure gd --with-freetype --with-jpeg

# Install PHP extensions needed for Zend Framework 1 and the application
RUN docker-php-ext-install \
    mysqli \
    pdo \
    pdo_mysql \
    gd \
    zip \
    exif \
    opcache \
    bcmath \
    intl \
    mbstring \
    soap \
    xml \
    ctype \
    session \
    simplexml \
    dom \
    && docker-php-ext-enable mysqli pdo_mysql

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Configure Apache
RUN a2enmod rewrite \
    && a2enmod headers \
    && sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# Create log directory
RUN mkdir -p /var/log/apache2 && chown www-data:www-data /var/log/apache2

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html


# Install application dependencies
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader

# Copy application files
COPY . .

# Set proper permissions for all files
RUN chown -R www-data:www-data /var/www/html

# Expose port 80
EXPOSE 80

# Use apache2-foreground as the command
CMD ["apache2-foreground"]
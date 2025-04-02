# Use an official PHP image with Apache
FROM php:8.1-apache

# Install dependencies and PHP extensions
RUN apt-get update && apt-get install -y libpng-dev libjpeg-dev libfreetype6-dev libmariadb-dev \
    && docker-php-ext-install mysqli pdo pdo_mysql

# Copy custom PHP configuration
COPY php.ini /usr/local/etc/php/

# Copy your application code into the container
COPY . /var/www/html/

# Expose port 80 for the web server
EXPOSE 80

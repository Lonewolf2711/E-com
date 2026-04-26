FROM php:8.2-apache

# Install required extensions
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy application files to the container
COPY . /var/www/html/

# Set appropriate permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Define PORT 80 and expose it so Railway routes traffic here automatically
ENV PORT=80
EXPOSE 80


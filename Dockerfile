FROM php:8.2-apache

# Install required extensions
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Enable Apache mod_rewrite and ensure only one MPM is loaded (mpm_prefork)
RUN a2dismod mpm_event mpm_worker 2>/dev/null || true \
    && a2enmod mpm_prefork rewrite

# Copy application files to the container
COPY . /var/www/html/

# Set appropriate permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# At runtime, point Apache at Railway's $PORT (default 80 locally), then start.
# Targeted replacements only — avoids matching unrelated "80" substrings.
CMD ["sh", "-c", "PORT=\"${PORT:-80}\" && \
    echo \"Listen ${PORT}\" > /etc/apache2/ports.conf && \
    sed -i \"s|<VirtualHost \\*:[0-9]\\+>|<VirtualHost *:${PORT}>|g\" /etc/apache2/sites-available/000-default.conf && \
    echo \"Apache starting on port ${PORT}\" && \
    exec docker-php-entrypoint apache2-foreground"]


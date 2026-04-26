FROM php:8.2-apache

# Install required extensions
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Enable mod_rewrite (MPM cleanup happens at runtime in CMD to bypass build caching)
RUN a2enmod rewrite

# Copy application files to the container
COPY . /var/www/html/

# Set appropriate permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Runtime: force a single MPM (prefork) by deleting any other MPM symlinks,
# then bind Apache to Railway's $PORT and start.
CMD ["sh", "-c", "set -e; \
    find /etc/apache2/mods-enabled/ -name 'mpm_event*' -delete; \
    find /etc/apache2/mods-enabled/ -name 'mpm_worker*' -delete; \
    a2enmod mpm_prefork >/dev/null; \
    PORT=\"${PORT:-80}\"; \
    echo \"Listen ${PORT}\" > /etc/apache2/ports.conf; \
    sed -i \"s|<VirtualHost \\*:[0-9]\\+>|<VirtualHost *:${PORT}>|g\" /etc/apache2/sites-available/000-default.conf; \
    echo \"=== mods-enabled at startup ===\"; \
    ls /etc/apache2/mods-enabled/ | grep -i mpm; \
    echo \"=== Apache starting on port ${PORT} ===\"; \
    exec docker-php-entrypoint apache2-foreground"]

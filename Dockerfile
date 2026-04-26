FROM php:8.2-apache

# Install required extensions
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Force a single MPM (prefork) by removing ALL MPM symlinks then enabling prefork.
# Also enable mod_rewrite. The ls at the end is a build-log sanity check.
RUN rm -f /etc/apache2/mods-enabled/mpm_*.load /etc/apache2/mods-enabled/mpm_*.conf \
    && a2enmod mpm_prefork rewrite \
    && echo "--- mods-enabled after fix ---" \
    && ls /etc/apache2/mods-enabled/ | grep mpm_

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


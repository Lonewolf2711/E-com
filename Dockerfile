FROM php:8.2-apache

# Install required extensions
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Force a single MPM (prefork). Diagnostic dumps included so the build
# log shows where any extra MPM LoadModule lines are coming from.
RUN echo "=== mods-enabled BEFORE ===" \
    && ls /etc/apache2/mods-enabled/ | grep -i mpm || true \
    && echo "=== conf-enabled ===" \
    && ls /etc/apache2/conf-enabled/ || true \
    && echo "=== all LoadModule mpm references in /etc/apache2 ===" \
    && grep -rin "LoadModule.*mpm" /etc/apache2/ || true \
    && rm -f /etc/apache2/mods-enabled/mpm_*.load /etc/apache2/mods-enabled/mpm_*.conf \
    && a2enmod mpm_prefork rewrite \
    && echo "=== mods-enabled AFTER ===" \
    && ls /etc/apache2/mods-enabled/ | grep -i mpm \
    && echo "=== LoadModule mpm references AFTER ===" \
    && grep -rin "LoadModule.*mpm" /etc/apache2/ || true

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
    echo '=== RUNTIME mods-enabled (mpm) ===' && \
    ls -la /etc/apache2/mods-enabled/ | grep -i mpm || echo '(no mpm in mods-enabled)' && \
    echo '=== RUNTIME LoadModule mpm refs ===' && \
    grep -rin 'LoadModule.*mpm' /etc/apache2/ || echo '(no LoadModule mpm refs)' && \
    echo \"=== Apache starting on port ${PORT} ===\" && \
    exec docker-php-entrypoint apache2-foreground"]


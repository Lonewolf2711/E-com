#!/bin/sh
set -e

# Default to 80 if PORT is not set
if [ -z "$PORT" ]; then
  PORT=80
fi

# Update apache configuration to listen on the Railway provided PORT
sed -i "s/80/$PORT/g" /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

# Fallback to standard entrypoint
exec docker-php-entrypoint "$@"

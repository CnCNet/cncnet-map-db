#!/bin/sh

# Exit immediately if a command exits with a non-zero status
set -e

# Run Laravel optimizations
php /app/artisan optimize

# Start the application
exec php /app/artisan octane:frankenphp
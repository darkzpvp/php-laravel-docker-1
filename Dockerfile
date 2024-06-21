# Use the richarvey/nginx-php-fpm base image
FROM richarvey/nginx-php-fpm:3.1.6

# Install PostgreSQL client libraries and PHP PostgreSQL extension
RUN apk --no-cache add postgresql-dev \
    && docker-php-ext-install pdo pdo_pgsql

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Clear Composer cache
RUN composer clear-cache

# Update Composer to the latest version
RUN composer self-update --2

# Set the working directory
WORKDIR /var/www/html

# Copy the Laravel application files into the container
COPY . .

# Copy the .env file into the container
COPY .env /var/www/html/.env

# Update Composer dependencies (if needed)
RUN composer update --no-interaction --no-scripts
# Install Composer dependencies for production
RUN composer install --no-dev --no-interaction --optimize-autoloader

# Publish Sanctum configuration
RUN php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"

# Debugging: Check current directory content
RUN ls -la

# Run Laravel migrations with --force flag
RUN php artisan migrate --force

# Debugging: Check migration status
RUN php artisan migrate:status

# Define the command to start the container
CMD ["/start.sh"]

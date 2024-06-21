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

# Update Composer dependencies (if needed)
RUN composer update --no-interaction --no-scripts
# Install Composer dependencies for production
RUN composer install --no-dev --no-interaction --optimize-autoloader

# Publish Sanctum configuration
RUN php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
# Run Laravel migrations
RUN php artisan migrate

# Set up environment variables for the container
ENV SKIP_COMPOSER=1
ENV WEBROOT=/var/www/html/public
ENV PHP_ERRORS_STDERR=1
ENV RUN_SCRIPTS=1
ENV REAL_IP_HEADER=1
ENV COMPOSER_ALLOW_SUPERUSER=1

# Add PostgreSQL environment variables
ENV DB_CONNECTION=pgsql
ENV DB_HOST=dpg-cpq6vdiju9rs739vhhc0-a.oregon-postgres.render.com
ENV DB_PORT=5432
ENV DB_DATABASE=backend_tk3k
ENV DB_USERNAME=backend_tk3k_user
ENV DB_PASSWORD=nThLcF8blTLgnJ7y4JKlugyIrSRyfbf5

# Define the command to start the container
CMD ["/start.sh"]

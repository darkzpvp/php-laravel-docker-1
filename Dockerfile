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

# Define environment variables for PostgreSQL
ENV DB_CONNECTION=pgsql
ENV DB_HOST=dpg-cpq6vdiju9rs739vhhc0-a.oregon-postgres.render.com
ENV DB_PORT=5432
ENV DB_DATABASE=backend_tk3k
ENV DB_USERNAME=backend_tk3k_user
ENV DB_PASSWORD=nThLcF8blTLgnJ7y4JKlugyIrSRyfbf5

# Create .env file with environment variables
RUN echo "DB_CONNECTION=\${DB_CONNECTION}" >> .env \
    && echo "DB_HOST=\${DB_HOST}" >> .env \
    && echo "DB_PORT=\${DB_PORT}" >> .env \
    && echo "DB_DATABASE=\${DB_DATABASE}" >> .env \
    && echo "DB_USERNAME=\${DB_USERNAME}" >> .env \
    && echo "DB_PASSWORD=\${DB_PASSWORD}" >> .env \
    && echo "" >> .env \
    && echo "MAIL_MAILER=smtp" >> .env \
    && echo "MAIL_HOST=smtp.gmail.com" >> .env \
    && echo "MAIL_PORT=587" >> .env \
    && echo "MAIL_USERNAME=victor01val@gmail.com" >> .env \
    && echo "MAIL_PASSWORD='ceog zxpe nqgk agqw'" >> .env \
    && echo "MAIL_ENCRYPTION=tls" >> .env \
    && echo "MAIL_FROM_ADDRESS=victor01val@gmail.com" >> .env \
    && echo "MAIL_FROM_NAME=\${APP_NAME}" >> .env \
    && echo "APP_NAME=ForstAI" >> .env \
    && echo "" >> .env \
    && echo "SKIP_COMPOSER=1" >> .env \
    && echo "WEBROOT=/var/www/html/public" >> .env \
    && echo "PHP_ERRORS_STDERR=1" >> .env \
    && echo "RUN_SCRIPTS=1" >> .env \
    && echo "REAL_IP_HEADER=1" >> .env \
    && echo "COMPOSER_ALLOW_SUPERUSER=1" >> .env

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
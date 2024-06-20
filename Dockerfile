FROM richarvey/nginx-php-fpm:3.1.6

# Instalación de php-pgsql
RUN apk --no-cache add postgresql-dev && docker-php-ext-install pdo pdo_pgsql

# Establecer el directorio de trabajo
WORKDIR /var/www/html

# Copiar la aplicación Laravel
COPY . .



# Configuración de la imagen
ENV SKIP_COMPOSER 1
ENV WEBROOT /var/www/html/public
ENV PHP_ERRORS_STDERR 1
ENV RUN_SCRIPTS 1
ENV REAL_IP_HEADER 1

# Configuración específica de Laravel
ENV APP_ENV production
ENV APP_DEBUG false
ENV LOG_CHANNEL stderr

# Permitir que Composer se ejecute como superusuario
ENV COMPOSER_ALLOW_SUPERUSER 1

CMD ["/start.sh"]

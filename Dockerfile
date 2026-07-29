FROM richarvey/nginx-php-fpm:latest

# Copy your Laravel app files into the container
COPY . /var/www/html

# Point Nginx directly to Laravel's public directory
ENV DOCUMENT_ROOT /var/www/html/public
ENV ERRORS 1

# Install Laravel dependencies
RUN composer install --no-dev --optimize-autoloader

# Expose web port
EXPOSE 8080
FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libssl-dev \
    pkg-config \
    supervisor

# Install PHP extensions
RUN pecl install mongodb \
    && docker-php-ext-enable mongodb \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . .

# Create required directories and set permissions
RUN mkdir -p bootstrap/cache storage/logs storage/framework/{cache,sessions,views} storage/app/public \
    && chown -R www-data:www-data bootstrap/cache storage \
    && chmod -R 755 bootstrap/cache storage

# Install application dependencies
RUN composer install --no-dev --optimize-autoloader

# Generate application key (skip if APP_KEY already exists)
RUN php artisan key:generate --force || echo "Key generation skipped"

# Cache configuration and routes
RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

# Set file permissions
RUN chown -R www-data:www-data /var/www/html/storage \
    && chmod -R 755 /var/www/html/storage

# Configure Supervisor
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Expose port 9000
EXPOSE 9000

# Start PHP-FPM and Supervisor
CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
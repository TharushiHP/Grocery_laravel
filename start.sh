#!/bin/bash
set -e

echo "=== Starting Laravel Application ==="

# Show environment info
echo "Environment: $APP_ENV"
echo "Database: $DB_CONNECTION"
echo "Host: $DB_HOST"

# Wait for database connection
echo "Waiting for database connection..."
for i in {1..30}; do
    if php artisan migrate:status > /dev/null 2>&1; then
        echo "Database connection successful!"
        break
    fi
    echo "Waiting for database... attempt $i/30"
    sleep 2
done

# Run migrations
echo "Running database migrations..."
php artisan migrate --force

# Seed database (non-blocking)
echo "Seeding database..."
php artisan db:seed --force --class=DatabaseSeeder || echo "Database seeding failed, but continuing..."

# Clear and cache config for production
echo "Optimizing for production..."
php artisan config:cache || echo "Config cache failed, continuing..."
php artisan route:cache || echo "Route cache failed, continuing..."

# Start the web server
echo "Starting web server..."
php artisan serve --host=0.0.0.0 --port=$PORT
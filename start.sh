#!/bin/bash
set -e

echo "=== Starting Laravel Application with Custom Script ==="
echo "Current directory: $(pwd)"
echo "PHP version: $(php --version | head -n1)"

# Show environment info
echo "Environment: $APP_ENV"
echo "Database: $DB_CONNECTION"
echo "Host: $DB_HOST"
echo "Port: $PORT"

# Try to run migrations but don't fail if database isn't ready
echo "Attempting database migrations..."
if php artisan migrate --force > /dev/null 2>&1; then
    echo "Database migrations completed successfully!"
else
    echo "Warning: Database migrations failed or database not ready. Continuing without migrations."
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
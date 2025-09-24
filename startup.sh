#!/bin/bash
set -e

echo "=== Laravel Startup Script ==="
echo "Current directory: $(pwd)"
echo "Port: $PORT"

# Set default port if not provided
PORT=${PORT:-8000}

# Show environment variables for debugging
echo "APP_ENV: $APP_ENV"
echo "DB_CONNECTION: $DB_CONNECTION"

# Test basic PHP functionality
echo "PHP Version: $(php --version | head -n1)"

# Check if we can connect to database
echo "Testing database connection..."
php -r "
try {
    \$pdo = new PDO('mysql:host=' . getenv('MYSQLHOST') . ';port=' . getenv('MYSQLPORT') . ';dbname=' . getenv('MYSQLDATABASE'), getenv('MYSQLUSER'), getenv('MYSQLPASSWORD'));
    echo 'Database connection successful!' . PHP_EOL;
} catch (Exception \$e) {
    echo 'Database connection failed: ' . \$e->getMessage() . PHP_EOL;
    echo 'Continuing anyway...' . PHP_EOL;
}
"

# Run Laravel migrations
echo "Running migrations..."
php artisan migrate --force || echo "Migrations failed, continuing..."

# Start Laravel development server
echo "Starting Laravel server on 0.0.0.0:$PORT"
exec php artisan serve --host=0.0.0.0 --port=$PORT
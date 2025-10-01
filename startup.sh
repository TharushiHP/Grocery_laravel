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

# Check if we can connect to database (using Railway environment variables)
echo "Testing database connection..."
php -r "
try {
    \$host = getenv('DB_HOST') ?: 'localhost';
    \$port = getenv('DB_PORT') ?: '3306';
    \$database = getenv('DB_DATABASE') ?: 'railway';
    \$username = getenv('DB_USERNAME') ?: 'root';
    \$password = getenv('DB_PASSWORD') ?: '';
    
    \$pdo = new PDO(\"mysql:host=\$host;port=\$port;dbname=\$database\", \$username, \$password);
    echo 'Database connection successful!' . PHP_EOL;
} catch (Exception \$e) {
    echo 'Database connection failed: ' . \$e->getMessage() . PHP_EOL;
    echo 'Continuing anyway...' . PHP_EOL;
}
"

# Start Laravel development server
echo "Starting Laravel server on 0.0.0.0:$PORT"
exec php artisan serve --host=0.0.0.0 --port=$PORT
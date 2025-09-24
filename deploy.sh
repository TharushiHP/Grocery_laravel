#!/bin/bash

# Simple Laravel deployment script
set -e

echo "=== Laravel Deployment Started ==="

# Create required directories
echo "Creating directories..."
mkdir -p bootstrap/cache storage/logs storage/framework/{cache,sessions,views} storage/app/public

# Install dependencies
echo "Installing PHP dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

# Install and build assets
echo "Building assets..."
npm ci
npm run build

# Set permissions
echo "Setting permissions..."
chmod -R 755 storage bootstrap/cache
chmod -R 755 public/build || true

# Generate app key
echo "Generating application key..."
php artisan key:generate --force || true

# Create storage link
php artisan storage:link || true

echo "=== Deployment Complete ==="
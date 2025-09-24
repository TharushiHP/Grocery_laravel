#!/bin/bash

# Railway deployment script for Laravel
set -e

echo "=== Starting Laravel deployment ==="

# Install PHP dependencies (production only)
echo "Installing PHP dependencies..."
composer install --optimize-autoloader --no-dev --no-interaction --prefer-dist

# Clear any cached config
echo "Clearing configuration cache..."
php artisan config:clear || true
php artisan cache:clear || true

# Install Node dependencies (including dev dependencies for build)
echo "Installing Node dependencies..."  
npm ci --silent

echo "Building assets..."
npm run build

# Remove node_modules after build to save space
echo "Cleaning up node_modules..."
rm -rf node_modules

# Create storage directories
echo "Setting up storage..."
mkdir -p storage/logs
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions  
mkdir -p storage/framework/views
mkdir -p storage/app/public
mkdir -p storage/documents
touch storage/logs/laravel.log

# Set proper permissions
chmod -R 755 storage
chmod -R 755 bootstrap/cache

# Create storage link
echo "Creating storage link..."
php artisan storage:link || true

echo "=== Deployment preparation complete ==="
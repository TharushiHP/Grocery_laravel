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

# Install Node dependencies and build assets
echo "Installing Node dependencies..."  
npm ci --only=production --silent

echo "Building assets..."
npm run build

# Create storage directories
echo "Setting up storage..."
mkdir -p storage/logs
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions  
mkdir -p storage/framework/views
mkdir -p storage/app/public
touch storage/logs/laravel.log

# Create storage link
echo "Creating storage link..."
php artisan storage:link || true

echo "=== Deployment preparation complete ==="
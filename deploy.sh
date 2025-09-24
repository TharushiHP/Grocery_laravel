#!/bin/bash

# Railway deployment script for Laravel
set -e

echo "=== Starting Laravel deployment ==="

# Create necessary directories first
echo "Creating required directories..."
mkdir -p bootstrap/cache
mkdir -p storage/logs
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions  
mkdir -p storage/framework/views
mkdir -p storage/app/public
mkdir -p storage/documents
mkdir -p database
touch storage/logs/laravel.log

# MySQL database will be provided by Railway service

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

# Ensure public/build directory has correct permissions and exists
mkdir -p public/build
chmod -R 755 public/build || true
chmod -R 755 public/images || true

# Ensure Laravel can detect the build manifest
ls -la public/build/ || echo "Build directory contents check failed"

# Remove node_modules after build to save space
echo "Cleaning up node_modules..."
rm -rf node_modules

# Set proper permissions
echo "Setting permissions..."
chmod -R 755 storage
chmod -R 755 bootstrap/cache

# Create storage link
echo "Creating storage link..."
php artisan storage:link || true

# Generate application key if not present
echo "Generating application key..."
php artisan key:generate --force --no-interaction || echo "Key generation failed"

# Startup script is already included as startup.sh file
echo "Startup script ready"
chmod +x startup.sh || echo "Could not set executable permissions"

echo "=== Deployment preparation complete ==="
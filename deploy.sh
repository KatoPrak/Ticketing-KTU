#!/bin/bash

# Laravel Deployment Script for Ubuntu Server
# This script automates the deployment process

echo "========================================="
echo "Laravel Deployment Script"
echo "========================================="

# Set proper permissions for Laravel directories
echo "Setting permissions..."
sudo chown -R www-data:www-data /var/www/ticketing
sudo chmod -R 755 /var/www/ticketing
sudo chmod -R 775 /var/www/ticketing/storage
sudo chmod -R 775 /var/www/ticketing/bootstrap/cache

# Install/Update Composer dependencies
echo "Installing Composer dependencies..."
cd /var/www/ticketing
composer install --optimize-autoloader --no-dev

# Install NPM dependencies and build assets
echo "Building frontend assets..."
npm install
npm run build

# Run Laravel optimization commands
echo "Optimizing Laravel..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run database migrations
echo "Running database migrations..."
php artisan migrate --force

# Clear and cache everything
echo "Clearing caches..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Recache for production
echo "Caching for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set final permissions
echo "Setting final permissions..."
sudo chown -R www-data:www-data /var/www/ticketing
sudo chmod -R 755 /var/www/ticketing
sudo chmod -R 775 /var/www/ticketing/storage
sudo chmod -R 775 /var/www/ticketing/bootstrap/cache

echo "========================================="
echo "Deployment completed successfully!"
echo "========================================="

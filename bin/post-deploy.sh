#!/bin/bash

echo "Running post-deploy script..."

# Clear any existing caches first
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Run migrations
php artisan migrate --force

# Seed the database with themes if needed
php artisan db:seed --class=ThemeSeeder --force

# Cache configuration for better performance
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Post-deploy script completed."
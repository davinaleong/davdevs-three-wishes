#!/bin/bash

echo "Running post-deploy script..."

# Run migrations
php artisan migrate --force

# Seed the database with themes if needed
php artisan db:seed --class=ThemeSeeder --force

# Cache configuration for better performance
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Build assets
npm ci --only=production
npm run build

echo "Post-deploy script completed."
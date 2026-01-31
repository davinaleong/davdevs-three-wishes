#!/bin/bash

echo "🚀 Deploying Three Wishes App to Heroku..."

# Check if we're in git repo
if [ ! -d ".git" ]; then
    echo "❌ Error: Not in a git repository"
    exit 1
fi

# Check for uncommitted changes
if [ -n "$(git status --porcelain)" ]; then
    echo "⚠️  Warning: You have uncommitted changes"
    echo "Please commit your changes before deploying:"
    git status --short
    exit 1
fi

# Run tests before deployment
echo "🧪 Running tests..."
if ! php artisan test --stop-on-failure; then
    echo "❌ Tests failed! Deployment aborted."
    exit 1
fi

echo "✅ All tests passed!"

# Build assets locally for verification
echo "🔧 Building assets for verification..."
if ! npm run build; then
    echo "❌ Asset build failed! Deployment aborted."
    exit 1
fi

echo "✅ Assets built successfully!"

# Deploy to Heroku
echo "📤 Deploying to Heroku..."
if ! git push heroku main; then
    echo "❌ Heroku deployment failed!"
    exit 1
fi

echo "🎉 Deployment successful!"
echo ""
echo "Next steps:"
echo "1. Check logs: heroku logs --tail --app davdevs-three-wishes"
echo "2. Open app: heroku open --app davdevs-three-wishes"
echo "3. Run migrations if needed: heroku run php artisan migrate --app davdevs-three-wishes"
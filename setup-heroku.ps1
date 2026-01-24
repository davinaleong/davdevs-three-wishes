# Heroku Setup Script
# This script helps you set up Heroku with test automation

Write-Host "🚀 Setting up Heroku with Test Automation..." -ForegroundColor Green
Write-Host ""

# Check if Heroku CLI is installed
try {
    $null = heroku --version 2>&1
    Write-Host "✅ Heroku CLI is installed" -ForegroundColor Green
} catch {
    Write-Host "❌ Heroku CLI not found. Please install it first:" -ForegroundColor Red
    Write-Host "   https://devcenter.heroku.com/articles/heroku-cli" -ForegroundColor Yellow
    exit 1
}

# Get app name
$appName = Read-Host "Enter your Heroku app name (default: davdevs-three-wishes)"
if ([string]::IsNullOrWhiteSpace($appName)) {
    $appName = "davdevs-three-wishes"
}

Write-Host ""
Write-Host "Setting up Heroku app: $appName" -ForegroundColor Cyan

# Add Heroku remote
try {
    git remote add heroku https://git.heroku.com/$appName.git
    Write-Host "✅ Added Heroku remote" -ForegroundColor Green
} catch {
    Write-Host "ℹ️  Heroku remote might already exist, updating..." -ForegroundColor Yellow
    git remote set-url heroku https://git.heroku.com/$appName.git
}

# Set up Heroku buildpacks
Write-Host "Setting up buildpacks..." -ForegroundColor Yellow
heroku buildpacks:set heroku/php --app $appName
heroku buildpacks:add --index 1 heroku/nodejs --app $appName

# Add PostgreSQL addon
Write-Host "Adding PostgreSQL database..." -ForegroundColor Yellow
heroku addons:create heroku-postgresql:essential-0 --app $appName

Write-Host ""
Write-Host "🎉 Heroku setup complete!" -ForegroundColor Green
Write-Host ""
Write-Host "Next steps:" -ForegroundColor Cyan
Write-Host "1. Set up your environment variables using: heroku-env-setup.txt" -ForegroundColor White
Write-Host "2. Push to deploy: git push heroku main" -ForegroundColor White
Write-Host "3. Tests will automatically run before deployment!" -ForegroundColor White
Write-Host ""
Write-Host "Note: The pre-push hook will run tests before pushing to Heroku" -ForegroundColor Yellow
Write-Host "      GitHub Actions will also run tests and deploy automatically" -ForegroundColor Yellow
Write-Host ""
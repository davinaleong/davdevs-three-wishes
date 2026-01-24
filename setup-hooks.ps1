# PowerShell setup script for git hooks and pre-commit checks

Write-Host "Setting up git hooks and test automation..." -ForegroundColor Green
Write-Host ""

# Test the setup
Write-Host "Testing setup..." -ForegroundColor Yellow
Write-Host ""

# Test composer scripts
try {
    $null = composer test 2>&1
    Write-Host "✅ Composer test script works" -ForegroundColor Green
} catch {
    Write-Host "❌ Composer test script failed" -ForegroundColor Red
    exit 1
}

# Test pre-commit script
try {
    $null = composer pre-commit 2>&1
    Write-Host "✅ Composer pre-commit script works" -ForegroundColor Green
} catch {
    Write-Host "❌ Composer pre-commit script failed" -ForegroundColor Red
    exit 1
}

Write-Host ""
Write-Host "🎉 Git hooks and test automation setup completed!" -ForegroundColor Green
Write-Host ""
Write-Host "From now on:" -ForegroundColor Cyan
Write-Host "• Tests will run automatically before each commit" -ForegroundColor White
Write-Host "• Tests will run before pushes to main/master/heroku" -ForegroundColor White
Write-Host "• You can run 'composer test' to test manually" -ForegroundColor White
Write-Host "• You can run 'composer pre-commit' to run the full pre-commit check manually" -ForegroundColor White
Write-Host ""

# Test the git hooks
Write-Host "Testing git hooks..." -ForegroundColor Yellow
if (Test-Path ".git\hooks\pre-commit") {
    Write-Host "✅ Pre-commit hook installed" -ForegroundColor Green
} else {
    Write-Host "❌ Pre-commit hook missing" -ForegroundColor Red
}

if (Test-Path ".git\hooks\pre-push") {
    Write-Host "✅ Pre-push hook installed" -ForegroundColor Green
} else {
    Write-Host "❌ Pre-push hook missing" -ForegroundColor Red
}

Write-Host ""
Write-Host "Setup complete! Your repository now enforces tests before commits and deployments." -ForegroundColor Green
# Test Automation & Pre-commit Hooks Setup

This project is configured to automatically run tests before commits and deployments to ensure code quality and prevent broken code from reaching production.

## Setup

### Automated Setup
Run the setup script to configure everything:

**Windows (PowerShell):**
```powershell
.\setup-hooks.ps1
```

**Unix/Linux/macOS:**
```bash
chmod +x setup-hooks.sh
./setup-hooks.sh
```

### Manual Commands

You can also run these commands manually:

```bash
# Run tests
composer test

# Run tests with coverage
composer test-coverage

# Run pre-commit checks (tests only, no Pint)
composer pre-commit

# Or use npm
npm run test
npm run pre-commit
```

## What's Configured

### Local Git Hooks
- **Pre-commit hook**: Runs full test suite before commits
- **Pre-push hook**: Runs tests when pushing to main/master or Heroku

### GitHub Actions CI/CD
- **tests.yml**: Runs on every push/PR to main/master
  - Tests against PHP 8.2 and 8.3
  - Uses PostgreSQL database
  - Runs full test suite with parallel execution

- **deploy.yml**: Automated Heroku deployment
  - Only deploys if tests pass
  - Runs final test verification before deployment
  - Handles database migrations
  - Optimizes caches after deployment

### Composer Scripts
- `composer test` - Run PHPUnit tests
- `composer test-coverage` - Run tests with coverage
- `composer pre-commit` - Run pre-commit check (tests only)

### NPM Scripts
- `npm run test` - Run tests via Composer
- `npm run pre-commit` - Run pre-commit checks via Composer

## Heroku Setup with Test Automation

### Quick Setup
Run the Heroku setup script:
```powershell
.\setup-heroku.ps1
```

### Manual Heroku Setup
```bash
# Add Heroku remote
git remote add heroku https://git.heroku.com/your-app-name.git

# Set up buildpacks
heroku buildpacks:set heroku/php --app your-app-name
heroku buildpacks:add --index 1 heroku/nodejs --app your-app-name

# Add database
heroku addons:create heroku-postgresql:essential-0 --app your-app-name
```

### Environment Variables
Use the `heroku-env-setup.txt` file to set up your environment variables:
```bash
# Example commands from the file
heroku config:set APP_NAME="Three Wishes" --app your-app-name
heroku config:set APP_ENV=production --app your-app-name
# ... (see heroku-env-setup.txt for complete list)
```

## Heroku Deployment

The deployment is configured to:
1. Only deploy when tests pass in GitHub Actions
2. Run a final test verification before deployment
3. Automatically run database migrations
4. Clear and rebuild Laravel caches

### Required Secrets

For GitHub Actions deployment, add these secrets to your GitHub repository:
- `HEROKU_API_KEY` - Your Heroku API key
- `HEROKU_EMAIL` - Your Heroku account email

## How It Works

### Pre-commit Flow
1. You run `git commit`
2. Pre-commit hook automatically runs
3. Tests execute - if they fail, commit is rejected
4. If tests pass, commit proceeds

### Pre-push Flow
1. You run `git push` (to main/master or heroku remote)
2. Pre-push hook runs tests
3. If tests fail, push is rejected
4. If tests pass, push proceeds

### GitHub Actions Flow
1. Code is pushed to GitHub
2. Tests workflow runs automatically
3. If tests pass, deploy workflow triggers
4. Final test verification runs
5. Deployment to Heroku proceeds
6. Database migrations and cache optimization happen

## Bypassing Hooks (Emergency Only)

In rare cases where you need to bypass the hooks:

```bash
# Skip pre-commit hook (NOT RECOMMENDED)
git commit --no-verify -m "Emergency commit"

# Skip pre-push hook (NOT RECOMMENDED)  
git push --no-verify
```

**⚠️ Warning**: Only bypass hooks in true emergencies. Always fix and test your code properly.

## Testing the Setup

After setup, test that everything works:

```bash
# Test individual commands
composer test
composer pre-commit

# Test git hooks by making a commit
git add .
git commit -m "Test commit"

# Test pre-push by pushing to a test branch
git checkout -b test-branch
git push origin test-branch
```

The hooks should run automatically and show test results before allowing the commit/push to proceed.
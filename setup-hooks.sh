#!/bin/bash

# Setup script for git hooks and pre-commit checks

echo "Setting up git hooks..."

# Make hooks executable (for Unix-based systems)
if [[ "$OSTYPE" != "msys" && "$OSTYPE" != "cygwin" ]]; then
    chmod +x .git/hooks/pre-commit
    chmod +x .git/hooks/pre-push
    echo "✅ Git hooks made executable"
else
    echo "ℹ️  Windows detected - hooks should work without chmod"
fi

# Test the setup
echo ""
echo "Testing setup..."
echo ""

# Test composer scripts
if composer test > /dev/null 2>&1; then
    echo "✅ Composer test script works"
else
    echo "❌ Composer test script failed"
    exit 1
fi

# Test pre-commit script
if composer pre-commit > /dev/null 2>&1; then
    echo "✅ Composer pre-commit script works"
else
    echo "❌ Composer pre-commit script failed"
    exit 1
fi

echo ""
echo "🎉 Git hooks and test automation setup completed!"
echo ""
echo "From now on:"
echo "• Tests will run automatically before each commit"
echo "• Tests will run before pushes to main/master/heroku"
echo "• You can run 'composer test' to test manually"
echo "• You can run 'composer pre-commit' to run the full pre-commit check manually"
echo ""
#!/bin/bash

# Git Setup Script for The Bargain Repository
# This script helps you push your code to GitHub

echo "🚀 Setting up Git repository for The Bargain..."

# Check if git is installed
if ! command -v git &> /dev/null; then
    echo "❌ Git is not installed. Please install Git first."
    exit 1
fi

# Initialize git if not already done
if [ ! -d ".git" ]; then
    echo "📦 Initializing Git repository..."
    git init
else
    echo "✅ Git repository already initialized"
fi

# Add remote if it doesn't exist
if ! git remote | grep -q "origin"; then
    echo "🔗 Adding GitHub remote..."
    git remote add origin https://github.com/richardawe/thebargain.git
else
    echo "✅ Remote 'origin' already exists"
    echo "🔄 Updating remote URL..."
    git remote set-url origin https://github.com/richardawe/thebargain.git
fi

# Add all files
echo "📝 Adding files to Git..."
git add .

# Check if there are changes to commit
if git diff --staged --quiet; then
    echo "ℹ️  No changes to commit"
else
    echo "💾 Committing changes..."
    git commit -m "Initial commit - The Bargain website with GitHub Actions deployment"
fi

# Set main branch
echo "🌿 Setting main branch..."
git branch -M main

# Push to GitHub
echo "⬆️  Pushing to GitHub..."
echo ""
echo "⚠️  You may be prompted for GitHub credentials"
echo "   If you haven't set up authentication, you may need to:"
echo "   1. Use a Personal Access Token (recommended)"
echo "   2. Or set up SSH keys"
echo ""
read -p "Press Enter to continue with push..."

git push -u origin main

if [ $? -eq 0 ]; then
    echo ""
    echo "✅ Successfully pushed to GitHub!"
    echo "🔗 Repository: https://github.com/richardawe/thebargain"
    echo ""
    echo "📋 Next steps:"
    echo "   1. Go to https://github.com/richardawe/thebargain/settings/secrets/actions"
    echo "   2. Add the required secrets (see DEPLOYMENT_SETUP.md)"
    echo "   3. Push again or manually trigger the workflow"
else
    echo ""
    echo "❌ Push failed. Please check:"
    echo "   - GitHub credentials"
    echo "   - Repository access permissions"
    echo "   - Internet connection"
fi

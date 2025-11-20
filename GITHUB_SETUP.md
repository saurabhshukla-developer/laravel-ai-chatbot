# GitHub Repository Setup Guide

This guide will help you set up and publish this package to GitHub.

## Repository Name

**Suggested Repository Name:** `laravel-ai-chatbot`

**Full GitHub URL:** `https://github.com/saurabhshukla-developer/laravel-ai-chatbot`

## Step-by-Step Setup

### 1. Create Repository on GitHub

1. Go to [GitHub](https://github.com/new)
2. Repository name: `laravel-ai-chatbot`
3. Description: `A Laravel package for building AI agents with configurable API keys`
4. Visibility: Choose Public or Private
5. **DO NOT** initialize with README, .gitignore, or license (we already have these)
6. Click "Create repository"

### 2. Initialize Git (if not already done)

```bash
# Navigate to package directory
cd /Applications/MAMP/htdocs/ai/chatbot

# Initialize git (if not already initialized)
git init

# Add all files
git add .

# Make initial commit
git commit -m "Initial commit: Laravel AI Chatbot Package"
```

### 3. Connect to GitHub Repository

```bash
# Add remote repository
git remote add origin https://github.com/saurabhshukla-developer/laravel-ai-chatbot.git

# Verify remote
git remote -v
```

### 4. Push to GitHub

```bash
# Push to main branch
git branch -M main
git push -u origin main
```

### 5. Create Release Tag (Optional but Recommended)

```bash
# Create a version tag
git tag -a v1.0.0 -m "Initial release: Laravel AI Chatbot Package v1.0.0"

# Push tags
git push origin v1.0.0
```

### 6. Set Up GitHub Actions (Already Configured)

The repository includes GitHub Actions workflows:
- **Tests**: Runs PHPUnit tests on push/PR
- **PHPStan**: Runs static analysis (optional)

These will automatically run when you push code.

### 7. Configure Repository Settings

1. Go to repository Settings
2. **General**:
   - Add topics: `laravel`, `chatbot`, `ai`, `openai`, `anthropic`, `php`, `package`
   - Add description: `A Laravel package for building AI agents with configurable API keys`
   - Enable Issues
   - Enable Discussions (optional)
   - Enable Wiki (optional)

3. **Pages** (optional):
   - Enable GitHub Pages if you want documentation site

4. **Secrets** (for CI/CD):
   - Add any required secrets if needed for automated releases

## Installation from GitHub

Users can install your package in two ways:

### Method 1: Via Composer (VCS)

Add to `composer.json`:

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/saurabhshukla-developer/laravel-ai-chatbot"
        }
    ],
    "require": {
        "saurabhshukla-developer/laravel-ai-chatbot": "*"
    }
}
```

Then run:
```bash
composer require saurabhshukla-developer/laravel-ai-chatbot
```

### Method 2: Direct Clone

```bash
git clone https://github.com/saurabhshukla-developer/laravel-ai-chatbot.git
cd laravel-ai-chatbot
composer install
```

## Publishing to Packagist (Optional)

If you want to publish to Packagist for easier installation:

1. Go to [Packagist](https://packagist.org/)
2. Sign up/Login
3. Click "Submit"
4. Enter repository URL: `https://github.com/saurabhshukla-developer/laravel-ai-chatbot`
5. Click "Check"
6. Follow the instructions

After publishing, users can install with:
```bash
composer require saurabhshukla-developer/laravel-ai-chatbot
```

## Repository Structure

Your repository should have:

```
laravel-ai-chatbot/
├── .github/
│   ├── workflows/
│   │   ├── tests.yml
│   │   └── phpstan.yml
│   ├── ISSUE_TEMPLATE/
│   │   ├── bug_report.md
│   │   └── feature_request.md
│   └── PULL_REQUEST_TEMPLATE.md
├── config/
├── database/
├── resources/
├── routes/
├── src/
├── tests/
├── .gitignore
├── composer.json
├── CONTRIBUTING.md
├── LICENSE
├── README.md
├── SETUP.md
├── TESTING.md
└── phpunit.xml
```

## Badges for README

The README already includes badges. You can add more:

```markdown
[![GitHub stars](https://img.shields.io/github/stars/saurabhshukla-developer/laravel-ai-chatbot)](https://github.com/saurabhshukla-developer/laravel-ai-chatbot/stargazers)
[![GitHub forks](https://img.shields.io/github/forks/saurabhshukla-developer/laravel-ai-chatbot)](https://github.com/saurabhshukla-developer/laravel-ai-chatbot/network)
[![GitHub issues](https://img.shields.io/github/issues/saurabhshukla-developer/laravel-ai-chatbot)](https://github.com/saurabhshukla-developer/laravel-ai-chatbot/issues)
```

## Next Steps

1. ✅ Create repository on GitHub
2. ✅ Push code to GitHub
3. ✅ Create initial release
4. ✅ Set up repository settings
5. ⬜ (Optional) Publish to Packagist
6. ⬜ Share with the community!

## Quick Commands Reference

```bash
# Check status
git status

# Add changes
git add .

# Commit
git commit -m "Your commit message"

# Push to GitHub
git push origin main

# Create and push tag
git tag -a v1.0.0 -m "Release v1.0.0"
git push origin v1.0.0

# Pull latest changes
git pull origin main
```

## Troubleshooting

### Issue: Remote already exists
```bash
git remote remove origin
git remote add origin https://github.com/saurabhshukla-developer/laravel-ai-chatbot.git
```

### Issue: Authentication failed
- Use Personal Access Token instead of password
- Or use SSH: `git@github.com:saurabhshukla-developer/laravel-ai-chatbot.git`

### Issue: Branch protection
- Go to Settings > Branches
- Add branch protection rules if needed

## Support

If you encounter any issues, check:
- [GitHub Documentation](https://docs.github.com/)
- [Composer Documentation](https://getcomposer.org/doc/)


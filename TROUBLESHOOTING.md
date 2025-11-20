# Troubleshooting Guide

Common issues and solutions when installing the Laravel AI Chatbot package.

## Issue: Minimum Stability Error

### Error Message
```
Your requirements could not be resolved to an installable set of packages.
Problem 1
- Root composer.json requires saurabhshukla-developer/laravel-ai-chatbot *, 
  found saurabhshukla-developer/laravel-ai-chatbot[dev-master] but it does 
  not match your minimum-stability.
```

### Cause
Your Laravel project's `composer.json` has `"minimum-stability": "stable"`, but the package is currently on a `dev-master` branch (development branch), which Composer considers unstable.

### Solution 1: Allow Dev Packages (Quick Fix)

Add this to your Laravel project's `composer.json`:

```json
{
    "minimum-stability": "dev",
    "prefer-stable": true,
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
composer update
```

### Solution 2: Use Dev-Master Explicitly (Recommended for Development)

In your Laravel project's `composer.json`:

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/saurabhshukla-developer/laravel-ai-chatbot"
        }
    ],
    "require": {
        "saurabhshukla-developer/laravel-ai-chatbot": "dev-master"
    }
}
```

Then run:
```bash
composer require saurabhshukla-developer/laravel-ai-chatbot:dev-master
```

### Solution 3: Create a Release Tag (Best for Production)

**On the GitHub repository:**

1. Create a release tag:
   ```bash
   git tag -a v1.0.0 -m "Release version 1.0.0"
   git push origin v1.0.0
   ```

2. Then in your Laravel project, require a specific version:
   ```json
   {
       "repositories": [
           {
               "type": "vcs",
               "url": "https://github.com/saurabhshukla-developer/laravel-ai-chatbot"
           }
       ],
       "require": {
           "saurabhshukla-developer/laravel-ai-chatbot": "^1.0"
       }
   }
   ```

### Solution 4: Use Composer Command (Easiest)

Run this command in your Laravel project:

```bash
composer config minimum-stability dev
composer config prefer-stable true
composer require saurabhshukla-developer/laravel-ai-chatbot:dev-master
```

## Issue: Package Not Found

### Error Message
```
Could not find package saurabhshukla-developer/laravel-ai-chatbot
```

### Solution

Make sure you've added the repository:

```bash
composer config repositories.laravel-ai-chatbot vcs https://github.com/saurabhshukla-developer/laravel-ai-chatbot
composer require saurabhshukla-developer/laravel-ai-chatbot:dev-master
```

## Issue: Authentication Required

### Error Message
```
Authentication required (github.com)
```

### Solution

1. **Use Personal Access Token:**
   - Go to GitHub Settings > Developer settings > Personal access tokens
   - Create a token with `repo` scope
   - Use it when prompted for password

2. **Or use SSH:**
   ```bash
   composer config repositories.laravel-ai-chatbot vcs git@github.com:saurabhshukla-developer/laravel-ai-chatbot.git
   ```

## Issue: Class Not Found

### Error Message
```
Class 'LaravelAI\Chatbot\ChatbotServiceProvider' not found
```

### Solution

1. Clear composer cache:
   ```bash
   composer dump-autoload
   ```

2. Make sure package is installed:
   ```bash
   composer require saurabhshukla-developer/laravel-ai-chatbot:dev-master
   ```

3. Clear Laravel cache:
   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```

## Issue: Migration Errors

### Error Message
```
SQLSTATE[42S01]: Base table or view already exists
```

### Solution

1. Check if tables already exist:
   ```bash
   php artisan migrate:status
   ```

2. If needed, rollback and re-run:
   ```bash
   php artisan migrate:rollback
   php artisan migrate
   ```

## Issue: Routes Not Working

### Error Message
```
404 Not Found on /chatbot/api-keys
```

### Solution

1. Clear route cache:
   ```bash
   php artisan route:clear
   php artisan route:cache
   ```

2. Check if routes are registered:
   ```bash
   php artisan route:list | grep chatbot
   ```

3. Verify service provider is registered in `config/app.php` (should be automatic)

## Issue: API Key Encryption Errors

### Error Message
```
The only supported ciphers are AES-128-CBC and AES-256-CBC
```

### Solution

1. Generate application key:
   ```bash
   php artisan key:generate
   ```

2. Make sure `.env` has `APP_KEY` set

## Quick Fix Commands

Run these commands in order if you're having general issues:

```bash
# Clear all caches
composer dump-autoload
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Reinstall package
composer remove saurabhshukla-developer/laravel-ai-chatbot
composer require saurabhshukla-developer/laravel-ai-chatbot:dev-master

# Publish and migrate
php artisan vendor:publish --provider="LaravelAI\Chatbot\ChatbotServiceProvider"
php artisan migrate
```

## Still Having Issues?

1. Check PHP version: `php -v` (needs 8.1+)
2. Check Laravel version: `php artisan --version` (needs 10.x or 11.x)
3. Check Composer version: `composer --version`
4. Review error logs: `storage/logs/laravel.log`
5. Open an issue on [GitHub](https://github.com/saurabhshukla-developer/laravel-ai-chatbot/issues)


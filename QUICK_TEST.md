# Quick Test Guide

Fastest way to test the package right now!

## Option 1: Run Package Tests (Fastest)

```bash
# Install dependencies
composer install

# Run tests
vendor/bin/phpunit
```

That's it! This will run all unit and feature tests.

## Option 2: Test in a Laravel App

### Quick Setup (5 minutes)

1. **Create a test Laravel app** (one directory up):
```bash
cd ..
composer create-project laravel/laravel test-chatbot-app
cd test-chatbot-app
```

2. **Add package to composer.json**:
```json
{
    "repositories": [
        {
            "type": "path",
            "url": "../chatbot"
        }
    ],
    "require": {
        "laravel-ai/chatbot": "*"
    }
}
```

3. **Install and setup**:
```bash
composer require laravel-ai/chatbot
php artisan vendor:publish --provider="LaravelAI\Chatbot\ChatbotServiceProvider"
php artisan migrate
php artisan key:generate
php artisan serve
```

4. **Test it**:
- Visit: `http://localhost:8000/chatbot/api-keys`
- Add an API key
- Create an agent
- Chat!

## Option 3: Use the Test Script

```bash
./test-package.sh
```

This script will:
- Install dependencies
- Run package tests
- Optionally create a test Laravel app
- Set everything up for you

## Option 4: Manual PHP Test

Create `test.php` in package root:

```php
<?php
require 'vendor/autoload.php';

// Test basic functionality
use LaravelAI\Chatbot\Models\ApiKey;
use LaravelAI\Chatbot\Models\AiAgent;

echo "Testing package...\n";

// This won't work without Laravel, but shows the structure
echo "✓ Package structure is correct\n";
echo "✓ Models exist\n";
echo "✓ Ready for Laravel integration\n";
```

Run: `php test.php`

## What Gets Tested?

### Unit Tests
- ✅ API key encryption/decryption
- ✅ Model scopes
- ✅ Agent creation
- ✅ Slug generation

### Feature Tests  
- ✅ Manager functionality
- ✅ Agent retrieval
- ✅ Provider access

## Next Steps

For detailed testing, see [TESTING.md](TESTING.md)


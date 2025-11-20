# Testing Guide

This guide explains how to test the Laravel AI Chatbot package.

## Quick Start

The fastest way to test the package:

```bash
# Install dependencies
composer install

# Run all tests
vendor/bin/phpunit
```

## Table of Contents

1. [Quick Start](#quick-start)
2. [Running Unit Tests](#running-unit-tests)
3. [Testing in a Laravel Application](#testing-in-a-laravel-application)
4. [Manual Testing](#manual-testing)
5. [Test Coverage](#test-coverage)

## Running Unit Tests

### Prerequisites

Install development dependencies:

```bash
composer install
```

### Run All Tests

```bash
vendor/bin/phpunit
```

Or using PHPUnit directly:

```bash
./vendor/bin/phpunit
```

### Run Specific Test Suites

```bash
# Run only unit tests
vendor/bin/phpunit tests/Unit

# Run only feature tests
vendor/bin/phpunit tests/Feature

# Run a specific test file
vendor/bin/phpunit tests/Unit/Models/ApiKeyTest.php

# Run a specific test method
vendor/bin/phpunit --filter test_api_key_is_encrypted_when_saved
```

### With Coverage

```bash
vendor/bin/phpunit --coverage-html coverage
```

Then open `coverage/index.html` in your browser.

## Testing in a Laravel Application

### Method 1: Create a Test Laravel Application

1. **Create a new Laravel project** (if you don't have one):

```bash
composer create-project laravel/laravel test-chatbot-app
cd test-chatbot-app
```

2. **Add the package as a local path repository:**

Edit `composer.json`:

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "/Applications/MAMP/htdocs/ai/chatbot"
        }
    ],
    "require": {
        "laravel-ai/chatbot": "*"
    }
}
```

3. **Install the package:**

```bash
composer require laravel-ai/chatbot
```

4. **Publish and migrate:**

```bash
php artisan vendor:publish --provider="LaravelAI\Chatbot\ChatbotServiceProvider" --tag="chatbot-config"
php artisan vendor:publish --provider="LaravelAI\Chatbot\ChatbotServiceProvider" --tag="chatbot-migrations"
php artisan migrate
```

5. **Set APP_KEY:**

```bash
php artisan key:generate
```

6. **Start the server:**

```bash
php artisan serve
```

7. **Test the web interface:**

- Visit: `http://localhost:8000/chatbot/api-keys`
- Add an API key
- Create an agent
- Test the chat functionality

### Method 2: Use Tinker for Quick Testing

```bash
php artisan tinker
```

Then in tinker:

```php
use LaravelAI\Chatbot\Facades\Chatbot;
use LaravelAI\Chatbot\Models\ApiKey;
use LaravelAI\Chatbot\Models\AiAgent;

// Create an API key
$apiKey = ApiKey::create([
    'provider' => 'openai',
    'name' => 'Test Key',
    'api_key' => 'sk-test123',
    'is_default' => true,
    'is_active' => true,
]);

// Verify encryption
$apiKey->getDecryptedApiKey(); // Should return 'sk-test123'

// Create an agent
$agent = Chatbot::createAgent([
    'name' => 'Test Bot',
    'slug' => 'test-bot',
    'provider' => 'openai',
    'system_prompt' => 'You are helpful.',
    'is_active' => true,
]);

// Get the agent
$retrieved = Chatbot::getAgent('test-bot');
$retrieved->name; // Should return 'Test Bot'
```

## Manual Testing

### Test Checklist

#### 1. API Key Management

- [ ] Create an API key via web interface
- [ ] Verify API key is encrypted in database
- [ ] Edit an API key
- [ ] Set as default
- [ ] Delete an API key
- [ ] Test with multiple providers

#### 2. AI Agent Management

- [ ] Create an agent via web interface
- [ ] Verify slug is auto-generated
- [ ] Edit an agent
- [ ] View agent details
- [ ] Delete an agent
- [ ] Test with different providers

#### 3. Chat Functionality

- [ ] Send a message via web interface
- [ ] Receive a response
- [ ] Test with different agents
- [ ] Test with different providers
- [ ] Test error handling (invalid API key, etc.)

#### 4. Programmatic Usage

- [ ] Create agent via code
- [ ] Get agent by slug
- [ ] Get agent by ID
- [ ] Chat via code
- [ ] Use different providers

### Manual Test Script

Create a test file `test-manual.php` in your Laravel project root:

```php
<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use LaravelAI\Chatbot\Facades\Chatbot;
use LaravelAI\Chatbot\Models\ApiKey;
use LaravelAI\Chatbot\Models\AiAgent;

echo "=== Testing Laravel AI Chatbot Package ===\n\n";

// Test 1: Create API Key
echo "1. Creating API Key...\n";
$apiKey = ApiKey::create([
    'provider' => 'openai',
    'name' => 'Test Key',
    'api_key' => 'sk-test123',
    'is_default' => true,
    'is_active' => true,
]);
echo "   ✓ API Key created (ID: {$apiKey->id})\n";

// Test 2: Verify encryption
echo "2. Verifying encryption...\n";
$decrypted = $apiKey->getDecryptedApiKey();
if ($decrypted === 'sk-test123') {
    echo "   ✓ Encryption working correctly\n";
} else {
    echo "   ✗ Encryption failed\n";
}

// Test 3: Create Agent
echo "3. Creating AI Agent...\n";
$agent = Chatbot::createAgent([
    'name' => 'Test Bot',
    'slug' => 'test-bot',
    'provider' => 'openai',
    'system_prompt' => 'You are a helpful assistant.',
    'is_active' => true,
]);
echo "   ✓ Agent created (ID: {$agent->id}, Slug: {$agent->slug})\n";

// Test 4: Get Agent
echo "4. Retrieving agent...\n";
$retrieved = Chatbot::getAgent('test-bot');
if ($retrieved && $retrieved->id === $agent->id) {
    echo "   ✓ Agent retrieved successfully\n";
} else {
    echo "   ✗ Failed to retrieve agent\n";
}

// Test 5: Get Provider
echo "5. Getting provider instance...\n";
try {
    $provider = Chatbot::provider('openai');
    echo "   ✓ Provider instance created\n";
} catch (\Exception $e) {
    echo "   ✗ Failed to create provider: {$e->getMessage()}\n";
}

echo "\n=== All tests completed ===\n";
```

Run it:

```bash
php test-manual.php
```

## Test Coverage

Current test coverage includes:

### Unit Tests

- ✅ ApiKey model encryption/decryption
- ✅ ApiKey scopes (active, defaultForProvider)
- ✅ AiAgent model creation
- ✅ AiAgent slug generation
- ✅ AiAgent scopes

### Feature Tests

- ✅ ChatbotManager agent creation
- ✅ ChatbotManager agent retrieval
- ✅ ChatbotManager provider access

### Areas Needing More Tests

- [ ] Provider implementations (OpenAI, Anthropic, Google)
- [ ] HTTP request/response handling
- [ ] Streaming functionality
- [ ] Controller actions
- [ ] Route registration
- [ ] Error handling

## Writing New Tests

### Example Unit Test

```php
<?php

namespace LaravelAI\Chatbot\Tests\Unit;

use LaravelAI\Chatbot\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MyTest extends TestCase
{
    use RefreshDatabase;

    public function test_something()
    {
        // Your test code here
        $this->assertTrue(true);
    }
}
```

### Example Feature Test

```php
<?php

namespace LaravelAI\Chatbot\Tests\Feature;

use LaravelAI\Chatbot\Facades\Chatbot;
use LaravelAI\Chatbot\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MyFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_something()
    {
        // Your test code here
        $result = Chatbot::someMethod();
        $this->assertNotNull($result);
    }
}
```

## Continuous Integration

### GitHub Actions Example

Create `.github/workflows/tests.yml`:

```yaml
name: Tests

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest
    
    strategy:
      matrix:
        php: [8.1, 8.2, 8.3]
        laravel: [10.*, 11.*]
    
    steps:
      - uses: actions/checkout@v3
      
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: ${{ matrix.php }}
          extensions: pdo, pdo_sqlite
      
      - name: Install Dependencies
        run: composer install
      
      - name: Run Tests
        run: vendor/bin/phpunit
```

## Troubleshooting Tests

### Issue: Tests can't find classes

**Solution:** Run `composer dump-autoload`

### Issue: Database errors

**Solution:** Make sure you're using SQLite in-memory database (configured in TestCase)

### Issue: Encryption errors

**Solution:** Ensure APP_KEY is set in phpunit.xml

### Issue: Migration errors

**Solution:** Check that migrations are being loaded in TestCase::setUp()

## Next Steps

- Add more comprehensive tests
- Set up CI/CD pipeline
- Add integration tests with mock API responses
- Test error scenarios
- Add performance tests


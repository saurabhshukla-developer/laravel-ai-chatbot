# Testing Package Locally Before Publishing

## Step 1: Set Up Local Package Testing

### In Your Laravel Project's `composer.json`

Add the package as a path repository:

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "../chatbot"
        }
    ],
    "require": {
        "saurabhshukla-developer/laravel-ai-chatbot": "*"
    }
}
```

**Or if package is in a different location:**

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "/Applications/MAMP/htdocs/ai/chatbot"
        }
    ]
}
```

### Update Composer

```bash
cd /path/to/your/laravel-project
composer update saurabhshukla-developer/laravel-ai-chatbot
```

This will create a symlink to your local package.

## Step 2: Run Migrations

```bash
php artisan migrate
```

This will create the new `chatbot_tools` and `chatbot_agent_tools` tables.

## Step 3: Clear All Caches

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan optimize:clear
```

## Step 4: Verify Installation

### Check Commands

```bash
php artisan list | grep chatbot
```

Should show:
- `chatbot:list-tools`
- `chatbot:make-tool`
- `chatbot:test-tool`

### Check Routes

```bash
php artisan route:list | grep chatbot.tools
```

Should show all tool routes including `chatbot.tools.folder-info`.

## Step 5: Test File-Based Tools

### Create Tools Directory

```bash
mkdir -p app/Tools
```

### Create a Test Tool

```bash
php artisan chatbot:make-tool TestCalculator
```

This creates: `app/Tools/TestCalculatorTool.php`

### Edit the Tool

Open `app/Tools/TestCalculatorTool.php` and update:

```php
public function parameters(): array
{
    return [
        'properties' => [
            'expression' => [
                'type' => 'string',
                'description' => 'Mathematical expression (e.g., "2 + 2")',
            ],
        ],
        'required' => ['expression'],
    ];
}

public function execute(array $arguments): mixed
{
    $this->validateArguments($arguments, ['expression']);
    $expression = preg_replace('/[^0-9+\-*/().\s]/', '', $arguments['expression']);
    $result = eval("return {$expression};");
    return ['result' => $result, 'expression' => $expression];
}
```

### Test the Tool

```bash
# List tools
php artisan chatbot:list-tools

# Test tool
php artisan chatbot:test-tool test-calculator --args='{"expression":"10*5"}'
```

## Step 6: Test Web Interface

### 1. Access Tools Page

Visit: `http://localhost:8000/chatbot/tools`

Should show:
- File-Based Tools section (with your TestCalculator)
- Database Tools section
- "Tools Folder Info" button

### 2. Test Folder Info Page

Click "Tools Folder Info" or visit:
`http://localhost:8000/chatbot/tools/folder-info`

Should show:
- Folder path
- Status (exists/not found)
- List of files
- Quick start guide

### 3. Create Database Tool (Optional)

1. Go to `/chatbot/tools`
2. Click "Create Database Tool"
3. Fill form and save
4. Verify it appears in Database Tools section

## Step 7: Test Tool Assignment

### Assign Tool to Agent

1. Go to `/chatbot/agents`
2. Create new agent or edit existing
3. In "Tools" section, check "TestCalculator" (file-based)
4. Save

### Verify Assignment

```php
use LaravelAI\Chatbot\Facades\Chatbot;

$agent = Chatbot::getAgent('your-agent-slug');
$tools = $agent->getFormattedTools();
dd($tools); // Should show your tool
```

## Step 8: Test Tool Execution

### Enable Logging

Add to `.env`:
```env
CHATBOT_LOG_TOOL_USAGE=true
```

### Test Chat with Tool

```php
use LaravelAI\Chatbot\Facades\Chatbot;

$agent = Chatbot::getAgent('your-agent-slug');
$response = Chatbot::chat($agent, 'Calculate 25 * 4');

// Check response
dd([
    'content' => $response['content'],
    'executed_tools' => $response['executed_tools'] ?? [],
    'tool_calls' => $response['tool_calls'] ?? [],
]);
```

### Check Logs

```bash
tail -f storage/logs/laravel.log | grep "Tool"
```

Should see tool execution logs.

## Step 9: Test All Features

### ✅ Checklist

- [ ] Artisan commands work (`list-tools`, `make-tool`, `test-tool`)
- [ ] Tools page loads (`/chatbot/tools`)
- [ ] Folder info page works (`/chatbot/tools/folder-info`)
- [ ] Can create file-based tools
- [ ] Can create database tools
- [ ] Tools appear in list
- [ ] Can assign tools to agents
- [ ] Tools execute when chatting
- [ ] Response includes `executed_tools`
- [ ] Logging works (if enabled)
- [ ] No errors in Laravel logs

## Step 10: Test Edge Cases

### Test Empty Tools Folder

```bash
# Temporarily rename tools folder
mv app/Tools app/Tools_backup
```

Visit `/chatbot/tools` - should show helpful message.

### Test Invalid Tool

Create a tool file with syntax error - should be skipped gracefully.

### Test Tool Without Required Parameters

Test tool execution with missing parameters - should handle error.

## Common Local Testing Issues

### Package Not Found

```bash
# In your Laravel project
composer dump-autoload
php artisan config:clear
```

### Commands Not Available

```bash
php artisan cache:clear
php artisan config:clear
composer dump-autoload
```

### Tools Not Discovered

```bash
# Clear cache
php artisan cache:clear

# Verify folder exists
ls -la app/Tools

# Check tool files
php artisan chatbot:list-tools
```

### Routes Not Working

```bash
php artisan route:clear
php artisan route:list | grep chatbot
```

## Quick Test Script

Create `test-local.php` in your Laravel project root:

```php
<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Local Package Testing ===\n\n";

// 1. Check package loaded
echo "1. Package Check:\n";
try {
    $manager = app('chatbot');
    echo "   ✅ ChatbotManager loaded\n";
} catch (\Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}

// 2. Check tools
echo "\n2. Tools Discovery:\n";
try {
    $tools = \LaravelAI\Chatbot\Tools\ToolLoader::discover();
    echo "   Found: " . count($tools) . " tools\n";
    foreach ($tools as $tool) {
        echo "   - {$tool->name()} ({$tool->slug()})\n";
    }
} catch (\Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}

// 3. Check routes
echo "\n3. Routes Check:\n";
try {
    $routes = \Illuminate\Support\Facades\Route::getRoutes();
    $chatbotRoutes = collect($routes)->filter(function($route) {
        return str_contains($route->getName() ?? '', 'chatbot');
    });
    echo "   Found: " . $chatbotRoutes->count() . " chatbot routes\n";
} catch (\Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}

echo "\n=== Test Complete ===\n";
```

Run: `php test-local.php`

## Before Pushing Checklist

- [ ] All tests pass locally
- [ ] No errors in logs
- [ ] All features work as expected
- [ ] Documentation is updated
- [ ] CHANGELOG.md is updated
- [ ] composer.json is correct
- [ ] No temporary files committed
- [ ] .env.example exists
- [ ] All migrations work

## Ready to Push?

Once everything works locally:

1. Commit changes in package directory
2. Create tag: `git tag -a v1.1.0 -m "..."`  
3. Push: `git push origin v1.1.0`
4. Create GitHub release
5. Update Packagist

---

**Test everything locally first, then push!** 🚀


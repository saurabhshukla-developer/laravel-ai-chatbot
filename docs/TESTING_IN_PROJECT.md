# Testing the Updated Package in Your Project

## Step 1: Update the Package

### If Using Composer (Git Repository)

```bash
# Navigate to your Laravel project
cd /path/to/your/laravel-project

# Update the package
composer update saurabhshukla-developer/laravel-ai-chatbot

# Run migrations (if new migrations exist)
php artisan migrate
```

### If Using Local Development

If you're developing locally and using path repository:

```bash
# In your Laravel project's composer.json, make sure you have:
# "repositories": [{"type": "path", "url": "../chatbot"}]

# Then update
composer update saurabhshukla-developer/laravel-ai-chatbot
php artisan migrate
```

## Step 2: Clear Caches

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## Step 3: Verify Installation

### Check Artisan Commands

```bash
php artisan list | grep chatbot
```

You should see:
- `chatbot:list-tools`
- `chatbot:make-tool`
- `chatbot:test-tool`

### Check Routes

```bash
php artisan route:list | grep chatbot
```

You should see:
- `chatbot.tools.index`
- `chatbot.tools.folder-info`
- etc.

## Step 4: Test File-Based Tools

### Create a Test Tool

```bash
php artisan chatbot:make-tool TestCalculator
```

This should create: `app/Tools/TestCalculatorTool.php`

### List Tools

```bash
php artisan chatbot:list-tools
```

Should show your new tool.

### Test Tool Execution

```bash
php artisan chatbot:test-tool test-calculator --args='{"param1":"test"}'
```

### Edit the Tool

Open `app/Tools/TestCalculatorTool.php` and customize:

```php
public function parameters(): array
{
    return [
        'properties' => [
            'expression' => [
                'type' => 'string',
                'description' => 'Math expression',
            ],
        ],
        'required' => ['expression'],
    ];
}

public function execute(array $arguments): mixed
{
    $this->validateArguments($arguments, ['expression']);
    $result = eval("return {$arguments['expression']};");
    return ['result' => $result];
}
```

Test again:
```bash
php artisan chatbot:test-tool test-calculator --args='{"expression":"2+2"}'
```

## Step 5: Test Web Interface

### 1. Access Tools Page

Visit: `http://localhost:8000/chatbot/tools`

You should see:
- File-Based Tools section (if any exist)
- Database Tools section
- "Tools Folder Info" button

### 2. Test Tools Folder Info

Click "Tools Folder Info" button or visit:
`http://localhost:8000/chatbot/tools/folder-info`

Should show:
- Folder path information
- List of tool files (if any)
- Quick start instructions

### 3. Create Database Tool (Optional)

1. Go to `/chatbot/tools`
2. Click "Create Database Tool"
3. Fill in the form
4. Save

### 4. Assign Tools to Agent

1. Go to `/chatbot/agents`
2. Create or edit an agent
3. Check file-based tools and/or database tools
4. Save

### 5. Test Agent with Tools

1. Go to agent detail page
2. Use the chat interface
3. Ask something that requires a tool (e.g., "Calculate 10 * 5")
4. Check the response

## Step 6: Verify Tool Execution

### Enable Logging

Add to your `.env`:
```env
CHATBOT_LOG_TOOL_USAGE=true
```

### Test Chat with Tool

```php
use LaravelAI\Chatbot\Facades\Chatbot;

$agent = Chatbot::getAgent('your-agent-slug');
$response = Chatbot::chat($agent, 'Calculate 25 * 4');

// Check response
dd($response);
// Should show: content, executed_tools, tool_calls
```

### Check Logs

```bash
tail -f storage/logs/laravel.log | grep "Tool"
```

Should see:
- `🔍 Tool Calls Detected`
- `🔧 Tool Execution Started`
- `✅ Tool Execution Completed`

## Step 7: Complete Test Checklist

- [ ] Package updated successfully
- [ ] Migrations run without errors
- [ ] Artisan commands available (`chatbot:list-tools`, etc.)
- [ ] Can create tool with `chatbot:make-tool`
- [ ] Tool appears in `chatbot:list-tools`
- [ ] Can test tool with `chatbot:test-tool`
- [ ] Tools page loads (`/chatbot/tools`)
- [ ] Tools folder info page works (`/chatbot/tools/folder-info`)
- [ ] Can assign tools to agents
- [ ] Agent can use tools in chat
- [ ] Response includes `executed_tools` array
- [ ] Logging works (if enabled)

## Troubleshooting

### Commands Not Found

```bash
# Clear caches
php artisan cache:clear
php artisan config:clear

# Verify package is loaded
composer show saurabhshukla-developer/laravel-ai-chatbot
```

### Tools Not Appearing

```bash
# Clear cache
php artisan cache:clear

# Check if tools folder exists
ls -la app/Tools

# List tools
php artisan chatbot:list-tools
```

### 500 Error on Tools Page

```bash
# Check Laravel logs
tail -f storage/logs/laravel.log

# Clear all caches
php artisan optimize:clear
```

### Tool Not Executing

1. Verify tool is assigned to agent
2. Check tool slug matches
3. Enable logging: `CHATBOT_LOG_TOOL_USAGE=true`
4. Check logs for errors

## Quick Test Script

Create `test-tools.php` in your project root:

```php
<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use LaravelAI\Chatbot\Tools\ToolLoader;

echo "=== Testing Tools ===\n\n";

// 1. List tools
echo "1. Listing tools:\n";
$tools = ToolLoader::discover();
echo "Found: " . count($tools) . " tools\n";
foreach ($tools as $tool) {
    echo "  - {$tool->name()} ({$tool->slug()})\n";
}
echo "\n";

// 2. Test tool execution (if calculator exists)
$calculator = ToolLoader::getBySlug('calculator');
if ($calculator) {
    echo "2. Testing calculator tool:\n";
    try {
        $result = ToolLoader::execute('calculator', ['expression' => '2+2']);
        echo "Result: " . json_encode($result) . "\n";
    } catch (\Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
} else {
    echo "2. Calculator tool not found\n";
}

echo "\n=== Test Complete ===\n";
```

Run it:
```bash
php test-tools.php
```

## Summary

1. **Update package**: `composer update`
2. **Run migrations**: `php artisan migrate`
3. **Clear caches**: `php artisan optimize:clear`
4. **Test commands**: `php artisan chatbot:list-tools`
5. **Create tool**: `php artisan chatbot:make-tool TestTool`
6. **Test in UI**: Visit `/chatbot/tools`
7. **Assign to agent**: Edit agent and select tools
8. **Test chat**: Chat with agent and verify tools work

That's it! Your package is ready to test. 🚀


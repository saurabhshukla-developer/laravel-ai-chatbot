# Troubleshooting: Empty Response with Tools

## Issue: Getting Empty Response When Using Calculator Tool

If you're getting an empty response when using tools with OpenAI, here's how to fix it:

## ✅ Solution

The package now automatically handles tool execution! When the AI calls a tool:
1. The tool is executed automatically
2. The result is sent back to OpenAI
3. OpenAI processes the result and returns the final answer

## 🔍 Debugging Steps

### 1. Verify Tool is Assigned to Agent

Check that your tool is actually assigned to the agent:

```php
$agent = Chatbot::getAgent('your-agent-slug');
$tools = $agent->getFormattedTools();
dd($tools); // Should show your calculator tool
```

Or check in the database:
```php
$agent = Chatbot::getAgent('your-agent-slug');
$fileTools = $agent->config['file_tools'] ?? [];
dd($fileTools); // Should contain ['calculator']
```

### 2. Check Tool Definition

Verify your tool's slug matches:

```php
use LaravelAI\Chatbot\Tools\ToolLoader;

$tool = ToolLoader::getBySlug('calculator');
dd($tool->getDefinition()); // Check the definition
```

The slug should match exactly (case-sensitive).

### 3. Test Tool Execution Directly

Test if your tool executes correctly:

```php
use LaravelAI\Chatbot\Tools\ToolLoader;

try {
    $result = ToolLoader::execute('calculator', [
        'expression' => '2 + 2'
    ]);
    dd($result); // Should return ['result' => 4]
} catch (\Exception $e) {
    dd($e->getMessage()); // Check for errors
}
```

### 4. Enable Debug Logging

Add logging to see what's happening:

```php
use Illuminate\Support\Facades\Log;

// In your tool's execute method
public function execute(array $arguments): mixed
{
    Log::info('Calculator tool called', ['arguments' => $arguments]);
    
    $result = eval("return {$arguments['expression']};");
    
    Log::info('Calculator tool result', ['result' => $result]);
    
    return ['result' => $result];
}
```

Then check `storage/logs/laravel.log` for the logs.

### 5. Check OpenAI Response

Add temporary logging to see the OpenAI response:

```php
// In BaseProvider.php chat method (temporary)
$responseData = $response->json();
\Log::info('OpenAI Response', ['response' => $responseData]);
```

## 🐛 Common Issues

### Issue 1: Tool Not Found

**Error:** `Tool not found: calculator`

**Solution:**
- Check the tool slug matches exactly
- Verify the tool file is in `app/Tools/`
- Clear cache: `php artisan cache:clear`
- Check namespace matches directory structure

### Issue 2: Tool Executes But No Response

**Symptom:** Tool runs but AI doesn't respond

**Solution:**
- Make sure tool returns a proper value (not null)
- Return structured data: `['result' => $value]` not just `$value`
- Check OpenAI API response in logs

### Issue 3: Wrong Tool Name

**Symptom:** Tool not being called

**Solution:**
- Check the tool slug in `getDefinition()` matches what OpenAI sees
- The slug is auto-generated from `name()` method
- Example: `name()` returns "Calculator" → slug is "calculator"

### Issue 4: Invalid Arguments

**Error:** `Missing required parameter: expression`

**Solution:**
- Check your tool's `parameters()` method defines required fields
- Verify OpenAI is sending arguments correctly
- Add validation in `execute()` method

## 📝 Example: Working Calculator Tool

Here's a complete working example:

```php
<?php

namespace App\Tools;

use LaravelAI\Chatbot\Tools\BaseTool;

class CalculatorTool extends BaseTool
{
    public function name(): string
    {
        return 'Calculator';
    }

    public function description(): string
    {
        return 'Performs basic mathematical calculations';
    }

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
        
        try {
            $result = eval("return {$expression};");
            
            // IMPORTANT: Return structured data
            return [
                'success' => true,
                'result' => $result,
                'expression' => $expression,
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'error' => 'Invalid expression: ' . $e->getMessage(),
            ];
        }
    }
}
```

## 🔧 Quick Fix Checklist

- [ ] Tool file exists in `app/Tools/`
- [ ] Tool extends `BaseTool`
- [ ] Tool is assigned to agent (check in UI or database)
- [ ] Tool slug matches (check `slug()` method)
- [ ] Tool `execute()` returns a value (not null)
- [ ] Cache cleared: `php artisan cache:clear`
- [ ] Check Laravel logs for errors
- [ ] Test tool execution directly

## 🆘 Still Not Working?

1. **Check Laravel Logs:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

2. **Enable Debug Mode:**
   ```env
   APP_DEBUG=true
   ```

3. **Test with Simple Tool:**
   Create a minimal test tool that just returns a string:
   ```php
   public function execute(array $arguments): mixed
   {
       return ['message' => 'Tool executed successfully'];
   }
   ```

4. **Check OpenAI API Response:**
   Add logging to see what OpenAI returns when it calls the tool.

## 📚 Related Documentation

- [TOOLS_README.md](TOOLS_README.md) - Complete tool guide
- [SETUP_FILE_TOOLS.md](SETUP_FILE_TOOLS.md) - Setup instructions
- [FILE_BASED_TOOLS.md](FILE_BASED_TOOLS.md) - Feature overview

---

**Need more help?** Check the logs and verify each step above. The most common issue is the tool not being assigned to the agent or the tool slug not matching.


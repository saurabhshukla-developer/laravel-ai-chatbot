# Fixing Tool Execution Errors

## Issue: Tool Executes But Returns Error

If you see:
- `executed_tools` shows the tool was called ✅
- But AI says "I'm unable to provide..." ❌

This means the tool executed but returned invalid data or threw an error.

## Common Issues & Fixes

### Issue 1: Undefined Variable

**Error in your code:**
```php
public function execute(array $arguments): mixed
{
    return $response->json([...]); // ❌ $response is not defined!
}
```

**Fix:**
```php
public function execute(array $arguments): mixed
{
    // Return data directly
    return [
        'success' => true,
        'location' => $arguments['location'],
        'weather' => 'sunny',
    ];
}
```

### Issue 2: Tool Returns Null or Empty

**Problem:** Tool doesn't return a value

**Fix:** Always return structured data:
```php
public function execute(array $arguments): mixed
{
    // ✅ Good - returns array
    return [
        'success' => true,
        'data' => [...],
    ];
    
    // ❌ Bad - returns null or nothing
    // return null;
}
```

### Issue 3: Tool Throws Exception

**Problem:** Exception is caught but error message confuses AI

**Fix:** Return error in structured format:
```php
public function execute(array $arguments): mixed
{
    try {
        // Your logic here
        return ['success' => true, 'data' => $result];
    } catch (\Exception $e) {
        // Return error in a way AI can understand
        return [
            'success' => false,
            'error' => $e->getMessage(),
            'message' => 'Unable to fetch weather data at this time',
        ];
    }
}
```

### Issue 4: Invalid JSON Response

**Problem:** Tool returns data that can't be JSON encoded

**Fix:** Ensure all data is JSON-serializable:
```php
public function execute(array $arguments): mixed
{
    // ✅ Good - simple types
    return [
        'temperature' => 22,
        'condition' => 'sunny',
    ];
    
    // ❌ Bad - complex objects
    // return $someObject; // May not serialize properly
}
```

## Fixed Weather Tool Example

Here's a corrected version:

```php
<?php

namespace App\Tools;

use LaravelAI\Chatbot\Tools\BaseTool;

class WeatherTool extends BaseTool
{
    public function name(): string
    {
        return 'Get Weather';
    }

    public function description(): string
    {
        return 'Gets the current weather conditions for a specific location.';
    }

    public function parameters(): array
    {
        return [
            'properties' => [
                'location' => [
                    'type' => 'string',
                    'description' => 'City and country (e.g., "London, UK")',
                ],
            ],
            'required' => ['location'],
        ];
    }

    public function execute(array $arguments): mixed
    {
        $this->validateArguments($arguments, ['location']);

        $location = $arguments['location'];

        // Mock response (replace with real API call)
        return [
            'success' => true,
            'location' => $location,
            'temperature' => '22°C',
            'condition' => 'Partly Cloudy',
            'humidity' => '65%',
            'wind' => '10 km/h',
            'description' => "Current weather in {$location}: Partly Cloudy, 22°C",
        ];
    }
}
```

## Debugging Steps

### 1. Check Tool Execution Directly

```php
use LaravelAI\Chatbot\Tools\ToolLoader;

try {
    $result = ToolLoader::execute('get-weather', [
        'location' => 'London, UK'
    ]);
    
    dd($result); // Check what's returned
} catch (\Exception $e) {
    dd($e->getMessage()); // Check for errors
}
```

### 2. Enable Logging

Add logging to your tool:

```php
public function execute(array $arguments): mixed
{
    \Log::info('Weather tool called', ['arguments' => $arguments]);
    
    try {
        $result = [
            'success' => true,
            'location' => $arguments['location'],
            'weather' => 'sunny',
        ];
        
        \Log::info('Weather tool result', ['result' => $result]);
        return $result;
    } catch (\Exception $e) {
        \Log::error('Weather tool error', ['error' => $e->getMessage()]);
        throw $e;
    }
}
```

### 3. Check Logs

```bash
tail -f storage/logs/laravel.log | grep "Weather"
```

### 4. Test Response Format

Make sure your tool returns data OpenAI can understand:

```php
// ✅ Good format
return [
    'success' => true,
    'location' => 'London',
    'temperature' => '22°C',
    'condition' => 'Sunny',
    'description' => 'Current weather in London: Sunny, 22°C',
];

// ❌ Bad format - too complex or missing description
return [
    'data' => [
        'nested' => [
            'complex' => 'structure'
        ]
    ]
];
```

## Best Practices

1. **Always return structured data:**
   ```php
   return ['success' => true, 'data' => ...];
   ```

2. **Include a description field:**
   ```php
   return [
       'description' => 'Human-readable summary',
       // ... other data
   ];
   ```

3. **Handle errors gracefully:**
   ```php
   try {
       // Tool logic
   } catch (\Exception $e) {
       return [
           'success' => false,
           'error' => $e->getMessage(),
       ];
   }
   ```

4. **Validate inputs:**
   ```php
   $this->validateArguments($arguments, ['required_param']);
   ```

5. **Log important events:**
   ```php
   \Log::info('Tool executed', ['args' => $arguments]);
   ```

## Quick Fix Checklist

- [ ] Tool returns a value (not null)
- [ ] Return value is an array or string
- [ ] No undefined variables
- [ ] Exceptions are caught and returned as errors
- [ ] Data is JSON-serializable
- [ ] Includes a description field for AI to understand
- [ ] Test tool execution directly
- [ ] Check logs for errors

---

**Your specific issue:** The `$response` variable was undefined. Use the fixed version above!


# Creating File-Based Tools

This guide shows you how to create tools using simple PHP files. It's the easiest and most professional way to add functionality to your AI agents.

## Quick Start

1. **Create a tools directory** (if it doesn't exist):
   ```bash
   mkdir -p app/Tools
   ```

2. **Create a tool file** - Create a PHP file in `app/Tools/` that extends `BaseTool`:

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
           
           $expression = $arguments['expression'];
           // Sanitize and evaluate
           $result = eval("return {$expression};");
           
           return [
               'result' => $result,
               'expression' => $expression,
           ];
       }
   }
   ```

3. **That's it!** The tool is automatically discovered and available for your agents.

## How It Works

- Tools are **auto-discovered** from the `app/Tools` directory (configurable via `CHATBOT_TOOLS_PATH`)
- Simply create a PHP class that extends `LaravelAI\Chatbot\Tools\BaseTool`
- The tool will appear in the tools list and can be assigned to agents
- No database setup or manual registration needed!

## BaseTool Methods

### Required Methods

1. **`name(): string`** - The display name of the tool
2. **`description(): string`** - What the tool does (shown to AI)
3. **`parameters(): array`** - JSON Schema for tool parameters
4. **`execute(array $arguments): mixed`** - Execute the tool logic

### Optional Methods

- **`slug(): string`** - Auto-generated from name, override if needed

### Helper Methods

- **`validateArguments(array $arguments, array $required): void`** - Validate required parameters

## Examples

### Example 1: Simple Calculator

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
                    'description' => 'Mathematical expression to evaluate',
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
        
        return ['result' => $result];
    }
}
```

### Example 2: Database Query Tool

```php
<?php

namespace App\Tools;

use LaravelAI\Chatbot\Tools\BaseTool;
use App\Models\User;

class SearchUsersTool extends BaseTool
{
    public function name(): string
    {
        return 'Search Users';
    }

    public function description(): string
    {
        return 'Searches for users in the database by name or email';
    }

    public function parameters(): array
    {
        return [
            'properties' => [
                'query' => [
                    'type' => 'string',
                    'description' => 'Search term (name or email)',
                ],
                'limit' => [
                    'type' => 'integer',
                    'description' => 'Maximum number of results',
                    'default' => 10,
                ],
            ],
            'required' => ['query'],
        ];
    }

    public function execute(array $arguments): mixed
    {
        $this->validateArguments($arguments, ['query']);
        
        $query = $arguments['query'];
        $limit = $arguments['limit'] ?? 10;
        
        $users = User::where('name', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->limit($limit)
            ->get(['id', 'name', 'email']);
        
        return [
            'count' => $users->count(),
            'users' => $users->toArray(),
        ];
    }
}
```

### Example 3: API Call Tool

```php
<?php

namespace App\Tools;

use LaravelAI\Chatbot\Tools\BaseTool;
use Illuminate\Support\Facades\Http;

class WeatherTool extends BaseTool
{
    public function name(): string
    {
        return 'Get Weather';
    }

    public function description(): string
    {
        return 'Gets current weather for a location';
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
        
        // Call weather API
        $response = Http::get('https://api.weather.com/v1/current', [
            'location' => $arguments['location'],
            'api_key' => config('services.weather.api_key'),
        ]);
        
        return $response->json();
    }
}
```

## Parameters Schema

The `parameters()` method should return a JSON Schema compatible array:

```php
[
    'properties' => [
        'param_name' => [
            'type' => 'string|integer|number|boolean|array|object',
            'description' => 'Parameter description',
            'enum' => ['option1', 'option2'], // Optional: for enums
            'default' => 'default_value',      // Optional: default value
        ],
    ],
    'required' => ['param_name'], // Array of required parameter names
]
```

## Assigning Tools to Agents

### Via Web UI

1. Go to `/chatbot/agents`
2. Create or edit an agent
3. Check the file-based tools you want to assign
4. Save

### Programmatically

```php
use LaravelAI\Chatbot\Facades\Chatbot;

$agent = Chatbot::getAgent('my-agent');

// File-based tools are assigned via config
$config = $agent->config ?? [];
$config['file_tools'] = ['calculator', 'weather']; // Use tool slugs
$agent->update(['config' => $config]);
```

## Configuration

Set the tools directory path in your `.env`:

```env
CHATBOT_TOOLS_PATH=app/Tools
```

Or in `config/chatbot.php`:

```php
'tools_path' => env('CHATBOT_TOOLS_PATH', 'app/Tools'),
```

## Best Practices

1. **Use descriptive names** - Help the AI understand when to use your tool
2. **Write clear descriptions** - The AI uses this to decide tool usage
3. **Validate inputs** - Always validate and sanitize arguments
4. **Handle errors gracefully** - Return meaningful error messages
5. **Use namespaces** - Organize tools in subdirectories with namespaces
6. **Document parameters** - Clear parameter descriptions help the AI

## File Structure

```
app/
└── Tools/
    ├── CalculatorTool.php
    ├── WeatherTool.php
    └── Database/
        └── SearchUsersTool.php
```

## Troubleshooting

**Tool not appearing?**
- Check the file extends `BaseTool`
- Verify the namespace matches the directory structure
- Check file permissions
- Clear Laravel cache: `php artisan cache:clear`

**Tool not executing?**
- Verify the `execute()` method returns a value
- Check error logs for exceptions
- Ensure parameters match the schema

## Next Steps

- See [TOOLS_EXAMPLES.md](TOOLS_EXAMPLES.md) for more examples
- Check the [README.md](README.md) for general package usage
- Explore example tools in `src/Tools/Examples/`


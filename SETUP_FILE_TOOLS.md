# Setting Up File-Based Tools in Your Laravel Project

This guide shows you how to use the new file-based tools feature in your Laravel project.

## Step 1: Update the Package

### If using Composer (Git Repository)

If you're using this package via Composer from a Git repository:

```bash
composer update saurabhshukla-developer/laravel-ai-chatbot
```

### If using Local Package Development

If you're developing the package locally and using it via `composer.json`:

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

Then update:
```bash
composer update saurabhshukla-developer/laravel-ai-chatbot
```

### If using Git Submodule or Direct Copy

If you've copied the package directly or using git submodule:

1. Pull the latest changes:
   ```bash
   cd path/to/chatbot/package
   git pull origin master
   ```

2. Run migrations (if new):
   ```bash
   php artisan migrate
   ```

## Step 2: Run Migrations

Make sure you have the latest migrations:

```bash
php artisan migrate
```

This will create the `chatbot_tools` and `chatbot_agent_tools` tables if they don't exist.

## Step 3: Create Tools Directory

Create the tools directory in your Laravel project:

```bash
mkdir -p app/Tools
```

Or manually create the folder: `app/Tools/`

## Step 4: Configure Tools Path (Optional)

By default, tools are loaded from `app/Tools`. To customize, add to your `.env`:

```env
CHATBOT_TOOLS_PATH=app/Tools
```

Or update `config/chatbot.php`:

```php
'tools_path' => env('CHATBOT_TOOLS_PATH', 'app/Tools'),
```

## Step 5: Create Your First Tool

Create a file `app/Tools/CalculatorTool.php`:

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
        return 'Performs basic mathematical calculations including addition, subtraction, multiplication, and division.';
    }

    public function parameters(): array
    {
        return [
            'properties' => [
                'expression' => [
                    'type' => 'string',
                    'description' => 'The mathematical expression to evaluate (e.g., "2 + 2", "10 * 5")',
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
            
            return [
                'success' => true,
                'result' => $result,
                'expression' => $expression,
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'error' => 'Invalid mathematical expression',
            ];
        }
    }
}
```

## Step 6: Verify Tool Discovery

1. Clear Laravel cache:
   ```bash
   php artisan cache:clear
   php artisan config:clear
   ```

2. Visit `/chatbot/tools` in your browser
   - You should see your `CalculatorTool` listed under "File-Based Tools"

## Step 7: Assign Tool to an Agent

### Via Web UI:

1. Go to `/chatbot/agents`
2. Create a new agent or edit an existing one
3. Scroll to the "Tools" section
4. Check the "Calculator" tool (it will show as "File-Based")
5. Save the agent

### Programmatically:

```php
use LaravelAI\Chatbot\Facades\Chatbot;

$agent = Chatbot::getAgent('my-agent-slug');

// Assign file-based tools
$config = $agent->config ?? [];
$config['file_tools'] = ['calculator']; // Use the tool slug
$agent->update(['config' => $config]);
```

## Step 8: Test the Tool

Chat with your agent:

```php
use LaravelAI\Chatbot\Facades\Chatbot;

$agent = Chatbot::getAgent('my-agent-slug');
$response = Chatbot::chat($agent, 'What is 25 * 4?');

echo $response['content'];
```

The AI will use the calculator tool to answer!

## Troubleshooting

### Tool Not Appearing?

1. **Check file location**: Must be in `app/Tools/` (or your configured path)
2. **Check namespace**: Must match directory structure
   - `app/Tools/MyTool.php` → `namespace App\Tools;`
3. **Check class name**: Must extend `BaseTool`
4. **Clear cache**:
   ```bash
   php artisan cache:clear
   php artisan config:clear
   ```

### Class Not Found Error?

Make sure you're using the correct namespace:

```php
namespace App\Tools;  // For app/Tools/ directory

use LaravelAI\Chatbot\Tools\BaseTool;
```

### Tool Not Executing?

1. Check the `execute()` method returns a value
2. Verify parameters match the schema
3. Check Laravel logs: `storage/logs/laravel.log`

## Example: Database Query Tool

Here's a more advanced example:

```php
<?php

namespace App\Tools;

use LaravelAI\Chatbot\Tools\BaseTool;
use App\Models\User; // Your model

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

## Next Steps

- Create more tools in `app/Tools/`
- Organize tools in subdirectories (e.g., `app/Tools/Database/`, `app/Tools/API/`)
- See [TOOLS_README.md](TOOLS_README.md) for more examples
- Check [FILE_BASED_TOOLS.md](FILE_BASED_TOOLS.md) for best practices

## Quick Checklist

- [ ] Package updated to latest version
- [ ] Migrations run (`php artisan migrate`)
- [ ] `app/Tools/` directory created
- [ ] First tool file created
- [ ] Cache cleared (`php artisan cache:clear`)
- [ ] Tool visible in `/chatbot/tools`
- [ ] Tool assigned to an agent
- [ ] Tested with a chat message

---

**That's it!** You're ready to use file-based tools in your Laravel project. 🚀


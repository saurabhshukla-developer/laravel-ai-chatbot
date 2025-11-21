# Quick Start: File-Based Tools

## 🚀 3-Step Setup

### 1. Update Package
```bash
composer update saurabhshukla-developer/laravel-ai-chatbot
php artisan migrate
```

### 2. Create Tools Directory
```bash
mkdir -p app/Tools
```

### 3. Create Your First Tool

Create `app/Tools/CalculatorTool.php`:

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
        return 'Performs mathematical calculations';
    }

    public function parameters(): array
    {
        return [
            'properties' => [
                'expression' => [
                    'type' => 'string',
                    'description' => 'Math expression (e.g., "2 + 2")',
                ],
            ],
            'required' => ['expression'],
        ];
    }

    public function execute(array $arguments): mixed
    {
        $result = eval("return {$arguments['expression']};");
        return ['result' => $result];
    }
}
```

**Done!** Visit `/chatbot/tools` to see your tool.

## 📝 Assign to Agent

**Via UI:** `/chatbot/agents` → Edit agent → Check "Calculator" → Save

**Via Code:**
```php
$agent = Chatbot::getAgent('my-agent');
$config = $agent->config ?? [];
$config['file_tools'] = ['calculator'];
$agent->update(['config' => $config]);
```

## ✅ Verify

```bash
php artisan cache:clear
```

Visit `/chatbot/tools` - you should see "Calculator" under File-Based Tools!

---

**Need more?** See [SETUP_FILE_TOOLS.md](SETUP_FILE_TOOLS.md) for detailed instructions.


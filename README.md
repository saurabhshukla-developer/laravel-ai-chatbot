# Laravel AI Chatbot Package

[![Latest Version](https://img.shields.io/github/v/release/saurabhshukla-developer/laravel-ai-chatbot)](https://github.com/saurabhshukla-developer/laravel-ai-chatbot/releases)
[![License](https://img.shields.io/github/license/saurabhshukla-developer/laravel-ai-chatbot)](https://github.com/saurabhshukla-developer/laravel-ai-chatbot/blob/main/LICENSE)
[![PHP Version](https://img.shields.io/badge/php-%3E%3D8.1-blue.svg)](https://www.php.net/)
[![Laravel Version](https://img.shields.io/badge/laravel-10.x%20%7C%2011.x%20%7C%2012.x-red.svg)](https://laravel.com/)

A comprehensive Laravel package for building AI agents with support for multiple AI providers (OpenAI, Anthropic, Google AI). Manage API keys, create AI agents, and interact with them seamlessly in your Laravel application.

**Repository:** [https://github.com/saurabhshukla-developer/laravel-ai-chatbot](https://github.com/saurabhshukla-developer/laravel-ai-chatbot)

## Features

- API key management with encrypted storage
- AI agent builder with custom prompts
- **Tool management** - Create and assign function calling tools to agents
- Multiple provider support (OpenAI, Anthropic, Google AI)
- Built-in chat interface
- Web UI for managing keys, agents, and tools
- Programmatic API for code integration
- Streaming support for responses

## Installation

```bash
composer config repositories.laravel-ai-chatbot vcs https://github.com/saurabhshukla-developer/laravel-ai-chatbot
composer require saurabhshukla-developer/laravel-ai-chatbot:dev-master
```

Publish configuration and migrations:

```bash
php artisan vendor:publish --provider="LaravelAI\Chatbot\ChatbotServiceProvider" --tag="chatbot-config"
php artisan vendor:publish --provider="LaravelAI\Chatbot\ChatbotServiceProvider" --tag="chatbot-migrations"
php artisan migrate
```

For detailed setup instructions, see [SETUP.md](SETUP.md).

## Installation (Detailed)

### Step 1: Install via Composer

```bash
# Add repository first
composer config repositories.laravel-ai-chatbot vcs https://github.com/saurabhshukla-developer/laravel-ai-chatbot

# Then require dev-master version
composer require saurabhshukla-developer/laravel-ai-chatbot:dev-master
```

### Step 2: Publish Configuration and Migrations

```bash
php artisan vendor:publish --provider="LaravelAI\Chatbot\ChatbotServiceProvider" --tag="chatbot-config"
php artisan vendor:publish --provider="LaravelAI\Chatbot\ChatbotServiceProvider" --tag="chatbot-migrations"
```

### Step 3: Run Migrations

```bash
php artisan migrate
```

### Step 4: (Optional) Publish Views

If you want to customize the views:

```bash
php artisan vendor:publish --provider="LaravelAI\Chatbot\ChatbotServiceProvider" --tag="chatbot-views"
```

## Configuration

After publishing the configuration file, you can customize it at `config/chatbot.php`:

```php
return [
    'default_provider' => env('CHATBOT_DEFAULT_PROVIDER', 'openai'),
    
    'providers' => [
        'openai' => [
            'name' => 'OpenAI',
            'api_url' => env('OPENAI_API_URL', 'https://api.openai.com/v1'),
            'model' => env('OPENAI_MODEL', 'gpt-4'),
            // ...
        ],
        // ...
    ],
    
    'storage_driver' => env('CHATBOT_STORAGE_DRIVER', 'database'),
    
    'routes' => [
        'prefix' => env('CHATBOT_ROUTE_PREFIX', 'chatbot'),
        'middleware' => ['web'],
    ],
];
```

## Usage

### Managing API Keys

1. Navigate to `/chatbot/api-keys` in your browser
2. Click "Add API Key"
3. Select your provider and enter your API key
4. Optionally set it as the default for that provider

### Creating AI Agents

1. Navigate to `/chatbot/agents` in your browser
2. Click "Create Agent"
3. Fill in the agent details:
   - **Name**: A descriptive name for your agent
   - **Provider**: Choose OpenAI, Anthropic, or Google AI
   - **Model**: (Optional) Specific model to use
   - **System Prompt**: (Optional) Define the agent's behavior and personality
   - **Tools**: (Optional) Select tools that the agent can use
4. Save the agent

### Creating Tools

#### Method 1: File-Based Tools (Recommended - Easiest!)

Create PHP files in `app/Tools/` directory:

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
                    'description' => 'Mathematical expression (e.g., "2 + 2")',
                ],
            ],
            'required' => ['expression'],
        ];
    }

    public function execute(array $arguments): mixed
    {
        $expression = $arguments['expression'];
        $result = eval("return {$expression};");
        return ['result' => $result];
    }
}
```

**That's it!** The tool is automatically discovered and available for your agents. No database setup needed!

#### Method 2: Database Tools (Via Web UI)

1. Navigate to `/chatbot/tools` in your browser
2. Click "Create Tool"
3. Fill in the tool details and save

For detailed examples, see:
- **[TOOLS_README.md](TOOLS_README.md)** - File-based tools guide (Recommended)
- **[TOOLS_EXAMPLES.md](TOOLS_EXAMPLES.md)** - Database tools and advanced examples

### Using Agents in Code

#### Basic Chat

```php
use LaravelAI\Chatbot\Facades\Chatbot;

// Get an agent by slug or ID
$agent = Chatbot::getAgent('my-agent-slug');

// Chat with the agent
$response = Chatbot::chat($agent, 'Hello, how are you?');

echo $response['content'];
```

#### Using Specific Provider

```php
use LaravelAI\Chatbot\Facades\Chatbot;

// Use a specific provider
$provider = Chatbot::provider('openai');

// Create an agent programmatically
$agent = Chatbot::createAgent([
    'name' => 'Customer Support Bot',
    'slug' => 'customer-support',
    'provider' => 'openai',
    'system_prompt' => 'You are a helpful customer support assistant.',
    'is_active' => true,
]);

// Chat with the agent
$response = Chatbot::chat($agent, 'I need help with my order');
```

#### Streaming Responses

```php
use LaravelAI\Chatbot\Facades\Chatbot;

$agent = Chatbot::getAgent('my-agent');

foreach (Chatbot::streamChat($agent, 'Tell me a story') as $chunk) {
    if (!$chunk['done']) {
        echo $chunk['content'];
        flush();
    }
}
```

#### Advanced Options

```php
$response = Chatbot::chat($agent, 'Your message', [
    'temperature' => 0.9,
    'max_tokens' => 1000,
    'model' => 'gpt-4-turbo', // Override agent's default model
]);
```

#### Using Agents with Tools

```php
use LaravelAI\Chatbot\Facades\Chatbot;
use LaravelAI\Chatbot\Models\Tool;

// Get an agent
$agent = Chatbot::getAgent('math-assistant');

// Tools are automatically included when chatting
$response = Chatbot::chat($agent, 'What is 25 * 4?');

// The AI can use assigned tools to answer the question
echo $response['content'];

// To assign tools programmatically:
$calculatorTool = Tool::where('slug', 'calculator')->first();
$agent->tools()->attach($calculatorTool->id);
```

For more tool examples, see [TOOLS_EXAMPLES.md](TOOLS_EXAMPLES.md)

### Direct Provider Access

```php
use LaravelAI\Chatbot\Facades\Chatbot;

$provider = Chatbot::provider('openai');
$response = $provider->chat($agent, 'Hello');
```

## Routes

The package automatically registers the following routes (with `/chatbot` prefix by default):

- `GET /chatbot/api-keys` - List all API keys
- `GET /chatbot/api-keys/create` - Create new API key form
- `POST /chatbot/api-keys` - Store new API key
- `GET /chatbot/api-keys/{id}/edit` - Edit API key form
- `PUT /chatbot/api-keys/{id}` - Update API key
- `DELETE /chatbot/api-keys/{id}` - Delete API key
- `GET /chatbot/agents` - List all agents
- `GET /chatbot/agents/create` - Create new agent form
- `POST /chatbot/agents` - Store new agent
- `GET /chatbot/agents/{id}` - View agent details and chat
- `GET /chatbot/agents/{id}/edit` - Edit agent form
- `PUT /chatbot/agents/{id}` - Update agent
- `DELETE /chatbot/agents/{id}` - Delete agent
- `POST /chatbot/agents/{id}/chat` - Chat with agent (API endpoint)
- `GET /chatbot/tools` - List all tools
- `GET /chatbot/tools/create` - Create new tool form
- `POST /chatbot/tools` - Store new tool
- `GET /chatbot/tools/{id}` - View tool details
- `GET /chatbot/tools/{id}/edit` - Edit tool form
- `PUT /chatbot/tools/{id}` - Update tool
- `DELETE /chatbot/tools/{id}` - Delete tool

## Environment Variables

You can set these in your `.env` file:

```env
CHATBOT_DEFAULT_PROVIDER=openai
CHATBOT_STORAGE_DRIVER=database
CHATBOT_ROUTE_PREFIX=chatbot

# Provider-specific (optional if using database storage)
OPENAI_API_KEY=your-openai-key
ANTHROPIC_API_KEY=your-anthropic-key
GOOGLE_AI_API_KEY=your-google-key
```

## Security

- API keys stored in the database are encrypted using Laravel's encryption
- Make sure your `APP_KEY` is set in your `.env` file
- API keys are never displayed in plain text in the UI
- Consider adding authentication middleware to protect the routes

## Supported Providers

### OpenAI
- Models: gpt-4, gpt-4-turbo, gpt-3.5-turbo, etc.
- API Documentation: https://platform.openai.com/docs

### Anthropic (Claude)
- Models: claude-3-opus, claude-3-sonnet, claude-3-haiku, etc.
- API Documentation: https://docs.anthropic.com

### Google AI
- Models: gemini-pro, gemini-pro-vision, etc.
- API Documentation: https://ai.google.dev/docs

## Customization

### Custom Provider

To add a custom provider, create a new provider class:

```php
namespace App\Providers;

use LaravelAI\Chatbot\Providers\BaseProvider;
use LaravelAI\Chatbot\Models\AiAgent;

class CustomProvider extends BaseProvider
{
    protected function getProviderName(): string
    {
        return 'custom';
    }

    // Implement required methods...
}
```

Then register it in the `ChatbotManager` class.

### Custom Views

Publish the views and customize them:

```bash
php artisan vendor:publish --tag="chatbot-views"
```

Views will be published to `resources/views/vendor/chatbot/`.

## Requirements

- PHP 8.1 or higher
- Laravel 10.x, 11.x, or 12.x
- Guzzle HTTP Client

## License

MIT License

## Contributing

Contributions are welcome. Please see [CONTRIBUTING.md](CONTRIBUTING.md) for guidelines.

## Support

- **Issues**: [GitHub Issues](https://github.com/saurabhshukla-developer/laravel-ai-chatbot/issues)
- **Security**: See [SECURITY.md](.github/SECURITY.md) for reporting security vulnerabilities
- **Changelog**: See [CHANGELOG.md](CHANGELOG.md) for version history

## Author

**Saurabh Shukla**

- GitHub: [@saurabhshukla-developer](https://github.com/saurabhshukla-developer)
- Repository: [laravel-ai-chatbot](https://github.com/saurabhshukla-developer/laravel-ai-chatbot)

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.


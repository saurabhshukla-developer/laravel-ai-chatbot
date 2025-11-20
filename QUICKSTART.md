# Quick Start Guide

Get up and running with Laravel AI Chatbot in 5 minutes!

## Prerequisites

- Laravel 10.x or 11.x installed
- PHP 8.1 or higher
- Composer installed
- Database configured

## Installation (5 Steps)

### 1. Install the Package

```bash
composer require saurabhshukla-developer/laravel-ai-chatbot
```

Or if installing from GitHub directly:

```bash
# In your Laravel project's composer.json, add:
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/saurabhshukla-developer/laravel-ai-chatbot"
        }
    ],
    "require": {
        "saurabhshukla-developer/laravel-ai-chatbot": "*"
    }
}

# Then run:
composer require saurabhshukla-developer/laravel-ai-chatbot
```

### 2. Publish Configuration

```bash
php artisan vendor:publish --provider="LaravelAI\Chatbot\ChatbotServiceProvider" --tag="chatbot-config"
php artisan vendor:publish --provider="LaravelAI\Chatbot\ChatbotServiceProvider" --tag="chatbot-migrations"
```

### 3. Set APP_KEY (if not already set)

```bash
php artisan key:generate
```

### 4. Run Migrations

```bash
php artisan migrate
```

### 5. Start Your Server

```bash
php artisan serve
```

## First Use

### Option 1: Web Interface (Easiest)

1. **Open your browser:** `http://localhost:8000/chatbot/api-keys`

2. **Add an API Key:**
   - Click "Add API Key"
   - Select provider (e.g., "OpenAI")
   - Enter your API key
   - Check "Set as default for this provider"
   - Click "Create API Key"

3. **Create an Agent:**
   - Go to `http://localhost:8000/chatbot/agents`
   - Click "Create Agent"
   - Fill in:
     - Name: "My First Bot"
     - Provider: Select your provider
     - System Prompt: "You are a helpful assistant."
   - Click "Create Agent"

4. **Test It:**
   - Click on your agent
   - Type a message in the chat box
   - See the response!

### Option 2: Code (Programmatic)

```php
use LaravelAI\Chatbot\Facades\Chatbot;

// Create an agent
$agent = Chatbot::createAgent([
    'name' => 'My Bot',
    'slug' => 'my-bot',
    'provider' => 'openai',
    'system_prompt' => 'You are helpful.',
    'is_active' => true,
]);

// Chat with it
$response = Chatbot::chat($agent, 'Hello!');
echo $response['content'];
```

## Common Tasks

### Add API Key via Code

```php
use LaravelAI\Chatbot\Models\ApiKey;

ApiKey::create([
    'provider' => 'openai',
    'name' => 'My OpenAI Key',
    'api_key' => 'sk-...',
    'is_default' => true,
    'is_active' => true,
]);
```

### Use in a Controller

```php
// app/Http/Controllers/ChatController.php
use LaravelAI\Chatbot\Facades\Chatbot;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function chat(Request $request)
    {
        $agent = Chatbot::getAgent('my-bot');
        $response = Chatbot::chat($agent, $request->input('message'));
        
        return response()->json($response);
    }
}
```

### Add to Routes

```php
// routes/web.php or routes/api.php
Route::post('/chat', [ChatController::class, 'chat']);
```

## Environment Variables

Add to your `.env`:

```env
CHATBOT_DEFAULT_PROVIDER=openai
CHATBOT_STORAGE_DRIVER=database

# Optional: Set API keys here if not using database storage
OPENAI_API_KEY=sk-...
```

## Troubleshooting

**Routes not working?**
```bash
php artisan route:clear
php artisan config:clear
```

**API keys not saving?**
- Make sure `APP_KEY` is set in `.env`
- Run `php artisan key:generate`

**Class not found?**
```bash
composer dump-autoload
```

**Database errors?**
- Check your `.env` database settings
- Make sure migrations ran: `php artisan migrate:status`

## Next Steps

- 📖 Read [SETUP.md](SETUP.md) for detailed setup
- 📚 Check [README.md](README.md) for full documentation
- 💡 See [EXAMPLES.md](EXAMPLES.md) for code examples
- 🔒 Add authentication to protect routes
- 🎨 Customize the UI to match your app

## Need Help?

- Check the [README.md](README.md) for detailed API docs
- See [EXAMPLES.md](EXAMPLES.md) for more code examples
- Review [SETUP.md](SETUP.md) for production deployment


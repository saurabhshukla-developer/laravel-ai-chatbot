# Setup and Hosting Guide

## Installation Steps

### Step 1: Install in Your Laravel Project

#### Option A: Install from GitHub (Recommended)

**Quick Install:**
```bash
# Add repository
composer config repositories.laravel-ai-chatbot vcs https://github.com/saurabhshukla-developer/laravel-ai-chatbot

# Install dev-master version
composer require saurabhshukla-developer/laravel-ai-chatbot:dev-master
```

**Or add to your `composer.json` manually:**

The repository configuration should be added to **your Laravel application's `composer.json`** file, NOT in the package's composer.json. Add it after `license` and before `require`:

```json
{
    "license": "MIT",
    
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/saurabhshukla-developer/laravel-ai-chatbot"
        }
    ],
    
    "require": {
        "saurabhshukla-developer/laravel-ai-chatbot": "dev-master"
    }
}
```

**Note:** If you get a minimum-stability error, add this to your `composer.json`:
```json
{
    "minimum-stability": "dev",
    "prefer-stable": true
}
```

Or add to your `composer.json`:

```json
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
```

#### Option B: Install as Local Package (For Development)

If you're developing locally, add this to your Laravel project's `composer.json`:

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "/path/to/laravel-ai-chatbot"
        }
    ],
    "require": {
        "saurabhshukla-developer/laravel-ai-chatbot": "*"
    }
}
```

Then run:

```bash
composer require saurabhshukla-developer/laravel-ai-chatbot
```

### Step 2: Publish Configuration and Migrations

```bash
# Publish configuration
php artisan vendor:publish --provider="LaravelAI\Chatbot\ChatbotServiceProvider" --tag="chatbot-config"

# Publish migrations
php artisan vendor:publish --provider="LaravelAI\Chatbot\ChatbotServiceProvider" --tag="chatbot-migrations"
```

### Step 3: Configure Environment Variables

Add these to your `.env` file:

```env
# Default provider (openai, anthropic, or google)
CHATBOT_DEFAULT_PROVIDER=openai

# Storage driver (database or config)
CHATBOT_STORAGE_DRIVER=database

# Route prefix (optional, defaults to 'chatbot')
CHATBOT_ROUTE_PREFIX=chatbot

# Optional: Set API keys in .env if not using database storage
OPENAI_API_KEY=your-openai-api-key-here
ANTHROPIC_API_KEY=your-anthropic-api-key-here
GOOGLE_AI_API_KEY=your-google-ai-api-key-here
```

**Important**: Make sure your `APP_KEY` is set in `.env` for encryption to work:

```bash
php artisan key:generate
```

### Step 4: Run Migrations

```bash
php artisan migrate
```

This will create two tables:
- `chatbot_api_keys` - Stores encrypted API keys
- `chatbot_ai_agents` - Stores AI agent configurations

### Step 5: (Optional) Publish Views for Customization

If you want to customize the UI:

```bash
php artisan vendor:publish --provider="LaravelAI\Chatbot\ChatbotServiceProvider" --tag="chatbot-views"
```

Views will be published to `resources/views/vendor/chatbot/`.

## Using the Package

### Method 1: Web Interface (Easiest)

1. **Start your Laravel development server:**
   ```bash
   php artisan serve
   ```

2. **Access the package routes:**
   - API Keys: `http://localhost:8000/chatbot/api-keys`
   - AI Agents: `http://localhost:8000/chatbot/agents`

3. **Add your first API key:**
   - Go to `/chatbot/api-keys`
   - Click "Add API Key"
   - Select provider (e.g., OpenAI)
   - Enter your API key
   - Check "Set as default for this provider"
   - Click "Create API Key"

4. **Create your first AI agent:**
   - Go to `/chatbot/agents`
   - Click "Create Agent"
   - Fill in:
     - Name: "Customer Support Bot"
     - Provider: Select your provider
     - System Prompt: "You are a helpful customer support assistant."
   - Click "Create Agent"

5. **Test the agent:**
   - Click on your agent to view details
   - Use the built-in chat interface to test

### Method 2: Programmatic Usage

#### Basic Chat Example

```php
use LaravelAI\Chatbot\Facades\Chatbot;

// In a controller or service
public function chat()
{
    // Get an agent by slug
    $agent = Chatbot::getAgent('customer-support-bot');
    
    // Send a message
    $response = Chatbot::chat($agent, 'Hello, I need help');
    
    return response()->json([
        'message' => $response['content']
    ]);
}
```

#### Create Agent Programmatically

```php
use LaravelAI\Chatbot\Facades\Chatbot;

$agent = Chatbot::createAgent([
    'name' => 'Code Assistant',
    'slug' => 'code-assistant',
    'provider' => 'openai',
    'model' => 'gpt-4',
    'system_prompt' => 'You are a helpful coding assistant.',
    'is_active' => true,
]);
```

#### Using in Routes

```php
// routes/web.php or routes/api.php
use LaravelAI\Chatbot\Facades\Chatbot;
use Illuminate\Http\Request;

Route::post('/api/chat/{agentSlug}', function (Request $request, $agentSlug) {
    $agent = Chatbot::getAgent($agentSlug);
    
    if (!$agent) {
        return response()->json(['error' => 'Agent not found'], 404);
    }
    
    $response = Chatbot::chat($agent, $request->input('message'));
    
    return response()->json($response);
});
```

## Hosting on Production

### Requirements

- PHP 8.1 or higher
- Laravel 10.x or 11.x
- MySQL/PostgreSQL/SQLite database
- Composer
- Web server (Apache/Nginx)

### Deployment Steps

1. **Install dependencies:**
   ```bash
   composer install --optimize-autoloader --no-dev
   ```

2. **Set up environment:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Configure database:**
   Update `.env` with your production database credentials:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=your-db-host
   DB_PORT=3306
   DB_DATABASE=your-database
   DB_USERNAME=your-username
   DB_PASSWORD=your-password
   ```

4. **Run migrations:**
   ```bash
   php artisan migrate --force
   ```

5. **Cache configuration:**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

6. **Set permissions:**
   ```bash
   chmod -R 755 storage bootstrap/cache
   ```

### Security Considerations

1. **Protect Routes with Authentication:**
   
   Update `config/chatbot.php`:
   ```php
   'routes' => [
       'prefix' => env('CHATBOT_ROUTE_PREFIX', 'chatbot'),
       'middleware' => ['web', 'auth'], // Add auth middleware
   ],
   ```

2. **Use HTTPS:**
   Ensure your production site uses HTTPS to protect API keys in transit.

3. **Environment Variables:**
   Never commit `.env` file. Keep API keys secure.

4. **Database Security:**
   - Use strong database passwords
   - Limit database user permissions
   - Regular backups

## Using with Different Web Servers

### Apache (.htaccess)

Ensure mod_rewrite is enabled and `.htaccess` is configured:

```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

### Nginx

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /path/to/your/laravel/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

## Troubleshooting

### Issue: "Class not found" errors

**Solution:** Run `composer dump-autoload`

### Issue: API keys not working

**Solutions:**
1. Check that `APP_KEY` is set in `.env`
2. Verify API keys are correct
3. Check provider configuration in `config/chatbot.php`
4. Ensure API keys are set as default for their provider

### Issue: Routes not found

**Solutions:**
1. Run `php artisan route:clear`
2. Check route prefix in `config/chatbot.php`
3. Verify service provider is registered in `config/app.php`

### Issue: Migration errors

**Solutions:**
1. Check database connection in `.env`
2. Ensure database user has CREATE TABLE permissions
3. Run `php artisan migrate:fresh` (WARNING: deletes all data)

### Issue: Views not found

**Solutions:**
1. Publish views: `php artisan vendor:publish --tag="chatbot-views"`
2. Clear view cache: `php artisan view:clear`

## Quick Start Checklist

- [ ] Install package via Composer
- [ ] Publish configuration and migrations
- [ ] Set `APP_KEY` in `.env`
- [ ] Run migrations
- [ ] Add API keys via web interface
- [ ] Create your first AI agent
- [ ] Test the chat functionality
- [ ] (Optional) Add authentication middleware
- [ ] (Optional) Customize views

## Next Steps

- Read the [README.md](README.md) for detailed API documentation
- Check [EXAMPLES.md](EXAMPLES.md) for code examples
- Customize the package to fit your needs
- Add authentication/authorization as needed
- Deploy to production following security best practices


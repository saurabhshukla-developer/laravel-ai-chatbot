# Usage Examples

## Basic Examples

### Example 1: Simple Chat

```php
use LaravelAI\Chatbot\Facades\Chatbot;

// Get an agent
$agent = Chatbot::getAgent('customer-support');

// Send a message
$response = Chatbot::chat($agent, 'Hello, I need help with my order');

echo $response['content'];
```

### Example 2: Create an Agent Programmatically

```php
use LaravelAI\Chatbot\Facades\Chatbot;

$agent = Chatbot::createAgent([
    'name' => 'Code Assistant',
    'slug' => 'code-assistant',
    'provider' => 'openai',
    'model' => 'gpt-4',
    'system_prompt' => 'You are a helpful coding assistant. Provide clear, concise code examples and explanations.',
    'is_active' => true,
]);
```

### Example 3: Streaming Response

```php
use LaravelAI\Chatbot\Facades\Chatbot;

$agent = Chatbot::getAgent('storyteller');

header('Content-Type: text/plain');
header('X-Accel-Buffering: no'); // Disable buffering for nginx

foreach (Chatbot::streamChat($agent, 'Tell me a short story about a robot') as $chunk) {
    if (!$chunk['done']) {
        echo $chunk['content'];
        flush();
        ob_flush();
    }
}
```

### Example 4: Using in a Controller

```php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use LaravelAI\Chatbot\Facades\Chatbot;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function chat(Request $request, $agentSlug)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $agent = Chatbot::getAgent($agentSlug);

        if (!$agent) {
            return response()->json(['error' => 'Agent not found'], 404);
        }

        try {
            $response = Chatbot::chat($agent, $request->message, [
                'temperature' => 0.7,
                'max_tokens' => 1000,
            ]);

            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
```

### Example 5: Custom Provider Usage

```php
use LaravelAI\Chatbot\Facades\Chatbot;

// Get a specific provider
$openAIProvider = Chatbot::provider('openai');

// Use it directly with an agent
$agent = Chatbot::getAgent('my-agent');
$response = $openAIProvider->chat($agent, 'Hello');
```

### Example 6: Building a Chat Interface

```blade
<!-- resources/views/chat.blade.php -->
<div id="chat-container">
    <div id="messages"></div>
    <form id="chat-form">
        <input type="text" id="message-input" placeholder="Type your message...">
        <button type="submit">Send</button>
    </form>
</div>

<script>
document.getElementById('chat-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const input = document.getElementById('message-input');
    const message = input.value;
    
    const response = await fetch('/api/chat/{{ $agent->slug }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ message })
    });
    
    const data = await response.json();
    document.getElementById('messages').innerHTML += 
        '<div class="message">' + data.content + '</div>';
    
    input.value = '';
});
</script>
```

### Example 7: Managing API Keys Programmatically

```php
use LaravelAI\Chatbot\Models\ApiKey;

// Create an API key
$apiKey = ApiKey::create([
    'provider' => 'openai',
    'name' => 'Production OpenAI Key',
    'api_key' => 'sk-...',
    'is_default' => true,
    'is_active' => true,
]);

// Get default key for provider
$defaultKey = ApiKey::defaultForProvider('openai')->first();

// Get decrypted key
$decryptedKey = $defaultKey->getDecryptedApiKey();
```

### Example 8: Advanced Agent Configuration

```php
use LaravelAI\Chatbot\Facades\Chatbot;

$agent = Chatbot::createAgent([
    'name' => 'Research Assistant',
    'slug' => 'research-assistant',
    'provider' => 'anthropic',
    'model' => 'claude-3-opus-20240229',
    'system_prompt' => 'You are a research assistant. Always cite sources and provide detailed explanations.',
    'config' => [
        'temperature' => 0.3, // Lower temperature for more focused responses
        'max_tokens' => 4000,
    ],
    'is_active' => true,
]);

// Assign tools to the agent (see TOOLS_EXAMPLES.md for detailed tool examples)
use LaravelAI\Chatbot\Models\Tool;
$tools = Tool::whereIn('slug', ['web_search', 'calculator'])->get();
$agent->tools()->attach($tools->pluck('id'));
```

> **Note**: For comprehensive examples on creating and using tools, see [TOOLS_EXAMPLES.md](TOOLS_EXAMPLES.md)

### Example 9: Error Handling

```php
use LaravelAI\Chatbot\Facades\Chatbot;

try {
    $agent = Chatbot::getAgent('my-agent');
    
    if (!$agent) {
        throw new \Exception('Agent not found');
    }
    
    if (!$agent->is_active) {
        throw new \Exception('Agent is not active');
    }
    
    $response = Chatbot::chat($agent, 'Hello');
    
} catch (\InvalidArgumentException $e) {
    // Handle invalid arguments
    logger()->error('Invalid argument: ' . $e->getMessage());
} catch (\Exception $e) {
    // Handle other errors (API failures, etc.)
    logger()->error('Chat error: ' . $e->getMessage());
    return response()->json(['error' => 'Failed to get response'], 500);
}
```

### Example 10: Using in a Queue Job

```php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use LaravelAI\Chatbot\Facades\Chatbot;

class ProcessChatMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public $agentId,
        public string $message
    ) {}

    public function handle()
    {
        $agent = Chatbot::getAgent($this->agentId);
        $response = Chatbot::chat($agent, $this->message);
        
        // Process the response...
        // Save to database, send notification, etc.
    }
}
```


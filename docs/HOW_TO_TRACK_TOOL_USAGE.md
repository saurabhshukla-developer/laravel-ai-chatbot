# How to Know If Tools Are Being Used

This guide shows you multiple ways to verify and track when your AI agents are using tools.

## 🔍 Method 1: Check Response Data

The response from `Chatbot::chat()` includes tool call information:

```php
use LaravelAI\Chatbot\Facades\Chatbot;

$agent = Chatbot::getAgent('your-agent');
$response = Chatbot::chat($agent, 'What is 25 * 4?');

// Check if tools were called
if (!empty($response['tool_calls'])) {
    echo "Tools were called!\n";
    print_r($response['tool_calls']);
}

// The content will show the final answer after tool execution
echo $response['content'];
```

## 🔍 Method 2: Enable Logging

Add logging to track tool execution. Update your tool's `execute()` method:

```php
<?php

namespace App\Tools;

use LaravelAI\Chatbot\Tools\BaseTool;
use Illuminate\Support\Facades\Log;

class CalculatorTool extends BaseTool
{
    // ... other methods ...

    public function execute(array $arguments): mixed
    {
        // Log when tool is called
        Log::info('🔧 Calculator Tool Called', [
            'arguments' => $arguments,
            'timestamp' => now(),
        ]);

        $result = eval("return {$arguments['expression']};");
        
        // Log the result
        Log::info('✅ Calculator Tool Result', [
            'result' => $result,
            'expression' => $arguments['expression'],
        ]);

        return [
            'success' => true,
            'result' => $result,
        ];
    }
}
```

Then check logs:
```bash
tail -f storage/logs/laravel.log | grep "Calculator Tool"
```

## 🔍 Method 3: Add Debug Mode

Create a helper method to check tool usage:

```php
use LaravelAI\Chatbot\Facades\Chatbot;
use Illuminate\Support\Facades\Log;

function chatWithDebug($agentSlug, $message) {
    $agent = Chatbot::getAgent($agentSlug);
    
    // Log before chat
    Log::info('💬 Chat Request', [
        'agent' => $agentSlug,
        'message' => $message,
        'tools_assigned' => $agent->getFormattedTools(),
    ]);
    
    $response = Chatbot::chat($agent, $message);
    
    // Log after chat
    Log::info('📝 Chat Response', [
        'content' => $response['content'],
        'tool_calls' => $response['tool_calls'] ?? [],
        'model' => $response['model'] ?? null,
    ]);
    
    return $response;
}

// Use it
$response = chatWithDebug('math-assistant', 'Calculate 10 * 5');
```

## 🔍 Method 4: Check Agent Configuration

Verify tools are assigned:

```php
use LaravelAI\Chatbot\Facades\Chatbot;

$agent = Chatbot::getAgent('your-agent');

// Check file-based tools
$fileTools = $agent->config['file_tools'] ?? [];
echo "File-based tools: " . json_encode($fileTools) . "\n";

// Check database tools
$dbTools = $agent->tools()->pluck('slug')->toArray();
echo "Database tools: " . json_encode($dbTools) . "\n";

// Get all formatted tools (what's sent to OpenAI)
$allTools = $agent->getFormattedTools();
echo "All tools sent to AI: " . json_encode($allTools, JSON_PRETTY_PRINT) . "\n";
```

## 🔍 Method 5: Monitor API Calls

Add logging to the provider to see actual API calls:

Create a custom provider wrapper or add logging to `BaseProvider.php`:

```php
// In BaseProvider.php chat method (temporary for debugging)
public function chat(AiAgent $agent, string $message, array $options = []): array
{
    $headers = $this->buildHeaders();
    $messages = [];
    $maxIterations = 5;
    $iteration = 0;
    $lastResponse = null;

    while ($iteration < $maxIterations) {
        $payload = $this->buildPayload($agent, $message, $options, $messages);
        
        // Log the request
        \Log::info('📤 API Request', [
            'iteration' => $iteration,
            'endpoint' => $this->getChatEndpoint(),
            'payload' => $payload,
        ]);
        
        $response = Http::withHeaders($headers)
            ->post($this->getChatEndpoint(), $payload);

        $responseData = $response->json();
        $lastResponse = $responseData;
        
        // Log the response
        \Log::info('📥 API Response', [
            'iteration' => $iteration,
            'response' => $responseData,
        ]);
        
        $toolCalls = $this->extractToolCalls($responseData);
        
        if (empty($toolCalls)) {
            return $this->parseResponse($responseData);
        }

        // Log tool calls
        \Log::info('🔧 Tool Calls Detected', [
            'tool_calls' => $toolCalls,
        ]);

        // ... rest of the code ...
    }
}
```

## 🔍 Method 6: Create a Tool Usage Tracker

Create a middleware or service to track tool usage:

```php
<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use LaravelAI\Chatbot\Tools\ToolLoader;

class ToolUsageTracker
{
    public static function track($toolSlug, $arguments, $result)
    {
        Log::channel('tools')->info('Tool Used', [
            'tool' => $toolSlug,
            'arguments' => $arguments,
            'result' => $result,
            'timestamp' => now(),
        ]);
        
        // Or save to database
        // ToolUsage::create([...]);
    }
}
```

Then update `BaseProvider.php` to use it:

```php
protected function executeTool(AiAgent $agent, array $toolCall): mixed
{
    $toolName = $this->getToolNameFromCall($toolCall);
    $arguments = $this->getToolArgumentsFromCall($toolCall);
    
    // ... execute tool ...
    
    // Track usage
    \App\Services\ToolUsageTracker::track($toolName, $arguments, $result);
    
    return $result;
}
```

## 🔍 Method 7: Visual Indicator in Response

Modify your chat response to include tool usage info:

```php
use LaravelAI\Chatbot\Facades\Chatbot;

$agent = Chatbot::getAgent('math-assistant');
$response = Chatbot::chat($agent, 'Calculate 10 * 5');

// Add tool usage info to response
if (!empty($response['tool_calls'])) {
    $response['debug'] = [
        'tools_used' => array_map(function($call) {
            return $call['function']['name'];
        }, $response['tool_calls']),
        'tool_count' => count($response['tool_calls']),
    ];
}

return response()->json($response);
```

## 🔍 Method 8: Database Tracking

Create a migration to track tool usage:

```php
Schema::create('chatbot_tool_usage', function (Blueprint $table) {
    $table->id();
    $table->foreignId('agent_id')->constrained('chatbot_ai_agents');
    $table->string('tool_slug');
    $table->json('arguments');
    $table->json('result');
    $table->timestamps();
});
```

Then log usage:

```php
\DB::table('chatbot_tool_usage')->insert([
    'agent_id' => $agent->id,
    'tool_slug' => $toolName,
    'arguments' => json_encode($arguments),
    'result' => json_encode($result),
    'created_at' => now(),
]);
```

## 🔍 Method 9: Real-Time Monitoring

Add a simple endpoint to check tool usage:

```php
// In routes/web.php or api.php
Route::get('/chatbot/debug/tool-usage', function() {
    $usage = \DB::table('chatbot_tool_usage')
        ->orderBy('created_at', 'desc')
        ->limit(50)
        ->get();
    
    return response()->json($usage);
});
```

## 🔍 Method 10: Test Tool Execution Directly

Test if your tool works independently:

```php
use LaravelAI\Chatbot\Tools\ToolLoader;

// Test tool discovery
$tools = ToolLoader::discover();
dd($tools); // Should show your tools

// Test tool execution
$result = ToolLoader::execute('calculator', [
    'expression' => '2 + 2'
]);
dd($result); // Should return result

// Test tool definition
$tool = ToolLoader::getBySlug('calculator');
dd($tool->getDefinition()); // Should show OpenAI format
```

## ✅ Quick Checklist

To verify tools are being used:

- [ ] Tools are assigned to agent (check in UI or database)
- [ ] Tool files exist in `app/Tools/`
- [ ] Tool slug matches (check `slug()` method)
- [ ] Response includes `tool_calls` array
- [ ] Logs show tool execution
- [ ] Final response contains calculated/processed data
- [ ] Test tool execution directly works

## 🎯 Expected Behavior

When tools ARE being used:

1. **First API Call**: OpenAI responds with `tool_calls` (no `content`)
2. **Tool Execution**: Your tool's `execute()` method runs
3. **Second API Call**: Tool result sent back to OpenAI
4. **Final Response**: OpenAI processes result and returns `content` with answer

When tools are NOT being used:

1. **Single API Call**: OpenAI responds directly with `content`
2. **No tool_calls**: Response has no `tool_calls` array

## 📊 Example: Complete Debug Function

```php
function debugChatWithTools($agentSlug, $message) {
    $agent = Chatbot::getAgent($agentSlug);
    
    echo "=== CHAT DEBUG ===\n";
    echo "Agent: {$agentSlug}\n";
    echo "Message: {$message}\n\n";
    
    // Check tools
    $tools = $agent->getFormattedTools();
    echo "Tools assigned: " . count($tools) . "\n";
    foreach ($tools as $tool) {
        echo "  - " . ($tool['function']['name'] ?? 'unknown') . "\n";
    }
    echo "\n";
    
    // Make request
    $start = microtime(true);
    $response = Chatbot::chat($agent, $message);
    $duration = round((microtime(true) - $start) * 1000, 2);
    
    // Show results
    echo "Response time: {$duration}ms\n";
    echo "Tool calls: " . count($response['tool_calls'] ?? []) . "\n";
    
    if (!empty($response['tool_calls'])) {
        echo "Tools used:\n";
        foreach ($response['tool_calls'] as $call) {
            echo "  - " . ($call['function']['name'] ?? 'unknown') . "\n";
        }
    }
    
    echo "\nFinal answer:\n";
    echo $response['content'] . "\n";
    echo "==================\n";
    
    return $response;
}

// Use it
debugChatWithTools('math-assistant', 'What is 25 * 4?');
```

---

**Quick Test:** Run this to see if tools are working:

```php
$agent = Chatbot::getAgent('your-agent');
$response = Chatbot::chat($agent, 'Calculate 10 * 5');

if (!empty($response['tool_calls'])) {
    echo "✅ Tools ARE being used!\n";
    echo "Tools called: " . count($response['tool_calls']) . "\n";
} else {
    echo "❌ Tools are NOT being used\n";
    echo "Check if tools are assigned to the agent\n";
}

echo "Answer: " . $response['content'] . "\n";
```


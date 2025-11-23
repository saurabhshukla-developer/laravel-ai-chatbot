# Quick Check: Are Tools Being Used?

## 🚀 Fastest Way to Check

Run this code to see if tools are being used:

```php
use LaravelAI\Chatbot\Facades\Chatbot;

$agent = Chatbot::getAgent('your-agent-slug');
$response = Chatbot::chat($agent, 'Calculate 10 * 5');

// Check response
echo "Content: " . ($response['content'] ?? 'EMPTY') . "\n";
echo "Tool Calls: " . (count($response['tool_calls'] ?? [])) . "\n";

if (!empty($response['tool_calls'])) {
    echo "✅ YES! Tools ARE being used!\n";
    foreach ($response['tool_calls'] as $call) {
        echo "  - Tool: " . ($call['function']['name'] ?? 'unknown') . "\n";
    }
} else {
    echo "❌ NO! Tools are NOT being used.\n";
    echo "Check:\n";
    echo "  1. Are tools assigned to the agent?\n";
    echo "  2. Is the tool file in app/Tools/?\n";
    echo "  3. Does the tool slug match?\n";
}
```

## 📊 Enable Logging

Add to your `.env`:

```env
CHATBOT_LOG_TOOL_USAGE=true
```

Or set `APP_DEBUG=true` (logs all tool usage automatically).

Then check logs:
```bash
tail -f storage/logs/laravel.log | grep "Tool"
```

You'll see:
- `🔍 Tool Calls Detected` - When OpenAI wants to use a tool
- `🔧 Tool Execution Started` - When your tool starts running
- `✅ Tool Execution Completed` - When tool finishes successfully
- `❌ Tool Execution Failed` - If there's an error

## ✅ Quick Checklist

1. **Tools assigned?**
   ```php
   $agent = Chatbot::getAgent('your-agent');
   $tools = $agent->getFormattedTools();
   echo count($tools) . " tools assigned\n";
   ```

2. **Tool exists?**
   ```php
   use LaravelAI\Chatbot\Tools\ToolLoader;
   $tool = ToolLoader::getBySlug('calculator');
   echo $tool ? "✅ Tool found" : "❌ Tool not found";
   ```

3. **Response has tool_calls?**
   ```php
   $response = Chatbot::chat($agent, 'Calculate 2+2');
   echo !empty($response['tool_calls']) ? "✅ Tools used" : "❌ No tools";
   ```

## 🎯 Expected Output

**When tools ARE working:**
```
Content: The result of 10 * 5 is 50.
Tool Calls: 1
✅ YES! Tools ARE being used!
  - Tool: calculator
```

**When tools are NOT working:**
```
Content: [empty or generic response]
Tool Calls: 0
❌ NO! Tools are NOT being used.
```

---

See [HOW_TO_TRACK_TOOL_USAGE.md](HOW_TO_TRACK_TOOL_USAGE.md) for detailed tracking methods.


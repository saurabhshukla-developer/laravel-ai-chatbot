# Tools Usage Examples

This guide shows you how to create, manage, and use tools with AI agents in the Laravel AI Chatbot package.

## What are Tools?

Tools are functions that AI agents can call to perform actions beyond text generation. For example:
- **Calculator**: Perform mathematical calculations
- **Weather API**: Get current weather information
- **Database Query**: Search or retrieve data
- **Email Sender**: Send emails
- **Web Search**: Search the internet

When you assign tools to an agent, the AI model can decide when to use them based on the user's request.

---

## Example 1: Creating a Tool via Web UI

1. Navigate to `/chatbot/tools` in your browser
2. Click "Create Tool"
3. Fill in the form:

**Example: Calculator Tool**

- **Name**: Calculator
- **Slug**: calculator (auto-generated)
- **Description**: Performs basic mathematical calculations
- **Type**: Function
- **Definition** (JSON):
```json
{
  "type": "function",
  "function": {
    "name": "calculator",
    "description": "Performs basic mathematical calculations including addition, subtraction, multiplication, and division.",
    "parameters": {
      "type": "object",
      "properties": {
        "expression": {
          "type": "string",
          "description": "The mathematical expression to evaluate (e.g., '2 + 2', '10 * 5', '100 / 4')"
        }
      },
      "required": ["expression"]
    }
  }
}
```

- **Implementation** (Optional - for documentation):
```php
// This tool evaluates mathematical expressions
// Example: calculator("2 + 2") returns 4
```

4. Click "Create Tool"

---

## Example 2: Creating a Tool Programmatically

```php
use LaravelAI\Chatbot\Models\Tool;

// Create a weather tool
$weatherTool = Tool::create([
    'name' => 'Get Weather',
    'slug' => 'get_weather',
    'description' => 'Gets the current weather for a given location',
    'type' => 'function',
    'definition' => [
        'type' => 'function',
        'function' => [
            'name' => 'get_weather',
            'description' => 'Get the current weather conditions for a specific location',
            'parameters' => [
                'type' => 'object',
                'properties' => [
                    'location' => [
                        'type' => 'string',
                        'description' => 'The city and state, e.g. San Francisco, CA'
                    ],
                    'unit' => [
                        'type' => 'string',
                        'enum' => ['celsius', 'fahrenheit'],
                        'description' => 'The unit of temperature'
                    ]
                ],
                'required' => ['location']
            ]
        ]
    ],
    'implementation' => 'Calls weather API to get current conditions',
    'is_active' => true,
]);
```

---

## Example 3: Creating a Database Query Tool

```php
use LaravelAI\Chatbot\Models\Tool;

$databaseTool = Tool::create([
    'name' => 'Search Users',
    'slug' => 'search_users',
    'description' => 'Searches for users in the database by name or email',
    'type' => 'function',
    'definition' => [
        'type' => 'function',
        'function' => [
            'name' => 'search_users',
            'description' => 'Search for users in the database',
            'parameters' => [
                'type' => 'object',
                'properties' => [
                    'query' => [
                        'type' => 'string',
                        'description' => 'Search term (name or email)'
                    ],
                    'limit' => [
                        'type' => 'integer',
                        'description' => 'Maximum number of results to return',
                        'default' => 10
                    ]
                ],
                'required' => ['query']
            ]
        ]
    ],
    'is_active' => true,
]);
```

---

## Example 4: Assigning Tools to an Agent

### Via Web UI

1. Navigate to `/chatbot/agents`
2. Create a new agent or edit an existing one
3. Scroll to the "Tools" section
4. Check the tools you want to assign
5. Save the agent

### Programmatically

```php
use LaravelAI\Chatbot\Facades\Chatbot;
use LaravelAI\Chatbot\Models\Tool;

// Get the tools
$calculatorTool = Tool::where('slug', 'calculator')->first();
$weatherTool = Tool::where('slug', 'get_weather')->first();

// Create an agent with tools
$agent = Chatbot::createAgent([
    'name' => 'Assistant with Tools',
    'slug' => 'assistant-with-tools',
    'provider' => 'openai',
    'model' => 'gpt-4',
    'system_prompt' => 'You are a helpful assistant that can perform calculations and check weather.',
    'is_active' => true,
]);

// Assign tools to the agent
$agent->tools()->attach([$calculatorTool->id, $weatherTool->id]);

// Or using sync (replaces all existing tools)
$agent->tools()->sync([$calculatorTool->id, $weatherTool->id]);
```

---

## Example 5: Using an Agent with Tools

Once tools are assigned to an agent, they are automatically included in API calls:

```php
use LaravelAI\Chatbot\Facades\Chatbot;

// Get an agent with tools
$agent = Chatbot::getAgent('assistant-with-tools');

// Chat with the agent - tools are automatically available
$response = Chatbot::chat($agent, 'What is 25 * 4?');

// The AI will recognize it needs to use the calculator tool
// and will request to call it (depending on the provider's response)

echo $response['content'];
```

---

## Example 6: Complete Example - Calculator Agent

Here's a complete example of creating a calculator agent:

```php
use LaravelAI\Chatbot\Facades\Chatbot;
use LaravelAI\Chatbot\Models\Tool;

// Step 1: Create the calculator tool
$calculatorTool = Tool::create([
    'name' => 'Calculator',
    'slug' => 'calculator',
    'description' => 'Performs mathematical calculations',
    'type' => 'function',
    'definition' => [
        'type' => 'function',
        'function' => [
            'name' => 'calculator',
            'description' => 'Evaluates a mathematical expression and returns the result',
            'parameters' => [
                'type' => 'object',
                'properties' => [
                    'expression' => [
                        'type' => 'string',
                        'description' => 'Mathematical expression like "2+2", "10*5", "100/4"'
                    ]
                ],
                'required' => ['expression']
            ]
        ]
    ],
    'is_active' => true,
]);

// Step 2: Create an agent
$agent = Chatbot::createAgent([
    'name' => 'Math Assistant',
    'slug' => 'math-assistant',
    'provider' => 'openai',
    'model' => 'gpt-4',
    'system_prompt' => 'You are a helpful math assistant. Use the calculator tool when users ask for calculations.',
    'is_active' => true,
]);

// Step 3: Assign the tool to the agent
$agent->tools()->attach($calculatorTool->id);

// Step 4: Use the agent
$response = Chatbot::chat($agent, 'Calculate 15 * 23 + 100');
echo $response['content'];
```

---

## Example 7: Tool Definition Formats

### OpenAI Format (Standard)

```json
{
  "type": "function",
  "function": {
    "name": "function_name",
    "description": "What the function does",
    "parameters": {
      "type": "object",
      "properties": {
        "param_name": {
          "type": "string",
          "description": "Parameter description"
        }
      },
      "required": ["param_name"]
    }
  }
}
```

### Example: Email Sender Tool

```json
{
  "type": "function",
  "function": {
    "name": "send_email",
    "description": "Sends an email to a recipient",
    "parameters": {
      "type": "object",
      "properties": {
        "to": {
          "type": "string",
          "description": "Recipient email address"
        },
        "subject": {
          "type": "string",
          "description": "Email subject line"
        },
        "body": {
          "type": "string",
          "description": "Email body content"
        }
      },
      "required": ["to", "subject", "body"]
    }
  }
}
```

---

## Example 8: Viewing Tools Assigned to an Agent

```php
use LaravelAI\Chatbot\Facades\Chatbot;

$agent = Chatbot::getAgent('math-assistant');

// Get all tools assigned to the agent
$tools = $agent->tools;

foreach ($tools as $tool) {
    echo "Tool: {$tool->name}\n";
    echo "Description: {$tool->description}\n";
    echo "Definition: " . json_encode($tool->definition, JSON_PRETTY_PRINT) . "\n\n";
}

// Get formatted tools for API calls
$formattedTools = $agent->getFormattedTools();
print_r($formattedTools);
```

---

## Example 9: Managing Tools

```php
use LaravelAI\Chatbot\Models\Tool;

// Get all active tools
$activeTools = Tool::active()->get();

// Get a specific tool
$tool = Tool::where('slug', 'calculator')->first();

// Update a tool
$tool->update([
    'description' => 'Updated description',
    'is_active' => false, // Deactivate
]);

// Delete a tool
$tool->delete();

// Get agents using a tool
$agents = $tool->agents;
```

---

## Example 10: Real-World Use Case - Customer Support Agent with Tools

```php
use LaravelAI\Chatbot\Facades\Chatbot;
use LaravelAI\Chatbot\Models\Tool;

// Create tools for customer support
$orderLookupTool = Tool::create([
    'name' => 'Lookup Order',
    'slug' => 'lookup_order',
    'description' => 'Looks up order information by order number',
    'type' => 'function',
    'definition' => [
        'type' => 'function',
        'function' => [
            'name' => 'lookup_order',
            'description' => 'Retrieves order details from the database',
            'parameters' => [
                'type' => 'object',
                'properties' => [
                    'order_number' => [
                        'type' => 'string',
                        'description' => 'The order number to look up'
                    ]
                ],
                'required' => ['order_number']
            ]
        ]
    ],
    'is_active' => true,
]);

$refundTool = Tool::create([
    'name' => 'Process Refund',
    'slug' => 'process_refund',
    'description' => 'Initiates a refund for an order',
    'type' => 'function',
    'definition' => [
        'type' => 'function',
        'function' => [
            'name' => 'process_refund',
            'description' => 'Processes a refund for a given order',
            'parameters' => [
                'type' => 'object',
                'properties' => [
                    'order_number' => [
                        'type' => 'string',
                        'description' => 'Order number to refund'
                    ],
                    'amount' => [
                        'type' => 'number',
                        'description' => 'Refund amount (optional, defaults to full amount)'
                    ]
                ],
                'required' => ['order_number']
            ]
        ]
    ],
    'is_active' => true,
]);

// Create customer support agent
$agent = Chatbot::createAgent([
    'name' => 'Customer Support',
    'slug' => 'customer-support',
    'provider' => 'openai',
    'model' => 'gpt-4',
    'system_prompt' => 'You are a helpful customer support agent. Use the lookup_order tool to find order information and process_refund tool when customers request refunds.',
    'is_active' => true,
]);

// Assign tools
$agent->tools()->attach([
    $orderLookupTool->id,
    $refundTool->id,
]);

// Use the agent
$response = Chatbot::chat($agent, 'Can you check the status of order #12345?');
```

---

## Important Notes

1. **Tool Execution**: The current implementation sends tools to AI providers, but actual tool execution (when the AI requests to call a tool) needs to be handled in your application. The AI will return a tool call request that you need to process.

2. **Provider Compatibility**: 
   - OpenAI: Fully supports function calling
   - Anthropic: Supports tools (converted to Anthropic format)
   - Google AI: Supports function calling (converted to Google format)

3. **Tool Definitions**: Always use valid JSON Schema for tool parameters. The AI models use these definitions to understand when and how to call tools.

4. **Tool Naming**: Use descriptive names and clear descriptions. The AI uses these to decide when to call tools.

5. **Testing**: After creating tools, test them with your agents to ensure they work as expected.

---

## Next Steps

- Check the AI provider's documentation for function calling:
  - [OpenAI Function Calling](https://platform.openai.com/docs/guides/function-calling)
  - [Anthropic Tools](https://docs.anthropic.com/claude/docs/tool-use)
  - [Google AI Function Calling](https://ai.google.dev/docs/function_calling)

- Implement tool execution handlers in your application to process tool calls from the AI

- Consider creating a tool execution service to handle tool calls centrally


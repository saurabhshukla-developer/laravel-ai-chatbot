# Easy Tool Creation Guide

## 🚀 Super Easy Tool Creation!

Creating tools is now **super simple** with artisan commands!

## Quick Start

### 1. Create a Tool (One Command!)

```bash
php artisan chatbot:make-tool Calculator
```

That's it! The tool file is created automatically.

### 2. Edit the Tool

Open `app/Tools/CalculatorTool.php` and customize it:

```php
public function execute(array $arguments): mixed
{
    $expression = $arguments['param1']; // Change param1 to 'expression'
    $result = eval("return {$expression};");
    return ['result' => $result];
}
```

### 3. Test It

```bash
php artisan chatbot:test-tool calculator --args='{"param1":"2+2"}'
```

### 4. List All Tools

```bash
php artisan chatbot:list-tools
```

## Available Commands

### `chatbot:make-tool`

Create a new tool file:

```bash
# Basic usage
php artisan chatbot:make-tool Calculator

# With description
php artisan chatbot:make-tool Weather --description="Gets weather for a location"

# Overwrite existing
php artisan chatbot:make-tool Calculator --force
```

**What it does:**
- Creates `app/Tools/CalculatorTool.php`
- Generates boilerplate code
- Sets up class structure
- Ready to customize!

### `chatbot:test-tool`

Test a tool directly:

```bash
# Test with default empty args
php artisan chatbot:test-tool calculator

# Test with custom arguments
php artisan chatbot:test-tool calculator --args='{"expression":"2+2"}'

# Test weather tool
php artisan chatbot:test-tool get-weather --args='{"location":"London"}'
```

### `chatbot:list-tools`

List all available tools:

```bash
php artisan chatbot:list-tools
```

Output:
```
Found 2 tool(s):

+------------+-------------+--------------------------------------------------+
| Name       | Slug        | Description                                      |
+------------+-------------+--------------------------------------------------+
| Calculator | calculator  | Performs mathematical calculations              |
| Weather    | get-weather | Gets weather for a location                     |
+------------+-------------+--------------------------------------------------+
```

## Complete Example

### Step 1: Create Tool

```bash
php artisan chatbot:make-tool EmailSender --description="Sends emails"
```

### Step 2: Edit Tool File

Open `app/Tools/EmailSenderTool.php`:

```php
public function parameters(): array
{
    return [
        'properties' => [
            'to' => [
                'type' => 'string',
                'description' => 'Recipient email address',
            ],
            'subject' => [
                'type' => 'string',
                'description' => 'Email subject',
            ],
            'body' => [
                'type' => 'string',
                'description' => 'Email body',
            ],
        ],
        'required' => ['to', 'subject', 'body'],
    ];
}

public function execute(array $arguments): mixed
{
    $this->validateArguments($arguments, ['to', 'subject', 'body']);
    
    // Send email logic here
    \Mail::to($arguments['to'])->send(new \App\Mail\CustomMail(
        $arguments['subject'],
        $arguments['body']
    ));
    
    return [
        'success' => true,
        'message' => "Email sent to {$arguments['to']}",
    ];
}
```

### Step 3: Test It

```bash
php artisan chatbot:test-tool email-sender --args='{"to":"test@example.com","subject":"Hello","body":"Test email"}'
```

### Step 4: Assign to Agent

Via UI: `/chatbot/agents` → Edit agent → Check "Email Sender"

Or via code:
```php
$agent = Chatbot::getAgent('my-agent');
$config = $agent->config ?? [];
$config['file_tools'] = ['email-sender'];
$agent->update(['config' => $config]);
```

## Tool Creation Workflow

```
1. php artisan chatbot:make-tool YourTool
   ↓
2. Edit app/Tools/YourToolTool.php
   ↓
3. php artisan chatbot:test-tool your-tool
   ↓
4. Assign to agent
   ↓
5. Use in chat!
```

## Tips

1. **Use descriptive names**: `EmailSender` not `Email`
2. **Add good descriptions**: Helps AI understand when to use it
3. **Test before assigning**: Use `chatbot:test-tool` first
4. **Check available tools**: Use `chatbot:list-tools` to see what's available

## Troubleshooting

**Command not found?**
```bash
php artisan list | grep chatbot
```

**Tool not showing?**
```bash
php artisan cache:clear
php artisan chatbot:list-tools
```

**File already exists?**
```bash
php artisan chatbot:make-tool Name --force
```

---

**That's it!** Creating tools is now as easy as running one command! 🎉


# File-Based Tools - Quick Start Guide

## 🎯 What's New?

You can now create tools by simply creating PHP files! No database setup, no JSON definitions - just create a class and it's automatically available.

## ✨ Key Benefits

- ✅ **Super Simple** - Just create a PHP file
- ✅ **Auto-Discovered** - Tools are automatically found
- ✅ **Type-Safe** - Full PHP type checking and IDE support
- ✅ **Professional** - Clean, maintainable code structure
- ✅ **No Database** - File-based, version controlled

## 🚀 Quick Start (30 seconds)

1. **Create the tools directory:**
   ```bash
   mkdir -p app/Tools
   ```

2. **Create a tool file** (`app/Tools/CalculatorTool.php`):
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

3. **Done!** The tool is now available in `/chatbot/tools` and can be assigned to agents.

## 📁 File Structure

```
app/
└── Tools/
    ├── CalculatorTool.php
    ├── WeatherTool.php
    └── Database/
        └── SearchUsersTool.php
```

## 🔧 Configuration

Set your tools directory in `.env`:

```env
CHATBOT_TOOLS_PATH=app/Tools
```

Or use the default: `app/Tools`

## 📝 Creating a Tool

Every tool must:

1. **Extend `BaseTool`**
2. **Implement 4 methods:**
   - `name()` - Display name
   - `description()` - What it does (shown to AI)
   - `parameters()` - JSON Schema for parameters
   - `execute()` - Tool logic

## 💡 Example Tools

See `src/Tools/Examples/` for complete examples:
- `CalculatorTool.php` - Math calculations
- `WeatherTool.php` - Weather API integration

## 🎨 Using Tools

### Assign to Agent (Web UI)
1. Go to `/chatbot/agents`
2. Create/edit an agent
3. Check file-based tools
4. Save

### Assign Programmatically
```php
$agent = Chatbot::getAgent('my-agent');
$config = $agent->config ?? [];
$config['file_tools'] = ['calculator', 'weather']; // Use slugs
$agent->update(['config' => $config]);
```

## 🔍 How It Works

1. **Discovery**: `ToolLoader` scans `app/Tools/` directory
2. **Loading**: Finds classes extending `BaseTool`
3. **Registration**: Tools are available immediately
4. **Usage**: Assign to agents via UI or code
5. **Execution**: AI can call tools when needed

## 📚 Documentation

- **[TOOLS_README.md](TOOLS_README.md)** - Complete guide with examples
- **[TOOLS_EXAMPLES.md](TOOLS_EXAMPLES.md)** - Advanced examples
- **Template**: See `src/Tools/TEMPLATE.php` for a starter template

## 🆚 File-Based vs Database Tools

| Feature | File-Based | Database |
|---------|-----------|----------|
| Setup | Create PHP file | Fill web form |
| Version Control | ✅ Yes | ❌ No |
| IDE Support | ✅ Full | ❌ Limited |
| Type Safety | ✅ Yes | ❌ No |
| Auto-Discovery | ✅ Yes | ❌ Manual |
| **Recommended** | ✅ **Yes** | For simple cases |

## 🎓 Best Practices

1. Use descriptive names and descriptions
2. Validate all inputs in `execute()`
3. Return structured data (arrays/objects)
4. Handle errors gracefully
5. Use namespaces for organization
6. Write clear parameter descriptions

## 🐛 Troubleshooting

**Tool not showing?**
- Check it extends `BaseTool`
- Verify namespace matches directory
- Clear cache: `php artisan cache:clear`

**Need help?**
- Check examples in `src/Tools/Examples/`
- See [TOOLS_README.md](TOOLS_README.md)
- Review template: `src/Tools/TEMPLATE.php`

---

**Ready to create your first tool?** Copy `src/Tools/TEMPLATE.php` to `app/Tools/` and customize it!


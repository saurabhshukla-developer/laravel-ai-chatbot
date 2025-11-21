<?php

namespace LaravelAI\Chatbot\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeToolCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'chatbot:make-tool 
                            {name : The name of the tool}
                            {--description= : Description of what the tool does}
                            {--force : Overwrite existing tool file}';

    /**
     * The console command description.
     */
    protected $description = 'Create a new chatbot tool file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $description = $this->option('description') ?: "Tool for {$name}";
        $force = $this->option('force');

        // Generate class name
        $className = Str::studly($name) . 'Tool';
        $slug = Str::slug($name);

        // Get tools path
        $toolsPath = config('chatbot.tools_path', base_path('app/Tools'));
        if (!Str::startsWith($toolsPath, '/')) {
            $toolsPath = base_path($toolsPath);
        }

        // Create directory if it doesn't exist
        if (!File::exists($toolsPath)) {
            File::makeDirectory($toolsPath, 0755, true);
            $this->info("Created directory: {$toolsPath}");
        }

        $filePath = $toolsPath . '/' . $className . '.php';

        // Check if file exists
        if (File::exists($filePath) && !$force) {
            $this->error("Tool file already exists: {$filePath}");
            $this->info("Use --force to overwrite");
            return 1;
        }

        // Generate tool code
        $stub = $this->getStub();
        $code = str_replace(
            [
                '{{CLASS_NAME}}',
                '{{TOOL_NAME}}',
                '{{TOOL_DESCRIPTION}}',
                '{{TOOL_SLUG}}',
            ],
            [
                $className,
                $name,
                $description,
                $slug,
            ],
            $stub
        );

        // Write file
        File::put($filePath, $code);

        $this->info("✅ Tool created successfully!");
        $this->line("📁 File: {$filePath}");
        $this->line("🔧 Class: {$className}");
        $this->line("🏷️  Slug: {$slug}");
        $this->newLine();
        $this->info("Next steps:");
        $this->line("1. Edit the tool file to implement your logic");
        $this->line("2. Assign the tool to an agent via UI or code");
        $this->line("3. Test it: php artisan chatbot:test-tool {$slug}");

        return 0;
    }

    /**
     * Get the stub file content.
     */
    protected function getStub(): string
    {
        return <<<'STUB'
<?php

namespace App\Tools;

use LaravelAI\Chatbot\Tools\BaseTool;

class {{CLASS_NAME}} extends BaseTool
{
    /**
     * The display name of the tool.
     */
    public function name(): string
    {
        return '{{TOOL_NAME}}';
    }

    /**
     * Description of what the tool does.
     * This is shown to the AI model to help it decide when to use the tool.
     */
    public function description(): string
    {
        return '{{TOOL_DESCRIPTION}}';
    }

    /**
     * Define the parameters schema (JSON Schema format).
     */
    public function parameters(): array
    {
        return [
            'properties' => [
                'param1' => [
                    'type' => 'string',
                    'description' => 'Description of parameter 1',
                ],
            ],
            'required' => ['param1'],
        ];
    }

    /**
     * Execute the tool with the given arguments.
     * 
     * @param array $arguments The arguments passed from the AI
     * @return mixed The result of the tool execution
     */
    public function execute(array $arguments): mixed
    {
        // Validate required arguments
        $this->validateArguments($arguments, ['param1']);
        
        // Get arguments
        $param1 = $arguments['param1'];
        
        // Your tool logic here
        // Example: Database query, API call, calculation, etc.
        
        $result = [
            'success' => true,
            'data' => [
                'param1' => $param1,
                // Add your result data here
            ],
        ];
        
        return $result;
    }
}
STUB;
    }
}


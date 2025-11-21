<?php

namespace LaravelAI\Chatbot\Console\Commands;

use Illuminate\Console\Command;
use LaravelAI\Chatbot\Tools\ToolLoader;

class TestToolCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'chatbot:test-tool 
                            {slug : The slug of the tool to test}
                            {--args= : JSON string of arguments to pass}';

    /**
     * The console command description.
     */
    protected $description = 'Test a chatbot tool';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $slug = $this->argument('slug');
        $argsJson = $this->option('args') ?: '{}';
        
        $arguments = json_decode($argsJson, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error('Invalid JSON in --args option');
            return 1;
        }

        $this->info("Testing tool: {$slug}");
        $this->line("Arguments: " . json_encode($arguments, JSON_PRETTY_PRINT));
        $this->newLine();

        try {
            $tool = ToolLoader::getBySlug($slug);
            
            if (!$tool) {
                $this->error("Tool not found: {$slug}");
                $this->info("Available tools:");
                $tools = ToolLoader::discover();
                foreach ($tools as $t) {
                    $this->line("  - {$t->slug()} ({$t->name()})");
                }
                return 1;
            }

            $this->info("Tool found: {$tool->name()}");
            $this->line("Description: {$tool->description()}");
            $this->newLine();

            $this->info("Executing tool...");
            $result = ToolLoader::execute($slug, $arguments);
            
            $this->info("✅ Tool executed successfully!");
            $this->newLine();
            $this->line("Result:");
            $this->line(json_encode($result, JSON_PRETTY_PRINT));

            return 0;
        } catch (\Exception $e) {
            $this->error("❌ Tool execution failed!");
            $this->error("Error: {$e->getMessage()}");
            return 1;
        }
    }
}


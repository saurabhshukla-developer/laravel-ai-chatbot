<?php

namespace LaravelAI\Chatbot\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use LaravelAI\Chatbot\Tools\ToolLoader;

class ListToolsCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'chatbot:list-tools';

    /**
     * The console command description.
     */
    protected $description = 'List all available chatbot tools';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tools = ToolLoader::discover();

        if (empty($tools)) {
            $this->warn('No tools found!');
            $this->info('Create a tool with: php artisan chatbot:make-tool YourToolName');
            return 0;
        }

        $this->info("Found " . count($tools) . " tool(s):");
        $this->newLine();

        $headers = ['Name', 'Slug', 'Description'];
        $rows = [];

        foreach ($tools as $tool) {
            $rows[] = [
                $tool->name(),
                $tool->slug(),
                Str::limit($tool->description(), 50),
            ];
        }

        $this->table($headers, $rows);

        return 0;
    }
}


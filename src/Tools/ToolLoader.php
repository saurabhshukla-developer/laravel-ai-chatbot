<?php

namespace LaravelAI\Chatbot\Tools;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ToolLoader
{
    /**
     * Discover and load all tools from the tools directory.
     */
    public static function discover(): array
    {
        $toolsPath = self::getToolsPath();
        
        if (!File::exists($toolsPath)) {
            return [];
        }

        $tools = [];
        $files = File::allFiles($toolsPath);

        foreach ($files as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }

            $className = self::getClassNameFromFile($file->getPathname());
            
            if (!$className) {
                continue;
            }

            try {
                // Use Laravel's class loader
                if (!class_exists($className)) {
                    require_once $file->getPathname();
                }

                // Verify it's a valid tool class
                $reflection = new \ReflectionClass($className);
                
                if ($reflection->isSubclassOf(BaseTool::class) && !$reflection->isAbstract()) {
                    $toolInstance = new $className();
                    $tools[] = $toolInstance;
                }
            } catch (\Exception $e) {
                // Skip invalid tools (log in debug mode)
                if (config('app.debug')) {
                    \Log::debug("Skipping invalid tool file: {$file->getPathname()}", ['error' => $e->getMessage()]);
                }
                continue;
            }
        }

        return $tools;
    }

    /**
     * Get a tool by slug.
     */
    public static function getBySlug(string $slug): ?BaseTool
    {
        $tools = self::discover();
        
        foreach ($tools as $tool) {
            if ($tool->slug() === $slug) {
                return $tool;
            }
        }

        return null;
    }

    /**
     * Execute a tool by slug.
     */
    public static function execute(string $slug, array $arguments): mixed
    {
        $tool = self::getBySlug($slug);
        
        if (!$tool) {
            throw new \InvalidArgumentException("Tool not found: {$slug}");
        }

        return $tool->execute($arguments);
    }

    /**
     * Get the tools directory path.
     */
    protected static function getToolsPath(): string
    {
        $path = config('chatbot.tools_path', base_path('app/Tools'));
        
        // Support both absolute and relative paths
        if (!Str::startsWith($path, '/')) {
            $path = base_path($path);
        }

        return $path;
    }

    /**
     * Extract class name from PHP file.
     */
    protected static function getClassNameFromFile(string $filePath): ?string
    {
        $content = File::get($filePath);
        
        // Try to extract namespace and class name
        $namespace = null;
        $className = null;

        // Extract namespace
        if (preg_match('/namespace\s+([^;]+);/', $content, $matches)) {
            $namespace = trim($matches[1]);
        }

        // Extract class name (more flexible pattern)
        if (preg_match('/class\s+(\w+)(?:\s+extends\s+\w+)?/', $content, $matches)) {
            $className = $matches[1];
        }

        if ($className) {
            return $namespace ? "{$namespace}\\{$className}" : $className;
        }

        return null;
    }

    /**
     * Get formatted definitions for all discovered tools.
     */
    public static function getFormattedDefinitions(): array
    {
        $tools = self::discover();
        
        return array_map(function ($tool) {
            return $tool->getDefinition();
        }, $tools);
    }
}


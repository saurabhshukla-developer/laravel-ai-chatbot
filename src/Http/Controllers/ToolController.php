<?php

namespace LaravelAI\Chatbot\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use LaravelAI\Chatbot\Models\Tool;
use LaravelAI\Chatbot\Models\AiAgent;
use LaravelAI\Chatbot\Tools\ToolLoader;

class ToolController extends Controller
{
    /**
     * Display a listing of tools.
     */
    public function index()
    {
        $dbTools = Tool::orderBy('created_at', 'desc')->get();
        $fileTools = ToolLoader::discover();
        
        return view('chatbot::tools.index', compact('dbTools', 'fileTools'));
    }

    /**
     * Show the form for creating a new tool.
     */
    public function create()
    {
        return view('chatbot::tools.create');
    }

    /**
     * Store a newly created tool.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:chatbot_tools,slug',
            'description' => 'nullable|string',
            'type' => 'required|string|in:function,api,custom',
            'definition' => 'nullable|json',
            'implementation' => 'nullable|string',
            'config' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        // Parse JSON definition if provided as string
        if (isset($validated['definition']) && is_string($validated['definition'])) {
            $validated['definition'] = json_decode($validated['definition'], true);
        }

        Tool::create($validated);

        return redirect()->route('chatbot.tools.index')
            ->with('success', 'Tool created successfully.');
    }

    /**
     * Display the specified tool.
     */
    public function show(Tool $tool)
    {
        $agents = $tool->agents()->get();
        
        return view('chatbot::tools.show', compact('tool', 'agents'));
    }

    /**
     * Show the form for editing the specified tool.
     */
    public function edit(Tool $tool)
    {
        return view('chatbot::tools.edit', compact('tool'));
    }

    /**
     * Update the specified tool.
     */
    public function update(Request $request, Tool $tool)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:chatbot_tools,slug,' . $tool->id,
            'description' => 'nullable|string',
            'type' => 'required|string|in:function,api,custom',
            'definition' => 'nullable|json',
            'implementation' => 'nullable|string',
            'config' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        // Parse JSON definition if provided as string
        if (isset($validated['definition']) && is_string($validated['definition'])) {
            $validated['definition'] = json_decode($validated['definition'], true);
        }

        $tool->update($validated);

        return redirect()->route('chatbot.tools.index')
            ->with('success', 'Tool updated successfully.');
    }

    /**
     * Remove the specified tool.
     */
    public function destroy(Tool $tool)
    {
        $tool->delete();

        return redirect()->route('chatbot.tools.index')
            ->with('success', 'Tool deleted successfully.');
    }

    /**
     * Show tools folder information.
     */
    public function folderInfo()
    {
        try {
            $toolsPath = config('chatbot.tools_path', 'app/Tools');
            
            // Handle both relative and absolute paths
            if (str_starts_with($toolsPath, '/')) {
                $fullPath = $toolsPath;
            } else {
                $fullPath = base_path($toolsPath);
            }
            
            $exists = File::exists($fullPath);
            
            $files = [];
            if ($exists && File::isDirectory($fullPath)) {
                try {
                    $fileObjects = File::files($fullPath);
                    $files = array_map(function($file) {
                        return [
                            'name' => $file->getFilename(),
                            'path' => $file->getPathname(),
                            'size' => $file->getSize(),
                        ];
                    }, $fileObjects);
                } catch (\Exception $e) {
                    // Log error but continue
                    \Log::warning('Error reading tools folder', [
                        'path' => $fullPath,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            $exampleCode = '<?php

namespace App\Tools;

use LaravelAI\Chatbot\Tools\BaseTool;

class YourTool extends BaseTool
{
    public function name(): string { return \'Your Tool\'; }
    public function description(): string { return \'Tool description\'; }
    public function parameters(): array { /* ... */ }
    public function execute(array $arguments): mixed { /* ... */ }
}';

            return view('chatbot::tools.folder-info', compact('toolsPath', 'fullPath', 'exists', 'files', 'exampleCode'));
        } catch (\Exception $e) {
            \Log::error('Error in folderInfo', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return redirect()->route('chatbot.tools.index')
                ->with('error', 'Error loading folder information: ' . $e->getMessage());
        }
    }
}


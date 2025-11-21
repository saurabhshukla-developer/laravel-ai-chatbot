<?php

namespace LaravelAI\Chatbot\Http\Controllers;

use Illuminate\Http\Request;
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
}


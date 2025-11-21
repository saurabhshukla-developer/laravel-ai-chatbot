<?php

namespace LaravelAI\Chatbot\Http\Controllers;

use Illuminate\Http\Request;
use LaravelAI\Chatbot\Models\AiAgent;
use LaravelAI\Chatbot\Models\Tool;
use LaravelAI\Chatbot\Facades\Chatbot;
use LaravelAI\Chatbot\Tools\ToolLoader;

class AiAgentController extends Controller
{
    /**
     * Display a listing of AI agents.
     */
    public function index()
    {
        $agents = AiAgent::orderBy('created_at', 'desc')->get();
        
        return view('chatbot::agents.index', compact('agents'));
    }

    /**
     * Show the form for creating a new AI agent.
     */
    public function create()
    {
        $providers = config('chatbot.providers', []);
        $dbTools = Tool::active()->orderBy('name')->get();
        $fileTools = ToolLoader::discover();
        
        return view('chatbot::agents.create', compact('providers', 'dbTools', 'fileTools'));
    }

    /**
     * Store a newly created AI agent.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:chatbot_ai_agents,slug',
            'description' => 'nullable|string',
            'provider' => 'required|string',
            'model' => 'nullable|string',
            'system_prompt' => 'nullable|string',
            'config' => 'nullable|array',
            'tools' => 'nullable|array',
            'tool_ids' => 'nullable|array',
            'tool_ids.*' => 'exists:chatbot_tools,id',
            'file_tool_slugs' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        // Handle file-based tools
        $config = $validated['config'] ?? [];
        if ($request->has('file_tool_slugs')) {
            $config['file_tools'] = $request->file_tool_slugs;
        }
        $validated['config'] = $config;

        $agent = Chatbot::createAgent($validated);

        // Sync database tools if provided
        if ($request->has('tool_ids')) {
            $agent->tools()->sync($request->tool_ids);
        }

        return redirect()->route('chatbot.agents.index')
            ->with('success', 'AI agent created successfully.');
    }

    /**
     * Display the specified AI agent.
     */
    public function show(AiAgent $agent)
    {
        return view('chatbot::agents.show', compact('agent'));
    }

    /**
     * Show the form for editing the specified AI agent.
     */
    public function edit(AiAgent $agent)
    {
        $providers = config('chatbot.providers', []);
        $dbTools = Tool::active()->orderBy('name')->get();
        $fileTools = ToolLoader::discover();
        
        return view('chatbot::agents.edit', compact('agent', 'providers', 'dbTools', 'fileTools'));
    }

    /**
     * Update the specified AI agent.
     */
    public function update(Request $request, AiAgent $agent)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:chatbot_ai_agents,slug,' . $agent->id,
            'description' => 'nullable|string',
            'provider' => 'required|string',
            'model' => 'nullable|string',
            'system_prompt' => 'nullable|string',
            'config' => 'nullable|array',
            'tools' => 'nullable|array',
            'tool_ids' => 'nullable|array',
            'tool_ids.*' => 'exists:chatbot_tools,id',
            'file_tool_slugs' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        // Handle file-based tools
        $config = $validated['config'] ?? $agent->config ?? [];
        if ($request->has('file_tool_slugs')) {
            $config['file_tools'] = $request->file_tool_slugs;
        } else {
            $config['file_tools'] = [];
        }
        $validated['config'] = $config;

        $agent->update($validated);

        // Sync database tools if provided
        if ($request->has('tool_ids')) {
            $agent->tools()->sync($request->tool_ids);
        } else {
            // If tool_ids is not provided, clear all database tools
            $agent->tools()->sync([]);
        }

        return redirect()->route('chatbot.agents.index')
            ->with('success', 'AI agent updated successfully.');
    }

    /**
     * Remove the specified AI agent.
     */
    public function destroy(AiAgent $agent)
    {
        $agent->delete();

        return redirect()->route('chatbot.agents.index')
            ->with('success', 'AI agent deleted successfully.');
    }

    /**
     * Chat with an AI agent.
     */
    public function chat(Request $request, AiAgent $agent)
    {
        $validated = $request->validate([
            'message' => 'required|string',
            'options' => 'nullable|array',
        ]);

        try {
            $response = Chatbot::chat($agent, $validated['message'], $validated['options'] ?? []);
            
            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}


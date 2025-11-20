<?php

namespace LaravelAI\Chatbot\Http\Controllers;

use Illuminate\Http\Request;
use LaravelAI\Chatbot\Models\ApiKey;
use Illuminate\Support\Facades\DB;

class ApiKeyController extends Controller
{
    /**
     * Display a listing of API keys.
     */
    public function index()
    {
        $apiKeys = ApiKey::orderBy('provider')->orderBy('created_at', 'desc')->get();
        
        return view('chatbot::api-keys.index', compact('apiKeys'));
    }

    /**
     * Show the form for creating a new API key.
     */
    public function create()
    {
        $providers = config('chatbot.providers', []);
        
        return view('chatbot::api-keys.create', compact('providers'));
    }

    /**
     * Store a newly created API key.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'provider' => 'required|string',
            'name' => 'nullable|string|max:255',
            'api_key' => 'required|string',
            'api_secret' => 'nullable|string',
            'is_default' => 'boolean',
        ]);

        // If this is set as default, unset other defaults for this provider
        if ($request->boolean('is_default')) {
            ApiKey::where('provider', $validated['provider'])
                ->update(['is_default' => false]);
        }

        ApiKey::create($validated);

        return redirect()->route('chatbot.api-keys.index')
            ->with('success', 'API key created successfully.');
    }

    /**
     * Show the form for editing the specified API key.
     */
    public function edit(ApiKey $apiKey)
    {
        $providers = config('chatbot.providers', []);
        
        return view('chatbot::api-keys.edit', compact('apiKey', 'providers'));
    }

    /**
     * Update the specified API key.
     */
    public function update(Request $request, ApiKey $apiKey)
    {
        $validated = $request->validate([
            'provider' => 'required|string',
            'name' => 'nullable|string|max:255',
            'api_key' => 'required|string',
            'api_secret' => 'nullable|string',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
        ]);

        // If this is set as default, unset other defaults for this provider
        if ($request->boolean('is_default')) {
            ApiKey::where('provider', $validated['provider'])
                ->where('id', '!=', $apiKey->id)
                ->update(['is_default' => false]);
        }

        $apiKey->update($validated);

        return redirect()->route('chatbot.api-keys.index')
            ->with('success', 'API key updated successfully.');
    }

    /**
     * Remove the specified API key.
     */
    public function destroy(ApiKey $apiKey)
    {
        $apiKey->delete();

        return redirect()->route('chatbot.api-keys.index')
            ->with('success', 'API key deleted successfully.');
    }
}


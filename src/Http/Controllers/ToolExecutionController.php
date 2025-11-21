<?php

namespace LaravelAI\Chatbot\Http\Controllers;

use Illuminate\Http\Request;
use LaravelAI\Chatbot\Tools\ToolLoader;

class ToolExecutionController extends Controller
{
    /**
     * Execute a tool.
     */
    public function execute(Request $request, string $toolSlug)
    {
        $validated = $request->validate([
            'arguments' => 'required|array',
        ]);

        try {
            $result = ToolLoader::execute($toolSlug, $validated['arguments']);
            
            return response()->json([
                'success' => true,
                'result' => $result,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}


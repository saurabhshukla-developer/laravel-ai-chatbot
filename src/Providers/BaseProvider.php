<?php

namespace LaravelAI\Chatbot\Providers;

use LaravelAI\Chatbot\Models\ApiKey;
use LaravelAI\Chatbot\Models\AiAgent;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;

abstract class BaseProvider implements ProviderInterface
{
    protected ApiKey $apiKey;
    protected array $config;

    public function __construct(?ApiKey $apiKey = null)
    {
        $this->apiKey = $apiKey ?? new ApiKey();
        $this->config = Config::get('chatbot.providers.' . $this->getProviderName(), []);
    }

    /**
     * Get the provider name.
     */
    abstract protected function getProviderName(): string;

    /**
     * Get the API key.
     */
    protected function getApiKey(): string
    {
        if ($this->apiKey->exists) {
            return $this->apiKey->getDecryptedApiKey();
        }

        $envKey = strtoupper($this->getProviderName()) . '_API_KEY';
        return env($envKey, '');
    }

    /**
     * Get the API URL.
     */
    protected function getApiUrl(): string
    {
        return $this->config['api_url'] ?? '';
    }

    /**
     * Get the default model.
     */
    protected function getDefaultModel(): string
    {
        return $this->config['model'] ?? '';
    }

    /**
     * Build the request headers.
     */
    abstract protected function buildHeaders(): array;

    /**
     * Build the request payload.
     */
    protected function buildPayload(AiAgent $agent, string $message, array $options = [], array $existingMessages = []): array
    {
        // Default implementation - override in providers if needed
        $messages = !empty($existingMessages) ? $existingMessages : [];

        if (empty($existingMessages)) {
            if ($agent->system_prompt) {
                $messages[] = [
                    'role' => 'system',
                    'content' => $agent->system_prompt,
                ];
            }

            $messages[] = [
                'role' => 'user',
                'content' => $message,
            ];
        }

        return [
            'messages' => $messages,
        ];
    }

    /**
     * Parse the response.
     */
    abstract protected function parseResponse(array $response): array;

    /**
     * Send a chat message.
     */
    public function chat(AiAgent $agent, string $message, array $options = []): array
    {
        $headers = $this->buildHeaders();
        $messages = [];

        // Handle tool calls in a loop (max 5 iterations to prevent infinite loops)
        $maxIterations = 5;
        $iteration = 0;
        $lastResponse = null;

        while ($iteration < $maxIterations) {
            // Build payload with current messages
            $payload = $this->buildPayload($agent, $message, $options, $messages);
            
            $response = Http::withHeaders($headers)
                ->post($this->getChatEndpoint(), $payload);

            if (!$response->successful()) {
                throw new \Exception('API request failed: ' . $response->body());
            }

            $responseData = $response->json();
            $lastResponse = $responseData;
            
            // Check if AI wants to call a tool
            $toolCalls = $this->extractToolCalls($responseData);
            
            if (empty($toolCalls)) {
                // No tool calls, return the final response
                return $this->parseResponse($responseData);
            }

            // Execute tools and add results to messages
            $assistantMessage = $this->getAssistantMessage($responseData);
            if (!empty($assistantMessage)) {
                $messages[] = $assistantMessage;
            }
            
            foreach ($toolCalls as $toolCall) {
                try {
                    $toolResult = $this->executeTool($agent, $toolCall);
                    $toolMessage = $this->buildToolMessage($toolCall, $toolResult);
                    if ($toolMessage) {
                        $messages[] = $toolMessage;
                    }
                } catch (\Exception $e) {
                    // If tool execution fails, send error back
                    $errorMessage = $this->buildToolMessage($toolCall, [
                        'error' => $e->getMessage()
                    ]);
                    if ($errorMessage) {
                        $messages[] = $errorMessage;
                    }
                }
            }

            $iteration++;
        }

        // If we've reached max iterations, return the last response
        return $this->parseResponse($lastResponse ?? []);
    }

    /**
     * Extract tool calls from response.
     */
    protected function extractToolCalls(array $response): array
    {
        // Override in provider-specific implementations
        return [];
    }

    /**
     * Get assistant message from response.
     */
    protected function getAssistantMessage(array $response): array
    {
        // Override in provider-specific implementations
        return [];
    }

    /**
     * Build tool message for sending back to AI.
     */
    protected function buildToolMessage(array $toolCall, mixed $result): ?array
    {
        // Override in provider-specific implementations
        $content = is_string($result) ? $result : json_encode($result);
        return [
            'role' => 'tool',
            'tool_call_id' => $toolCall['id'] ?? null,
            'content' => $content,
        ];
    }

    /**
     * Execute a tool call.
     */
    protected function executeTool(AiAgent $agent, array $toolCall): mixed
    {
        $toolName = $this->getToolNameFromCall($toolCall);
        $arguments = $this->getToolArgumentsFromCall($toolCall);

        if (!$toolName) {
            throw new \Exception('Tool name not found in tool call');
        }

        // Try to execute file-based tool
        try {
            $tool = \LaravelAI\Chatbot\Tools\ToolLoader::getBySlug($toolName);
            if ($tool) {
                return $tool->execute($arguments);
            }
        } catch (\Exception $e) {
            // Tool not found, try database tool
        }

        // Try to execute database tool
        $dbTool = \LaravelAI\Chatbot\Models\Tool::where('slug', $toolName)->first();
        if ($dbTool) {
            // For database tools with implementation, you might execute it
            // For now, return a placeholder
            return ['error' => 'Database tool execution requires custom implementation'];
        }

        throw new \Exception("Tool not found: {$toolName}");
    }

    /**
     * Get tool name from tool call.
     */
    protected function getToolNameFromCall(array $toolCall): ?string
    {
        return $toolCall['function']['name'] ?? $toolCall['name'] ?? null;
    }

    /**
     * Get tool arguments from tool call.
     */
    protected function getToolArgumentsFromCall(array $toolCall): array
    {
        $arguments = $toolCall['function']['arguments'] ?? $toolCall['input'] ?? '{}';
        
        if (is_string($arguments)) {
            return json_decode($arguments, true) ?? [];
        }
        
        return is_array($arguments) ? $arguments : [];
    }

    /**
     * Stream a chat message.
     */
    public function streamChat(AiAgent $agent, string $message, array $options = []): \Generator
    {
        $payload = $this->buildPayload($agent, $message, $options);
        $payload['stream'] = true;
        $headers = $this->buildHeaders();

        $response = Http::withHeaders($headers)
            ->withBody(json_encode($payload), 'application/json')
            ->post($this->getChatEndpoint());

        if (!$response->successful()) {
            throw new \Exception('API request failed: ' . $response->body());
        }

        foreach ($this->parseStreamResponse($response->body()) as $chunk) {
            yield $chunk;
        }
    }

    /**
     * Get the chat endpoint URL.
     */
    abstract protected function getChatEndpoint(): string;

    /**
     * Parse stream response.
     */
    abstract protected function parseStreamResponse(string $body): \Generator;
}


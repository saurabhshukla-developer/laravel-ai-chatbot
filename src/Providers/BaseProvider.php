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
    abstract protected function buildPayload(AiAgent $agent, string $message, array $options = []): array;

    /**
     * Parse the response.
     */
    abstract protected function parseResponse(array $response): array;

    /**
     * Send a chat message.
     */
    public function chat(AiAgent $agent, string $message, array $options = []): array
    {
        $payload = $this->buildPayload($agent, $message, $options);
        $headers = $this->buildHeaders();

        $response = Http::withHeaders($headers)
            ->post($this->getChatEndpoint(), $payload);

        if (!$response->successful()) {
            throw new \Exception('API request failed: ' . $response->body());
        }

        return $this->parseResponse($response->json());
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


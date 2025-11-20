<?php

namespace LaravelAI\Chatbot;

use LaravelAI\Chatbot\Models\ApiKey;
use LaravelAI\Chatbot\Models\AiAgent;
use LaravelAI\Chatbot\Providers\ProviderInterface;
use LaravelAI\Chatbot\Providers\OpenAIProvider;
use LaravelAI\Chatbot\Providers\AnthropicProvider;
use LaravelAI\Chatbot\Providers\GoogleProvider;
use Illuminate\Support\Facades\Config;

class ChatbotManager
{
    protected $app;
    protected $providers = [];

    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * Get a provider instance.
     */
    public function provider(string $provider = null): ProviderInterface
    {
        $provider = $provider ?? Config::get('chatbot.default_provider');

        if (!isset($this->providers[$provider])) {
            $this->providers[$provider] = $this->createProvider($provider);
        }

        return $this->providers[$provider];
    }

    /**
     * Create a provider instance.
     */
    protected function createProvider(string $provider): ProviderInterface
    {
        $apiKey = $this->getApiKeyForProvider($provider);

        return match ($provider) {
            'openai' => new OpenAIProvider($apiKey),
            'anthropic' => new AnthropicProvider($apiKey),
            'google' => new GoogleProvider($apiKey),
            default => throw new \InvalidArgumentException("Provider [{$provider}] is not supported."),
        };
    }

    /**
     * Get API key for provider.
     */
    protected function getApiKeyForProvider(string $provider): ?ApiKey
    {
        $storageDriver = Config::get('chatbot.storage_driver', 'database');

        if ($storageDriver === 'database') {
            return ApiKey::defaultForProvider($provider)->first();
        }

        // Fallback to config/env
        $envKey = strtoupper($provider) . '_API_KEY';
        if ($apiKey = env($envKey)) {
            $model = new ApiKey();
            $model->provider = $provider;
            $model->api_key = $apiKey;
            return $model;
        }

        return null;
    }

    /**
     * Create a new AI agent.
     */
    public function createAgent(array $attributes): AiAgent
    {
        return AiAgent::create($attributes);
    }

    /**
     * Get an AI agent by slug or ID.
     */
    public function getAgent($identifier): ?AiAgent
    {
        if (is_numeric($identifier)) {
            return AiAgent::find($identifier);
        }

        return AiAgent::where('slug', $identifier)->first();
    }

    /**
     * Chat with an AI agent.
     */
    public function chat($agent, string $message, array $options = []): array
    {
        if (is_string($agent) || is_numeric($agent)) {
            $agent = $this->getAgent($agent);
        }

        if (!$agent instanceof AiAgent) {
            throw new \InvalidArgumentException('Invalid agent provided.');
        }

        $provider = $this->provider($agent->provider);

        return $provider->chat($agent, $message, $options);
    }

    /**
     * Stream chat with an AI agent.
     */
    public function streamChat($agent, string $message, array $options = []): \Generator
    {
        if (is_string($agent) || is_numeric($agent)) {
            $agent = $this->getAgent($agent);
        }

        if (!$agent instanceof AiAgent) {
            throw new \InvalidArgumentException('Invalid agent provided.');
        }

        $provider = $this->provider($agent->provider);

        return $provider->streamChat($agent, $message, $options);
    }
}


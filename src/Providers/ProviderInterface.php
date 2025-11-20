<?php

namespace LaravelAI\Chatbot\Providers;

use LaravelAI\Chatbot\Models\AiAgent;

interface ProviderInterface
{
    /**
     * Send a chat message to the AI agent.
     */
    public function chat(AiAgent $agent, string $message, array $options = []): array;

    /**
     * Stream a chat message to the AI agent.
     */
    public function streamChat(AiAgent $agent, string $message, array $options = []): \Generator;
}


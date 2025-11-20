<?php

namespace LaravelAI\Chatbot\Providers;

use LaravelAI\Chatbot\Models\AiAgent;

class GoogleProvider extends BaseProvider
{
    protected function getProviderName(): string
    {
        return 'google';
    }

    protected function buildHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
        ];
    }

    protected function buildPayload(AiAgent $agent, string $message, array $options = []): array
    {
        $contents = [];

        if ($agent->system_prompt) {
            $contents[] = [
                'role' => 'user',
                'parts' => [['text' => $agent->system_prompt]],
            ];
            $contents[] = [
                'role' => 'model',
                'parts' => [['text' => 'I understand.']],
            ];
        }

        $contents[] = [
            'role' => 'user',
            'parts' => [['text' => $message]],
        ];

        $payload = [
            'contents' => $contents,
            'generationConfig' => [
                'temperature' => $options['temperature'] ?? $this->config['temperature'] ?? 0.7,
                'maxOutputTokens' => $options['max_tokens'] ?? $this->config['max_tokens'] ?? 2048,
            ],
        ];

        return $payload;
    }

    protected function parseResponse(array $response): array
    {
        return [
            'content' => $response['candidates'][0]['content']['parts'][0]['text'] ?? '',
            'model' => $this->getDefaultModel(),
            'usage' => $response['usageMetadata'] ?? [],
        ];
    }

    protected function getChatEndpoint(): string
    {
        $model = $this->getDefaultModel();
        $apiKey = $this->getApiKey();
        return $this->getApiUrl() . "/models/{$model}:generateContent?key={$apiKey}";
    }

    protected function parseStreamResponse(string $body): \Generator
    {
        // Google AI streaming implementation
        $lines = explode("\n", $body);
        
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }

            $json = json_decode($line, true);
            if (isset($json['candidates'][0]['content']['parts'][0]['text'])) {
                yield [
                    'content' => $json['candidates'][0]['content']['parts'][0]['text'],
                    'done' => false,
                ];
            }
        }

        yield ['content' => '', 'done' => true];
    }
}


<?php

namespace LaravelAI\Chatbot\Providers;

use LaravelAI\Chatbot\Models\AiAgent;

class AnthropicProvider extends BaseProvider
{
    protected function getProviderName(): string
    {
        return 'anthropic';
    }

    protected function buildHeaders(): array
    {
        return [
            'x-api-key' => $this->getApiKey(),
            'anthropic-version' => '2023-06-01',
            'Content-Type' => 'application/json',
        ];
    }

    protected function buildPayload(AiAgent $agent, string $message, array $options = []): array
    {
        $payload = [
            'model' => $options['model'] ?? $agent->model ?? $this->getDefaultModel(),
            'max_tokens' => $options['max_tokens'] ?? $this->config['max_tokens'] ?? 4096,
            'temperature' => $options['temperature'] ?? $this->config['temperature'] ?? 0.7,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $message,
                ],
            ],
        ];

        if ($agent->system_prompt) {
            $payload['system'] = $agent->system_prompt;
        }

        return $payload;
    }

    protected function parseResponse(array $response): array
    {
        return [
            'content' => $response['content'][0]['text'] ?? '',
            'model' => $response['model'] ?? '',
            'usage' => $response['usage'] ?? [],
        ];
    }

    protected function getChatEndpoint(): string
    {
        return $this->getApiUrl() . '/messages';
    }

    protected function parseStreamResponse(string $body): \Generator
    {
        $lines = explode("\n", $body);
        
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line) || !str_starts_with($line, 'data: ')) {
                continue;
            }

            $data = substr($line, 6);
            $json = json_decode($data, true);
            
            if (isset($json['type']) && $json['type'] === 'content_block_delta') {
                yield [
                    'content' => $json['delta']['text'] ?? '',
                    'done' => false,
                ];
            } elseif (isset($json['type']) && $json['type'] === 'message_stop') {
                yield ['content' => '', 'done' => true];
                break;
            }
        }
    }
}


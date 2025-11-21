<?php

namespace LaravelAI\Chatbot\Providers;

use LaravelAI\Chatbot\Models\AiAgent;

class OpenAIProvider extends BaseProvider
{
    protected function getProviderName(): string
    {
        return 'openai';
    }

    protected function buildHeaders(): array
    {
        return [
            'Authorization' => 'Bearer ' . $this->getApiKey(),
            'Content-Type' => 'application/json',
        ];
    }

    protected function buildPayload(AiAgent $agent, string $message, array $options = [], array $existingMessages = []): array
    {
        $messages = !empty($existingMessages) ? $existingMessages : [];

        // Only add system and user message if this is the first request
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

        $payload = [
            'model' => $options['model'] ?? $agent->model ?? $this->getDefaultModel(),
            'messages' => $messages,
            'temperature' => $options['temperature'] ?? $this->config['temperature'] ?? 0.7,
            'max_tokens' => $options['max_tokens'] ?? $this->config['max_tokens'] ?? 2000,
        ];

        // Add tools if agent has any
        $tools = $agent->getFormattedTools();
        if (!empty($tools)) {
            $payload['tools'] = $tools;
        }

        if (isset($options['stream'])) {
            $payload['stream'] = $options['stream'];
        }

        return $payload;
    }

    protected function parseResponse(array $response): array
    {
        $message = $response['choices'][0]['message'] ?? [];
        
        return [
            'content' => $message['content'] ?? '',
            'model' => $response['model'] ?? '',
            'usage' => $response['usage'] ?? [],
            'tool_calls' => $message['tool_calls'] ?? [],
        ];
    }

    /**
     * Extract tool calls from OpenAI response.
     */
    protected function extractToolCalls(array $response): array
    {
        $message = $response['choices'][0]['message'] ?? [];
        return $message['tool_calls'] ?? [];
    }

    /**
     * Get assistant message from OpenAI response.
     */
    protected function getAssistantMessage(array $response): array
    {
        $message = $response['choices'][0]['message'] ?? [];
        $result = [
            'role' => 'assistant',
        ];
        
        if (!empty($message['content'])) {
            $result['content'] = $message['content'];
        }
        
        if (!empty($message['tool_calls'])) {
            $result['tool_calls'] = $message['tool_calls'];
        }
        
        return $result;
    }

    /**
     * Build tool message for OpenAI.
     */
    protected function buildToolMessage(array $toolCall, mixed $result): array
    {
        $toolCallId = $toolCall['id'] ?? null;
        $content = is_string($result) ? $result : json_encode($result);
        
        return [
            'role' => 'tool',
            'tool_call_id' => $toolCallId,
            'content' => $content,
        ];
    }

    protected function getChatEndpoint(): string
    {
        return $this->getApiUrl() . '/chat/completions';
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
            if ($data === '[DONE]') {
                break;
            }

            $json = json_decode($data, true);
            if (isset($json['choices'][0]['delta']['content'])) {
                yield [
                    'content' => $json['choices'][0]['delta']['content'],
                    'done' => false,
                ];
            }
        }

        yield ['content' => '', 'done' => true];
    }
}


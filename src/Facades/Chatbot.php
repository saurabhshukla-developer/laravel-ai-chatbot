<?php

namespace LaravelAI\Chatbot\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \LaravelAI\Chatbot\Providers\ProviderInterface provider(string $provider = null)
 * @method static \LaravelAI\Chatbot\Models\AiAgent createAgent(array $attributes)
 * @method static \LaravelAI\Chatbot\Models\AiAgent|null getAgent($identifier)
 * @method static array chat($agent, string $message, array $options = [])
 * @method static \Generator streamChat($agent, string $message, array $options = [])
 */
class Chatbot extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'chatbot';
    }
}


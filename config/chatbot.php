<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default AI Provider
    |--------------------------------------------------------------------------
    |
    | This option controls the default AI provider that will be used when
    | no specific provider is requested.
    |
    */

    'default_provider' => env('CHATBOT_DEFAULT_PROVIDER', 'openai'),

    /*
    |--------------------------------------------------------------------------
    | AI Providers Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure the settings for various AI providers.
    | API keys can be stored in the database or environment variables.
    |
    */

    'providers' => [
        'openai' => [
            'name' => 'OpenAI',
            'api_url' => env('OPENAI_API_URL', 'https://api.openai.com/v1'),
            'model' => env('OPENAI_MODEL', 'gpt-4'),
            'max_tokens' => env('OPENAI_MAX_TOKENS', 2000),
            'temperature' => env('OPENAI_TEMPERATURE', 0.7),
        ],
        'anthropic' => [
            'name' => 'Anthropic',
            'api_url' => env('ANTHROPIC_API_URL', 'https://api.anthropic.com/v1'),
            'model' => env('ANTHROPIC_MODEL', 'claude-3-opus-20240229'),
            'max_tokens' => env('ANTHROPIC_MAX_TOKENS', 4096),
            'temperature' => env('ANTHROPIC_TEMPERATURE', 0.7),
        ],
        'google' => [
            'name' => 'Google AI',
            'api_url' => env('GOOGLE_AI_API_URL', 'https://generativelanguage.googleapis.com/v1'),
            'model' => env('GOOGLE_AI_MODEL', 'gemini-pro'),
            'max_tokens' => env('GOOGLE_AI_MAX_TOKENS', 2048),
            'temperature' => env('GOOGLE_AI_TEMPERATURE', 0.7),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Storage Driver
    |--------------------------------------------------------------------------
    |
    | This option determines where API keys are stored.
    | Options: 'database', 'config'
    |
    */

    'storage_driver' => env('CHATBOT_STORAGE_DRIVER', 'database'),

    /*
    |--------------------------------------------------------------------------
    | Route Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for package routes.
    |
    */

    'routes' => [
        'prefix' => env('CHATBOT_ROUTE_PREFIX', 'chatbot'),
        'middleware' => ['web'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Tools Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for file-based tools.
    |
    */

    'tools_path' => env('CHATBOT_TOOLS_PATH', 'app/Tools'),
];


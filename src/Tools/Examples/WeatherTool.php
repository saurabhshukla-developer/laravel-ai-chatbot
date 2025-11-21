<?php

namespace LaravelAI\Chatbot\Tools\Examples;

use LaravelAI\Chatbot\Tools\BaseTool;

class WeatherTool extends BaseTool
{
    public function name(): string
    {
        return 'Get Weather';
    }

    public function description(): string
    {
        return 'Gets the current weather conditions for a specific location.';
    }

    public function parameters(): array
    {
        return [
            'properties' => [
                'location' => [
                    'type' => 'string',
                    'description' => 'The city and state/country, e.g. "San Francisco, CA" or "London, UK"',
                ],
                'unit' => [
                    'type' => 'string',
                    'enum' => ['celsius', 'fahrenheit'],
                    'description' => 'The unit of temperature (default: celsius)',
                    'default' => 'celsius',
                ],
            ],
            'required' => ['location'],
        ];
    }

    public function execute(array $arguments): mixed
    {
        $this->validateArguments($arguments, ['location']);

        $location = $arguments['location'];
        $unit = $arguments['unit'] ?? 'celsius';

        // Example implementation - in production, you would call a weather API
        // This is just a mock response
        return [
            'success' => true,
            'location' => $location,
            'temperature' => $unit === 'celsius' ? '22°C' : '72°F',
            'condition' => 'Partly Cloudy',
            'humidity' => '65%',
            'wind' => '10 km/h',
            'note' => 'This is a mock response. Integrate with a real weather API for production use.',
        ];
    }
}


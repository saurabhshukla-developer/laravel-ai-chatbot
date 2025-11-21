<?php

namespace App\Tools;

use LaravelAI\Chatbot\Tools\BaseTool;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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

        // Option 1: Mock response (for testing without API key)
        // Remove this and use Option 2 for real API integration
        return [
            'success' => true,
            'location' => $location,
            'temperature' => $unit === 'celsius' ? '22°C' : '72°F',
            'condition' => 'Partly Cloudy',
            'humidity' => '65%',
            'wind' => '10 km/h',
            'description' => "Current weather in {$location}: Partly Cloudy, 22°C, Humidity 65%",
        ];

        // Option 2: Real API integration (uncomment and configure)
        /*
        try {
            $apiKey = config('services.weather.api_key'); // Add to config/services.php
            
            if (!$apiKey) {
                return [
                    'success' => false,
                    'error' => 'Weather API key not configured',
                    'location' => $location,
                ];
            }

            // Example using OpenWeatherMap API
            $response = Http::get('https://api.openweathermap.org/data/2.5/weather', [
                'q' => $location,
                'appid' => $apiKey,
                'units' => $unit === 'celsius' ? 'metric' : 'imperial',
            ]);

            if (!$response->successful()) {
                return [
                    'success' => false,
                    'error' => 'Failed to fetch weather data',
                    'location' => $location,
                ];
            }

            $data = $response->json();
            
            return [
                'success' => true,
                'location' => $location,
                'temperature' => $data['main']['temp'] . ($unit === 'celsius' ? '°C' : '°F'),
                'condition' => $data['weather'][0]['main'],
                'description' => $data['weather'][0]['description'],
                'humidity' => $data['main']['humidity'] . '%',
                'wind' => ($data['wind']['speed'] ?? 0) . ' m/s',
            ];
        } catch (\Exception $e) {
            Log::error('Weather tool error', [
                'location' => $location,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'An error occurred while fetching weather data',
                'location' => $location,
            ];
        }
        */
    }
}


<?php

namespace LaravelAI\Chatbot\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use LaravelAI\Chatbot\ChatbotServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Run migrations
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }

    protected function getPackageProviders($app)
    {
        return [
            ChatbotServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        // Set app key for encryption
        $app['config']->set('app.key', 'base64:' . base64_encode(
            \Illuminate\Support\Str::random(32)
        ));
    }
}


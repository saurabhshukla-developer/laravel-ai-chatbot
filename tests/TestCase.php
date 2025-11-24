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
        // Get database connection from environment or default to sqlite
        $connection = env('DB_CONNECTION', 'sqlite');

        if ($connection === 'mysql') {
            // Setup MySQL database for testing
            $app['config']->set('database.default', 'mysql');
            $app['config']->set('database.connections.mysql', [
                'driver' => 'mysql',
                'host' => env('DB_HOST', '127.0.0.1'),
                'port' => env('DB_PORT', '3306'),
                'database' => env('DB_DATABASE', 'chatbot_test'),
                'username' => env('DB_USERNAME', 'root'),
                'password' => env('DB_PASSWORD', ''),
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix' => '',
                'strict' => true,
                'engine' => null,
            ]);
        } else {
            // Setup default database to use sqlite :memory:
            $app['config']->set('database.default', 'sqlite');
            $app['config']->set('database.connections.sqlite', [
                'driver' => 'sqlite',
                'database' => env('DB_DATABASE', ':memory:'),
                'prefix' => '',
            ]);
        }

        // prefer env value if non-empty, otherwise generate one
        $appKey = env('APP_KEY') ?: 'base64:' . base64_encode(\Illuminate\Support\Str::random(32));
        // Set app key for encryption (32 characters base64 encoded)
        $app['config']->set('app.key', $appKey);
        // Ensure cipher is set
        $app['config']->set('app.cipher', 'AES-256-CBC');
    }
}

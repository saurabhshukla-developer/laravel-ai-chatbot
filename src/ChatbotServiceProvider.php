<?php

namespace LaravelAI\Chatbot;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class ChatbotServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/chatbot.php',
            'chatbot'
        );

        $this->app->singleton('chatbot', function ($app) {
            return new ChatbotManager($app);
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'chatbot');
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        if ($this->app->runningInConsole()) {
            $this->commands([
                \LaravelAI\Chatbot\Console\Commands\MakeToolCommand::class,
                \LaravelAI\Chatbot\Console\Commands\TestToolCommand::class,
                \LaravelAI\Chatbot\Console\Commands\ListToolsCommand::class,
            ]);

            $this->publishes([
                __DIR__.'/../config/chatbot.php' => config_path('chatbot.php'),
            ], 'chatbot-config');

            $this->publishes([
                __DIR__.'/../database/migrations' => database_path('migrations'),
            ], 'chatbot-migrations');

            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/chatbot'),
            ], 'chatbot-views');
        }
    }
}


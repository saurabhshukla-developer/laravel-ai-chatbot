<?php

use Illuminate\Support\Facades\Route;
use LaravelAI\Chatbot\Http\Controllers\ApiKeyController;
use LaravelAI\Chatbot\Http\Controllers\AiAgentController;

$prefix = config('chatbot.routes.prefix', 'chatbot');
$middleware = config('chatbot.routes.middleware', ['web']);

Route::prefix($prefix)->middleware($middleware)->group(function () {
    // API Keys Routes
    Route::resource('api-keys', ApiKeyController::class)->names([
        'index' => 'chatbot.api-keys.index',
        'create' => 'chatbot.api-keys.create',
        'store' => 'chatbot.api-keys.store',
        'show' => 'chatbot.api-keys.show',
        'edit' => 'chatbot.api-keys.edit',
        'update' => 'chatbot.api-keys.update',
        'destroy' => 'chatbot.api-keys.destroy',
    ]);

    // AI Agents Routes
    Route::resource('agents', AiAgentController::class)->names([
        'index' => 'chatbot.agents.index',
        'create' => 'chatbot.agents.create',
        'store' => 'chatbot.agents.store',
        'show' => 'chatbot.agents.show',
        'edit' => 'chatbot.agents.edit',
        'update' => 'chatbot.agents.update',
        'destroy' => 'chatbot.agents.destroy',
    ]);

    // Chat endpoint
    Route::post('agents/{agent}/chat', [AiAgentController::class, 'chat'])->name('chatbot.agents.chat');
});


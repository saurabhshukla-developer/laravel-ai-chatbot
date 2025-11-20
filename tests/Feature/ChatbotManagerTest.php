<?php

namespace LaravelAI\Chatbot\Tests\Feature;

use LaravelAI\Chatbot\Facades\Chatbot;
use LaravelAI\Chatbot\Models\ApiKey;
use LaravelAI\Chatbot\Models\AiAgent;
use LaravelAI\Chatbot\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ChatbotManagerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_agent()
    {
        $agent = Chatbot::createAgent([
            'name' => 'Test Agent',
            'slug' => 'test-agent',
            'provider' => 'openai',
            'is_active' => true,
        ]);

        $this->assertInstanceOf(AiAgent::class, $agent);
        $this->assertEquals('Test Agent', $agent->name);
    }

    public function test_can_get_agent_by_slug()
    {
        $agent = AiAgent::create([
            'name' => 'Test Agent',
            'slug' => 'test-agent',
            'provider' => 'openai',
        ]);

        $retrieved = Chatbot::getAgent('test-agent');
        $this->assertNotNull($retrieved);
        $this->assertEquals($agent->id, $retrieved->id);
    }

    public function test_can_get_agent_by_id()
    {
        $agent = AiAgent::create([
            'name' => 'Test Agent',
            'provider' => 'openai',
        ]);

        $retrieved = Chatbot::getAgent($agent->id);
        $this->assertNotNull($retrieved);
        $this->assertEquals($agent->id, $retrieved->id);
    }

    public function test_get_agent_returns_null_for_invalid_identifier()
    {
        $result = Chatbot::getAgent('non-existent');
        $this->assertNull($result);
    }

    public function test_provider_returns_provider_instance()
    {
        $provider = Chatbot::provider('openai');
        $this->assertInstanceOf(\LaravelAI\Chatbot\Providers\ProviderInterface::class, $provider);
    }

    public function test_provider_throws_exception_for_invalid_provider()
    {
        $this->expectException(\InvalidArgumentException::class);
        Chatbot::provider('invalid-provider');
    }
}


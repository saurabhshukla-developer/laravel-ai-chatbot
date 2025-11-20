<?php

namespace LaravelAI\Chatbot\Tests\Unit\Models;

use LaravelAI\Chatbot\Models\AiAgent;
use LaravelAI\Chatbot\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AiAgentTest extends TestCase
{
    use RefreshDatabase;

    public function test_agent_can_be_created()
    {
        $agent = AiAgent::create([
            'name' => 'Test Agent',
            'slug' => 'test-agent',
            'provider' => 'openai',
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('chatbot_ai_agents', [
            'name' => 'Test Agent',
            'slug' => 'test-agent',
        ]);
    }

    public function test_slug_is_auto_generated_from_name()
    {
        $agent = AiAgent::create([
            'name' => 'My Test Agent',
            'provider' => 'openai',
        ]);

        $this->assertEquals('my-test-agent', $agent->slug);
    }

    public function test_agent_can_have_system_prompt()
    {
        $agent = AiAgent::create([
            'name' => 'Test Agent',
            'provider' => 'openai',
            'system_prompt' => 'You are a helpful assistant.',
        ]);

        $this->assertEquals('You are a helpful assistant.', $agent->system_prompt);
    }

    public function test_agent_can_store_config()
    {
        $config = ['temperature' => 0.7, 'max_tokens' => 1000];
        
        $agent = AiAgent::create([
            'name' => 'Test Agent',
            'provider' => 'openai',
            'config' => $config,
        ]);

        $this->assertEquals($config, $agent->config);
    }

    public function test_scope_active_filters_active_agents()
    {
        AiAgent::create(['name' => 'Active', 'provider' => 'openai', 'is_active' => true]);
        AiAgent::create(['name' => 'Inactive', 'provider' => 'openai', 'is_active' => false]);

        $activeAgents = AiAgent::active()->get();
        $this->assertCount(1, $activeAgents);
        $this->assertEquals('Active', $activeAgents->first()->name);
    }
}


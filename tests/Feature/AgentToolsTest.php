<?php

namespace LaravelAI\Chatbot\Tests\Feature;

use LaravelAI\Chatbot\Models\Tool;
use LaravelAI\Chatbot\Models\AiAgent;
use LaravelAI\Chatbot\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AgentToolsTest extends TestCase
{
    use RefreshDatabase;

    public function test_agent_can_have_tools_assigned()
    {
        $agent = AiAgent::create([
            'name' => 'Test Agent',
            'provider' => 'openai',
        ]);

        $tool1 = Tool::create([
            'name' => 'Tool 1',
            'type' => 'function',
        ]);

        $tool2 = Tool::create([
            'name' => 'Tool 2',
            'type' => 'function',
        ]);

        $agent->tools()->attach([$tool1->id, $tool2->id]);

        $tools = $agent->tools()->get();
        $this->assertCount(2, $tools);
        $this->assertTrue($tools->contains($tool1));
        $this->assertTrue($tools->contains($tool2));
    }

    public function test_agent_can_sync_tools()
    {
        $agent = AiAgent::create([
            'name' => 'Test Agent',
            'provider' => 'openai',
        ]);

        $tool1 = Tool::create(['name' => 'Tool 1', 'type' => 'function']);
        $tool2 = Tool::create(['name' => 'Tool 2', 'type' => 'function']);
        $tool3 = Tool::create(['name' => 'Tool 3', 'type' => 'function']);

        // Attach tool1 and tool2
        $agent->tools()->sync([$tool1->id, $tool2->id]);
        $tools = $agent->tools()->get();
        $this->assertCount(2, $tools);

        // Sync to tool2 and tool3
        $agent->tools()->sync([$tool2->id, $tool3->id]);
        $tools = $agent->tools()->get();
        $this->assertCount(2, $tools);
        $this->assertTrue($tools->contains($tool2));
        $this->assertTrue($tools->contains($tool3));
        $this->assertFalse($tools->contains($tool1));
    }

    public function test_agent_get_formatted_tools_includes_database_tools()
    {
        $agent = AiAgent::create([
            'name' => 'Test Agent',
            'provider' => 'openai',
        ]);

        $tool = Tool::create([
            'name' => 'Calculator',
            'type' => 'function',
            'definition' => [
                'type' => 'function',
                'function' => [
                    'name' => 'calculate',
                    'description' => 'Calculate',
                ],
            ],
        ]);

        $agent->tools()->attach($tool->id);

        $formattedTools = $agent->getFormattedTools();

        $this->assertIsArray($formattedTools);
        $this->assertGreaterThanOrEqual(1, count($formattedTools));
    }

    public function test_agent_can_be_created_with_tools()
    {
        $tool1 = Tool::create(['name' => 'Tool 1', 'type' => 'function']);
        $tool2 = Tool::create(['name' => 'Tool 2', 'type' => 'function']);

        $agent = AiAgent::create([
            'name' => 'Test Agent',
            'provider' => 'openai',
        ]);

        $agent->tools()->attach([$tool1->id, $tool2->id]);

        $tools = $agent->tools()->get();
        $this->assertCount(2, $tools);
    }

    public function test_agent_tools_relationship_exists()
    {
        $agent = AiAgent::create([
            'name' => 'Test Agent',
            'provider' => 'openai',
        ]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsToMany::class, $agent->tools());
    }
}


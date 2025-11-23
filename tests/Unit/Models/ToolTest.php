<?php

namespace LaravelAI\Chatbot\Tests\Unit\Models;

use LaravelAI\Chatbot\Models\Tool;
use LaravelAI\Chatbot\Models\AiAgent;
use LaravelAI\Chatbot\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ToolTest extends TestCase
{
    use RefreshDatabase;

    public function test_tool_can_be_created()
    {
        $tool = Tool::create([
            'name' => 'Calculator',
            'slug' => 'calculator',
            'description' => 'Performs calculations',
            'type' => 'function',
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('chatbot_tools', [
            'name' => 'Calculator',
            'slug' => 'calculator',
        ]);
    }

    public function test_tool_can_have_definition()
    {
        $definition = [
            'type' => 'function',
            'function' => [
                'name' => 'calculate',
                'description' => 'Calculate expression',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'expression' => ['type' => 'string'],
                    ],
                ],
            ],
        ];

        $tool = Tool::create([
            'name' => 'Calculator',
            'type' => 'function',
            'definition' => $definition,
        ]);

        $this->assertEquals($definition, $tool->definition);
    }

    public function test_tool_can_be_assigned_to_agents()
    {
        $tool = Tool::create([
            'name' => 'Calculator',
            'type' => 'function',
        ]);

        $agent1 = AiAgent::create([
            'name' => 'Agent 1',
            'provider' => 'openai',
        ]);

        $agent2 = AiAgent::create([
            'name' => 'Agent 2',
            'provider' => 'openai',
        ]);

        $tool->agents()->attach([$agent1->id, $agent2->id]);

        $this->assertCount(2, $tool->agents);
        $this->assertTrue($tool->agents->contains($agent1));
        $this->assertTrue($tool->agents->contains($agent2));
    }

    public function test_tool_get_formatted_definition_returns_correct_format()
    {
        $definition = [
            'type' => 'function',
            'function' => [
                'name' => 'calculate',
                'description' => 'Calculate',
                'parameters' => ['type' => 'object'],
            ],
        ];

        $tool = Tool::create([
            'name' => 'Calculator',
            'type' => 'function',
            'definition' => $definition,
        ]);

        $formatted = $tool->getFormattedDefinition();

        $this->assertIsArray($formatted);
        $this->assertArrayHasKey('type', $formatted);
        $this->assertArrayHasKey('function', $formatted);
    }

    public function test_tool_slug_is_auto_generated()
    {
        $tool = Tool::create([
            'name' => 'My Calculator Tool',
            'type' => 'function',
        ]);

        $this->assertNotNull($tool->slug);
        $this->assertNotEmpty($tool->slug);
    }

    public function test_scope_active_filters_active_tools()
    {
        Tool::create(['name' => 'Active Tool', 'type' => 'function', 'is_active' => true]);
        Tool::create(['name' => 'Inactive Tool', 'type' => 'function', 'is_active' => false]);

        $activeTools = Tool::active()->get();
        $this->assertCount(1, $activeTools);
        $this->assertEquals('Active Tool', $activeTools->first()->name);
    }
}


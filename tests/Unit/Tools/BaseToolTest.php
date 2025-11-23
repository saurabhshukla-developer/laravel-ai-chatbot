<?php

namespace LaravelAI\Chatbot\Tests\Unit\Tools;

use LaravelAI\Chatbot\Tools\BaseTool;
use LaravelAI\Chatbot\Tests\TestCase;

class MockTestTool extends BaseTool
{
    public function name(): string
    {
        return 'Test Tool';
    }

    public function description(): string
    {
        return 'A test tool';
    }

    public function parameters(): array
    {
        return [
            'properties' => [
                'param1' => [
                    'type' => 'string',
                    'description' => 'First parameter',
                ],
                'param2' => [
                    'type' => 'number',
                    'description' => 'Second parameter',
                ],
            ],
            'required' => ['param1'],
        ];
    }

    public function execute(array $arguments): mixed
    {
        // Validate required parameter
        $this->validateArguments($arguments, ['param1']);
        return ['result' => 'success'];
    }
}

class BaseToolTest extends TestCase
{
    public function test_tool_has_name()
    {
        $tool = new MockTestTool();
        $this->assertEquals('Test Tool', $tool->name());
    }

    public function test_tool_has_description()
    {
        $tool = new MockTestTool();
        $this->assertEquals('A test tool', $tool->description());
    }

    public function test_tool_has_parameters()
    {
        $tool = new MockTestTool();
        $parameters = $tool->parameters();

        $this->assertIsArray($parameters);
        $this->assertArrayHasKey('properties', $parameters);
        $this->assertArrayHasKey('required', $parameters);
    }

    public function test_tool_slug_is_generated_from_name()
    {
        $tool = new MockTestTool();
        $slug = $tool->slug();

        $this->assertEquals('test-tool', $slug);
    }

    public function test_tool_get_definition_returns_correct_format()
    {
        $tool = new MockTestTool();
        $definition = $tool->getDefinition();

        $this->assertIsArray($definition);
        $this->assertArrayHasKey('type', $definition);
        $this->assertArrayHasKey('function', $definition);
        $this->assertEquals('function', $definition['type']);
        $this->assertEquals('test-tool', $definition['function']['name']);
        $this->assertEquals('A test tool', $definition['function']['description']);
    }

    public function test_tool_can_execute()
    {
        $tool = new MockTestTool();
        $result = $tool->execute(['param1' => 'value1']);

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['result']);
    }

    public function test_validate_arguments_passes_with_valid_arguments()
    {
        $tool = new MockTestTool();
        
        // Test indirectly through execute - should not throw exception with valid args
        $result = $tool->execute(['param1' => 'value1']);
        
        $this->assertIsArray($result);
    }

    public function test_validate_arguments_throws_exception_with_missing_required()
    {
        $tool = new MockTestTool();
        
        // Test indirectly - execute should handle validation
        $this->expectException(\InvalidArgumentException::class);
        $tool->execute([]);
    }
}


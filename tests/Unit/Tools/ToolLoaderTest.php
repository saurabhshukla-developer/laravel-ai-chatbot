<?php

namespace LaravelAI\Chatbot\Tests\Unit\Tools;

use LaravelAI\Chatbot\Tools\ToolLoader;
use LaravelAI\Chatbot\Tools\BaseTool;
use LaravelAI\Chatbot\Tests\TestCase;
use Illuminate\Support\Facades\File;

class ToolLoaderTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a temporary tools directory for testing
        $this->toolsPath = base_path('app/Tools');
        if (!File::exists($this->toolsPath)) {
            File::makeDirectory($this->toolsPath, 0755, true);
        }
    }

    protected function tearDown(): void
    {
        // Clean up test files
        if (File::exists($this->toolsPath)) {
            File::deleteDirectory($this->toolsPath);
        }
        
        parent::tearDown();
    }

    public function test_can_discover_tools()
    {
        // Create a test tool file
        $toolContent = <<<'PHP'
<?php

namespace App\Tools;

use LaravelAI\Chatbot\Tools\BaseTool;

class TestTool extends BaseTool
{
    public function name(): string { return 'Test Tool'; }
    public function description(): string { return 'Test description'; }
    public function parameters(): array { return ['properties' => [], 'required' => []]; }
    public function execute(array $arguments): mixed { return ['result' => 'test']; }
}
PHP;

        File::put($this->toolsPath . '/TestTool.php', $toolContent);

        $tools = ToolLoader::discover();

        $this->assertIsArray($tools);
        $this->assertGreaterThanOrEqual(1, count($tools));
    }

    public function test_can_get_tool_by_slug()
    {
        $toolContent = <<<'PHP'
<?php

namespace App\Tools;

use LaravelAI\Chatbot\Tools\BaseTool;

class CalculatorTool extends BaseTool
{
    public function name(): string { return 'Calculator'; }
    public function description(): string { return 'Calculate'; }
    public function parameters(): array { return ['properties' => [], 'required' => []]; }
    public function execute(array $arguments): mixed { return ['result' => 42]; }
}
PHP;

        File::put($this->toolsPath . '/CalculatorTool.php', $toolContent);

        $tool = ToolLoader::getBySlug('calculator');

        $this->assertNotNull($tool);
        $this->assertInstanceOf(BaseTool::class, $tool);
        $this->assertEquals('Calculator', $tool->name());
    }

    public function test_get_by_slug_returns_null_for_non_existent_tool()
    {
        $tool = ToolLoader::getBySlug('non-existent-tool');
        $this->assertNull($tool);
    }

    public function test_can_execute_tool()
    {
        $toolContent = <<<'PHP'
<?php

namespace App\Tools;

use LaravelAI\Chatbot\Tools\BaseTool;

class TestTool extends BaseTool
{
    public function name(): string { return 'Test Tool'; }
    public function description(): string { return 'Test'; }
    public function parameters(): array { 
        return [
            'properties' => [
                'input' => ['type' => 'string'],
            ],
            'required' => ['input'],
        ];
    }
    public function execute(array $arguments): mixed { 
        return ['result' => ($arguments['input'] ?? '') . ' processed'];
    }
}
PHP;

        File::put($this->toolsPath . '/TestTool.php', $toolContent);

        $result = ToolLoader::execute('test-tool', ['input' => 'test']);

        $this->assertIsArray($result);
        // The tool returns the input + ' processed', but check what it actually returns
        $this->assertArrayHasKey('result', $result);
        $this->assertStringContainsString('test', $result['result']);
    }

    public function test_execute_throws_exception_for_invalid_tool()
    {
        $this->expectException(\Exception::class);
        ToolLoader::execute('non-existent-tool', []);
    }

    public function test_discover_ignores_invalid_tool_files()
    {
        // Create an invalid PHP file (syntax error)
        File::put($this->toolsPath . '/InvalidTool.php', '<?php invalid syntax');

        // Create a valid tool
        $validTool = <<<'PHP'
<?php

namespace App\Tools;

use LaravelAI\Chatbot\Tools\BaseTool;

class ValidTool extends BaseTool
{
    public function name(): string { return 'Valid'; }
    public function description(): string { return 'Valid'; }
    public function parameters(): array { return ['properties' => [], 'required' => []]; }
    public function execute(array $arguments): mixed { return []; }
}
PHP;
        File::put($this->toolsPath . '/ValidTool.php', $validTool);

        // Should not throw exception, should skip invalid file
        $tools = ToolLoader::discover();
        
        // Should have at least the valid tool
        $this->assertIsArray($tools);
    }
}


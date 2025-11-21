<?php

/**
 * Tool Template
 * 
 * Copy this file to app/Tools/YourToolName.php and customize it.
 * The tool will be automatically discovered and available for your agents.
 */

namespace App\Tools;

use LaravelAI\Chatbot\Tools\BaseTool;

class YourToolName extends BaseTool
{
    /**
     * The display name of the tool.
     */
    public function name(): string
    {
        return 'Your Tool Name';
    }

    /**
     * Description of what the tool does.
     * This is shown to the AI model to help it decide when to use the tool.
     */
    public function description(): string
    {
        return 'Describe what your tool does here. Be specific and clear.';
    }

    /**
     * Define the parameters schema (JSON Schema format).
     */
    public function parameters(): array
    {
        return [
            'properties' => [
                'param1' => [
                    'type' => 'string',
                    'description' => 'Description of parameter 1',
                ],
                'param2' => [
                    'type' => 'integer',
                    'description' => 'Description of parameter 2',
                    'default' => 10, // Optional default value
                ],
            ],
            'required' => ['param1'], // List required parameters
        ];
    }

    /**
     * Execute the tool with the given arguments.
     * 
     * @param array $arguments The arguments passed from the AI
     * @return mixed The result of the tool execution
     */
    public function execute(array $arguments): mixed
    {
        // Validate required arguments
        $this->validateArguments($arguments, ['param1']);
        
        // Get arguments
        $param1 = $arguments['param1'];
        $param2 = $arguments['param2'] ?? 10; // Use default if not provided
        
        // Your tool logic here
        // Example: Database query, API call, calculation, etc.
        
        $result = [
            'success' => true,
            'data' => [
                'param1' => $param1,
                'param2' => $param2,
                // Add your result data here
            ],
        ];
        
        return $result;
    }
}


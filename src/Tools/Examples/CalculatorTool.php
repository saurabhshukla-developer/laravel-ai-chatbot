<?php

namespace LaravelAI\Chatbot\Tools\Examples;

use LaravelAI\Chatbot\Tools\BaseTool;

class CalculatorTool extends BaseTool
{
    public function name(): string
    {
        return 'Calculator';
    }

    public function description(): string
    {
        return 'Performs basic mathematical calculations including addition, subtraction, multiplication, and division.';
    }

    public function parameters(): array
    {
        return [
            'properties' => [
                'expression' => [
                    'type' => 'string',
                    'description' => 'The mathematical expression to evaluate (e.g., "2 + 2", "10 * 5", "100 / 4")',
                ],
            ],
            'required' => ['expression'],
        ];
    }

    public function execute(array $arguments): mixed
    {
        $this->validateArguments($arguments, ['expression']);

        $expression = $arguments['expression'];
        
        // Sanitize the expression to only allow numbers, operators, and spaces
        $expression = preg_replace('/[^0-9+\-*/().\s]/', '', $expression);
        
        // Evaluate the expression safely
        try {
            // Use eval in a controlled way (in production, consider using a math parser library)
            $result = eval("return {$expression};");
            
            return [
                'success' => true,
                'result' => $result,
                'expression' => $expression,
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'error' => 'Invalid mathematical expression',
                'expression' => $expression,
            ];
        }
    }
}


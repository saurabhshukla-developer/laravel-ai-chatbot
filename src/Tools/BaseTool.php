<?php

namespace LaravelAI\Chatbot\Tools;

abstract class BaseTool
{
    /**
     * The name of the tool.
     */
    abstract public function name(): string;

    /**
     * The description of what the tool does.
     * This is shown to the AI model to help it decide when to use the tool.
     */
    abstract public function description(): string;

    /**
     * The parameters schema for the tool.
     * This follows JSON Schema format.
     */
    abstract public function parameters(): array;

    /**
     * Execute the tool with the given arguments.
     * 
     * @param array $arguments The arguments passed from the AI
     * @return mixed The result of the tool execution
     */
    abstract public function execute(array $arguments): mixed;

    /**
     * Get the tool slug (auto-generated from name if not overridden).
     */
    public function slug(): string
    {
        return \Illuminate\Support\Str::slug($this->name());
    }

    /**
     * Get the tool definition in OpenAI/Anthropic format.
     */
    public function getDefinition(): array
    {
        return [
            'type' => 'function',
            'function' => [
                'name' => $this->slug(),
                'description' => $this->description(),
                'parameters' => array_merge([
                    'type' => 'object',
                ], $this->parameters()),
            ],
        ];
    }

    /**
     * Validate arguments before execution.
     */
    protected function validateArguments(array $arguments, array $required = []): void
    {
        foreach ($required as $param) {
            if (!isset($arguments[$param])) {
                throw new \InvalidArgumentException("Missing required parameter: {$param}");
            }
        }
    }
}


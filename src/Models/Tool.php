<?php

namespace LaravelAI\Chatbot\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tool extends Model
{
    protected $table = 'chatbot_tools';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'type',
        'definition',
        'implementation',
        'config',
        'is_active',
    ];

    protected $casts = [
        'definition' => 'array',
        'config' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($tool) {
            if (empty($tool->slug)) {
                $tool->slug = \Illuminate\Support\Str::slug($tool->name);
            }
        });
    }

    /**
     * Get the agents that use this tool.
     */
    public function agents(): BelongsToMany
    {
        return $this->belongsToMany(AiAgent::class, 'chatbot_agent_tools', 'tool_id', 'agent_id')
            ->withTimestamps();
    }

    /**
     * Scope to get active tools.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the tool definition in OpenAI/Anthropic format.
     */
    public function getFormattedDefinition(): array
    {
        $definition = $this->definition ?? [];

        // Ensure we have the required structure
        if (empty($definition)) {
            return [
                'type' => 'function',
                'function' => [
                    'name' => $this->slug,
                    'description' => $this->description ?? '',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [],
                        'required' => [],
                    ],
                ],
            ];
        }

        // If definition already has the structure, return it
        if (isset($definition['type']) || isset($definition['function'])) {
            return $definition;
        }

        // Otherwise, wrap it in the standard format
        return [
            'type' => 'function',
            'function' => array_merge([
                'name' => $this->slug,
                'description' => $this->description ?? '',
            ], $definition),
        ];
    }
}


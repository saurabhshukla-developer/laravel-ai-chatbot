<?php

namespace LaravelAI\Chatbot\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AiAgent extends Model
{
    protected $table = 'chatbot_ai_agents';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'provider',
        'model',
        'system_prompt',
        'config',
        'tools',
        'is_active',
    ];

    protected $casts = [
        'config' => 'array',
        'tools' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($agent) {
            if (empty($agent->slug)) {
                $agent->slug = \Illuminate\Support\Str::slug($agent->name);
            }
        });
    }

    /**
     * Get the tools associated with this agent.
     */
    public function tools(): BelongsToMany
    {
        return $this->belongsToMany(Tool::class, 'chatbot_agent_tools', 'agent_id', 'tool_id')
            ->withTimestamps();
    }

    /**
     * Get the API key for this agent's provider.
     */
    public function getApiKey()
    {
        return ApiKey::defaultForProvider($this->provider)->first();
    }

    /**
     * Get formatted tools for API calls.
     */
    public function getFormattedTools(): array
    {
        $formattedTools = [];
        
        // Get database tools
        $dbTools = $this->tools()->active()->get();
        foreach ($dbTools as $tool) {
            $formattedTools[] = $tool->getFormattedDefinition();
        }

        // Get file-based tools (if any are assigned via tool_ids or config)
        $fileBasedTools = $this->getFileBasedTools();
        foreach ($fileBasedTools as $tool) {
            $formattedTools[] = $tool->getDefinition();
        }

        // Fallback to legacy tools field if no relationships exist
        if (empty($formattedTools) && !empty($this->tools)) {
            return $this->tools;
        }

        return $formattedTools;
    }

    /**
     * Get file-based tools assigned to this agent.
     */
    protected function getFileBasedTools(): array
    {
        // Check if agent has file-based tools configured
        $fileToolSlugs = $this->config['file_tools'] ?? [];
        
        if (empty($fileToolSlugs)) {
            return [];
        }

        $tools = [];
        foreach ($fileToolSlugs as $slug) {
            $tool = \LaravelAI\Chatbot\Tools\ToolLoader::getBySlug($slug);
            if ($tool) {
                $tools[] = $tool;
            }
        }

        return $tools;
    }

    /**
     * Scope to get active agents.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}


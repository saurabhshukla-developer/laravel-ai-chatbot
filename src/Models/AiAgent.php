<?php

namespace LaravelAI\Chatbot\Models;

use Illuminate\Database\Eloquent\Model;

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
     * Get the API key for this agent's provider.
     */
    public function getApiKey()
    {
        return ApiKey::defaultForProvider($this->provider)->first();
    }

    /**
     * Scope to get active agents.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}


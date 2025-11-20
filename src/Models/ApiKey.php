<?php

namespace LaravelAI\Chatbot\Models;

use Illuminate\Database\Eloquent\Model;

class ApiKey extends Model
{
    protected $table = 'chatbot_api_keys';

    protected $fillable = [
        'provider',
        'name',
        'api_key',
        'api_secret',
        'config',
        'is_active',
        'is_default',
    ];

    protected $casts = [
        'config' => 'array',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
    ];

    protected $hidden = [
        'api_key',
        'api_secret',
    ];

    /**
     * Get the decrypted API key.
     */
    public function getDecryptedApiKey(): string
    {
        if (empty($this->api_key)) {
            return '';
        }

        try {
            return decrypt($this->api_key);
        } catch (\Exception $e) {
            // If decryption fails, assume it's already plain text (for backwards compatibility)
            return $this->api_key;
        }
    }

    /**
     * Set the encrypted API key.
     */
    public function setApiKeyAttribute($value): void
    {
        if (empty($value)) {
            $this->attributes['api_key'] = null;
            return;
        }

        // Always encrypt the value when setting
        $this->attributes['api_key'] = encrypt($value);
    }

    /**
     * Get the decrypted API secret.
     */
    public function getDecryptedApiSecret(): ?string
    {
        if (empty($this->api_secret)) {
            return null;
        }

        try {
            return decrypt($this->api_secret);
        } catch (\Exception $e) {
            // If decryption fails, assume it's already plain text
            return $this->api_secret;
        }
    }

    /**
     * Set the encrypted API secret.
     */
    public function setApiSecretAttribute($value): void
    {
        if (empty($value)) {
            $this->attributes['api_secret'] = null;
            return;
        }

        // Always encrypt the value when setting
        $this->attributes['api_secret'] = encrypt($value);
    }

    /**
     * Scope to get active keys.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get default key for provider.
     */
    public function scopeDefaultForProvider($query, string $provider)
    {
        return $query->where('provider', $provider)
            ->where('is_default', true)
            ->where('is_active', true);
    }
}


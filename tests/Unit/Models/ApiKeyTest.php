<?php

namespace LaravelAI\Chatbot\Tests\Unit\Models;

use LaravelAI\Chatbot\Models\ApiKey;
use LaravelAI\Chatbot\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApiKeyTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_key_is_encrypted_when_saved()
    {
        $apiKey = ApiKey::create([
            'provider' => 'openai',
            'name' => 'Test Key',
            'api_key' => 'sk-test123',
            'is_active' => true,
        ]);

        // The stored value should be encrypted (different from original)
        $this->assertNotEquals('sk-test123', $apiKey->getAttributes()['api_key']);
        
        // But we can decrypt it
        $this->assertEquals('sk-test123', $apiKey->getDecryptedApiKey());
    }

    public function test_api_key_can_be_retrieved_decrypted()
    {
        $apiKey = ApiKey::create([
            'provider' => 'openai',
            'api_key' => 'sk-test123',
        ]);

        $retrieved = ApiKey::find($apiKey->id);
        $this->assertEquals('sk-test123', $retrieved->getDecryptedApiKey());
    }

    public function test_api_secret_is_optional()
    {
        $apiKey = ApiKey::create([
            'provider' => 'openai',
            'api_key' => 'sk-test123',
        ]);

        $this->assertNull($apiKey->getDecryptedApiSecret());
    }

    public function test_api_secret_is_encrypted_when_provided()
    {
        $apiKey = ApiKey::create([
            'provider' => 'openai',
            'api_key' => 'sk-test123',
            'api_secret' => 'secret123',
        ]);

        $this->assertNotEquals('secret123', $apiKey->getAttributes()['api_secret']);
        $this->assertEquals('secret123', $apiKey->getDecryptedApiSecret());
    }

    public function test_scope_active_filters_active_keys()
    {
        ApiKey::create(['provider' => 'openai', 'api_key' => 'key1', 'is_active' => true]);
        ApiKey::create(['provider' => 'openai', 'api_key' => 'key2', 'is_active' => false]);

        $activeKeys = ApiKey::active()->get();
        $this->assertCount(1, $activeKeys);
        $this->assertTrue($activeKeys->first()->is_active);
    }

    public function test_scope_default_for_provider()
    {
        ApiKey::create([
            'provider' => 'openai',
            'api_key' => 'key1',
            'is_default' => true,
            'is_active' => true,
        ]);
        ApiKey::create([
            'provider' => 'openai',
            'api_key' => 'key2',
            'is_default' => false,
            'is_active' => true,
        ]);

        $default = ApiKey::defaultForProvider('openai')->first();
        $this->assertNotNull($default);
        $this->assertTrue($default->is_default);
    }
}


<?php

namespace LaravelAI\Chatbot\Tests\Feature;

use LaravelAI\Chatbot\Models\Tool;
use LaravelAI\Chatbot\Models\AiAgent;
use LaravelAI\Chatbot\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ToolControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_view_tools_index()
    {
        Tool::create([
            'name' => 'Test Tool',
            'type' => 'function',
        ]);

        $response = $this->get(route('chatbot.tools.index'));

        $response->assertStatus(200);
        $response->assertSee('Test Tool');
    }

    public function test_can_view_tools_folder_info()
    {
        $response = $this->get(route('chatbot.tools.folder-info'));

        $response->assertStatus(200);
        $response->assertSee('Tools Folder Information');
    }

    public function test_can_create_tool_via_form()
    {
        $response = $this->get(route('chatbot.tools.create'));

        $response->assertStatus(200);
        $response->assertSee('Create Tool');
    }

    public function test_can_store_tool()
    {
        $data = [
            'name' => 'New Tool',
            'slug' => 'new-tool',
            'description' => 'A new tool',
            'type' => 'function',
            'is_active' => true,
        ];

        $response = $this->post(route('chatbot.tools.store'), $data);

        $response->assertRedirect(route('chatbot.tools.index'));
        $this->assertDatabaseHas('chatbot_tools', [
            'name' => 'New Tool',
            'slug' => 'new-tool',
        ]);
    }

    public function test_can_view_tool()
    {
        $tool = Tool::create([
            'name' => 'Test Tool',
            'type' => 'function',
        ]);

        $response = $this->get(route('chatbot.tools.show', $tool));

        $response->assertStatus(200);
        $response->assertSee('Test Tool');
    }

    public function test_can_edit_tool()
    {
        $tool = Tool::create([
            'name' => 'Test Tool',
            'type' => 'function',
        ]);

        $response = $this->get(route('chatbot.tools.edit', $tool));

        $response->assertStatus(200);
        $response->assertSee('Edit Tool');
    }

    public function test_can_update_tool()
    {
        $tool = Tool::create([
            'name' => 'Old Name',
            'type' => 'function',
        ]);

        $data = [
            'name' => 'Updated Name',
            'slug' => $tool->slug,
            'type' => 'function',
        ];

        $response = $this->put(route('chatbot.tools.update', $tool), $data);

        $response->assertRedirect(route('chatbot.tools.index'));
        $this->assertDatabaseHas('chatbot_tools', [
            'id' => $tool->id,
            'name' => 'Updated Name',
        ]);
    }

    public function test_can_delete_tool()
    {
        $tool = Tool::create([
            'name' => 'Test Tool',
            'type' => 'function',
        ]);

        $response = $this->delete(route('chatbot.tools.destroy', $tool));

        $response->assertRedirect(route('chatbot.tools.index'));
        $this->assertDatabaseMissing('chatbot_tools', [
            'id' => $tool->id,
        ]);
    }
}


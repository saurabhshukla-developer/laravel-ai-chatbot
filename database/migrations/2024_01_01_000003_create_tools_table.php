<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('chatbot_tools', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('type')->default('function'); // function, api, custom, etc.
            $table->json('definition')->nullable(); // Tool definition (OpenAI/Anthropic format)
            $table->text('implementation')->nullable(); // PHP code or implementation details
            $table->json('config')->nullable(); // Additional configuration
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Pivot table for agent-tool relationships
        Schema::create('chatbot_agent_tools', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agent_id')->constrained('chatbot_ai_agents')->onDelete('cascade');
            $table->foreignId('tool_id')->constrained('chatbot_tools')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['agent_id', 'tool_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chatbot_agent_tools');
        Schema::dropIfExists('chatbot_tools');
    }
};


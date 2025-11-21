@extends('chatbot::layout')

@section('title', 'Edit AI Agent')

@section('content')
<div class="px-4 py-6 sm:px-0">
    <h2 class="text-2xl font-bold text-gray-900 mb-6">Edit AI Agent</h2>

    <form action="{{ route('chatbot.agents.update', $agent) }}" method="POST" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="name">
                Name
            </label>
            <input type="text" name="name" id="name" value="{{ old('name', $agent->name) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            @error('name')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="slug">
                Slug
            </label>
            <input type="text" name="slug" id="slug" value="{{ old('slug', $agent->slug) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            @error('slug')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="description">
                Description (Optional)
            </label>
            <textarea name="description" id="description" rows="3" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('description', $agent->description) }}</textarea>
            @error('description')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="provider">
                Provider
            </label>
            <select name="provider" id="provider" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                @foreach($providers as $key => $provider)
                    <option value="{{ $key }}" {{ old('provider', $agent->provider) === $key ? 'selected' : '' }}>
                        {{ $provider['name'] }}
                    </option>
                @endforeach
            </select>
            @error('provider')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="model">
                Model (Optional)
            </label>
            <input type="text" name="model" id="model" value="{{ old('model', $agent->model) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            @error('model')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="system_prompt">
                System Prompt (Optional)
            </label>
            <textarea name="system_prompt" id="system_prompt" rows="5" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('system_prompt', $agent->system_prompt) }}</textarea>
            @error('system_prompt')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">
                Tools (Optional)
            </label>
            
            @if(isset($fileTools) && count($fileTools) > 0)
                <div class="mb-3">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">File-Based Tools</label>
                    <div class="max-h-32 overflow-y-auto border rounded p-2 bg-green-50">
                        @foreach($fileTools as $tool)
                            <label class="flex items-center mb-2">
                                <input type="checkbox" name="file_tool_slugs[]" value="{{ $tool->slug() }}" {{ in_array($tool->slug(), old('file_tool_slugs', $agent->config['file_tools'] ?? [])) ? 'checked' : '' }} class="form-checkbox">
                                <span class="ml-2 text-sm text-gray-700">
                                    {{ $tool->name() }}
                                    <span class="text-xs text-green-600">(File)</span>
                                    @if($tool->description())
                                        <span class="text-gray-500">- {{ Str::limit($tool->description(), 40) }}</span>
                                    @endif
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endif

            @if(isset($dbTools) && count($dbTools) > 0)
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Database Tools</label>
                    <div class="max-h-32 overflow-y-auto border rounded p-2">
                        @foreach($dbTools as $tool)
                            <label class="flex items-center mb-2">
                                <input type="checkbox" name="tool_ids[]" value="{{ $tool->id }}" {{ in_array($tool->id, old('tool_ids', $agent->tools->pluck('id')->toArray())) ? 'checked' : '' }} class="form-checkbox">
                                <span class="ml-2 text-sm text-gray-700">
                                    {{ $tool->name }}
                                    @if($tool->description)
                                        <span class="text-gray-500">- {{ Str::limit($tool->description, 40) }}</span>
                                    @endif
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endif

            @if((!isset($fileTools) || count($fileTools) === 0) && (!isset($dbTools) || count($dbTools) === 0))
                <div class="border rounded p-3 bg-gray-50">
                    <p class="text-sm text-gray-600 mb-2">No tools available.</p>
                    <p class="text-xs text-gray-500">
                        Create file-based tools in <code class="bg-gray-200 px-1 rounded">{{ config('chatbot.tools_path', 'app/Tools') }}</code> or 
                        <a href="{{ route('chatbot.tools.create') }}" class="text-blue-500 hover:text-blue-700">create a database tool</a>.
                    </p>
                </div>
            @endif

            <p class="text-xs text-gray-500 mt-2">Select tools that this agent can use. File-based tools are automatically discovered from PHP files.</p>
        </div>

        <div class="mb-4">
            <label class="flex items-center">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $agent->is_active) ? 'checked' : '' }} class="form-checkbox">
                <span class="ml-2 text-gray-700">Active</span>
            </label>
        </div>

        <div class="flex items-center justify-between">
            <a href="{{ route('chatbot.agents.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Cancel
            </a>
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Update Agent
            </button>
        </div>
    </form>
</div>
@endsection


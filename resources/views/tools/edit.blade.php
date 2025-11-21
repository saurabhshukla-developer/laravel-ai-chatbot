@extends('chatbot::layout')

@section('title', 'Edit Tool')

@section('content')
<div class="px-4 py-6 sm:px-0">
    <h2 class="text-2xl font-bold text-gray-900 mb-6">Edit Tool</h2>

    <form action="{{ route('chatbot.tools.update', $tool) }}" method="POST" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="name">
                Name
            </label>
            <input type="text" name="name" id="name" value="{{ old('name', $tool->name) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            @error('name')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="slug">
                Slug
            </label>
            <input type="text" name="slug" id="slug" value="{{ old('slug', $tool->slug) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            <p class="text-xs text-gray-500 mt-1">Used as the function name in API calls. Must be unique.</p>
            @error('slug')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="description">
                Description
            </label>
            <textarea name="description" id="description" rows="3" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('description', $tool->description) }}</textarea>
            <p class="text-xs text-gray-500 mt-1">Describe what this tool does. This will be shown to the AI model.</p>
            @error('description')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="type">
                Type
            </label>
            <select name="type" id="type" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                <option value="function" {{ old('type', $tool->type) === 'function' ? 'selected' : '' }}>Function</option>
                <option value="api" {{ old('type', $tool->type) === 'api' ? 'selected' : '' }}>API</option>
                <option value="custom" {{ old('type', $tool->type) === 'custom' ? 'selected' : '' }}>Custom</option>
            </select>
            @error('type')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="definition">
                Definition (JSON)
            </label>
            <textarea name="definition" id="definition" rows="10" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline font-mono text-sm">{{ old('definition', $tool->definition ? json_encode($tool->definition, JSON_PRETTY_PRINT) : '{
  "type": "function",
  "function": {
    "name": "",
    "description": "",
    "parameters": {
      "type": "object",
      "properties": {},
      "required": []
    }
  }
}') }}</textarea>
            <p class="text-xs text-gray-500 mt-1">Tool definition in OpenAI/Anthropic format. Should include function name, description, and parameters schema.</p>
            @error('definition')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="implementation">
                Implementation (Optional)
            </label>
            <textarea name="implementation" id="implementation" rows="8" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline font-mono text-sm">{{ old('implementation', $tool->implementation) }}</textarea>
            <p class="text-xs text-gray-500 mt-1">PHP code or implementation details for executing this tool.</p>
            @error('implementation')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label class="flex items-center">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $tool->is_active) ? 'checked' : '' }} class="form-checkbox">
                <span class="ml-2 text-gray-700">Active</span>
            </label>
        </div>

        <div class="flex items-center justify-between">
            <a href="{{ route('chatbot.tools.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Cancel
            </a>
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Update Tool
            </button>
        </div>
    </form>
</div>
@endsection


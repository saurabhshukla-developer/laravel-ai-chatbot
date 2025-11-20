@extends('chatbot::layout')

@section('title', 'Edit API Key')

@section('content')
<div class="px-4 py-6 sm:px-0">
    <h2 class="text-2xl font-bold text-gray-900 mb-6">Edit API Key</h2>

    <form action="{{ route('chatbot.api-keys.update', $apiKey) }}" method="POST" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="provider">
                Provider
            </label>
            <select name="provider" id="provider" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                @foreach($providers as $key => $provider)
                    <option value="{{ $key }}" {{ old('provider', $apiKey->provider) === $key ? 'selected' : '' }}>
                        {{ $provider['name'] }}
                    </option>
                @endforeach
            </select>
            @error('provider')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="name">
                Name (Optional)
            </label>
            <input type="text" name="name" id="name" value="{{ old('name', $apiKey->name) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            @error('name')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="api_key">
                API Key
            </label>
            <input type="text" name="api_key" id="api_key" value="{{ old('api_key', $apiKey->getDecryptedApiKey()) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            @error('api_key')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="api_secret">
                API Secret (Optional)
            </label>
            <input type="text" name="api_secret" id="api_secret" value="{{ old('api_secret', $apiKey->getDecryptedApiSecret()) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            @error('api_secret')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label class="flex items-center">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $apiKey->is_active) ? 'checked' : '' }} class="form-checkbox">
                <span class="ml-2 text-gray-700">Active</span>
            </label>
        </div>

        <div class="mb-4">
            <label class="flex items-center">
                <input type="checkbox" name="is_default" value="1" {{ old('is_default', $apiKey->is_default) ? 'checked' : '' }} class="form-checkbox">
                <span class="ml-2 text-gray-700">Set as default for this provider</span>
            </label>
        </div>

        <div class="flex items-center justify-between">
            <a href="{{ route('chatbot.api-keys.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Cancel
            </a>
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Update API Key
            </button>
        </div>
    </form>
</div>
@endsection


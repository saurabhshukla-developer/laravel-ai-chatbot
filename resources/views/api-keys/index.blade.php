@extends('chatbot::layout')

@section('title', 'API Keys')

@section('content')
<div class="px-4 py-6 sm:px-0">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-900">API Keys</h2>
        <a href="{{ route('chatbot.api-keys.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Add API Key
        </a>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        <ul class="divide-y divide-gray-200">
            @forelse($apiKeys as $apiKey)
                <li>
                    <div class="px-4 py-4 sm:px-6 flex items-center justify-between">
                        <div class="flex items-center">
                            <div>
                                <p class="text-sm font-medium text-gray-900">
                                    {{ $apiKey->name ?: $apiKey->provider }}
                                </p>
                                <p class="text-sm text-gray-500">
                                    Provider: {{ ucfirst($apiKey->provider) }}
                                    @if($apiKey->is_default)
                                        <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Default
                                        </span>
                                    @endif
                                    @if(!$apiKey->is_active)
                                        <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            Inactive
                                        </span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            <a href="{{ route('chatbot.api-keys.edit', $apiKey) }}" class="text-indigo-600 hover:text-indigo-900">
                                Edit
                            </a>
                            <form action="{{ route('chatbot.api-keys.destroy', $apiKey) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                            </form>
                        </div>
                    </div>
                </li>
            @empty
                <li class="px-4 py-4 sm:px-6">
                    <p class="text-gray-500">No API keys found. <a href="{{ route('chatbot.api-keys.create') }}" class="text-blue-500 hover:text-blue-700">Add one</a> to get started.</p>
                </li>
            @endforelse
        </ul>
    </div>
</div>
@endsection


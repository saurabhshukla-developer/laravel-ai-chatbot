@extends('chatbot::layout')

@section('title', 'AI Agents')

@section('content')
<div class="px-4 py-6 sm:px-0">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-900">AI Agents</h2>
        <a href="{{ route('chatbot.agents.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Create Agent
        </a>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        <ul class="divide-y divide-gray-200">
            @forelse($agents as $agent)
                <li>
                    <div class="px-4 py-4 sm:px-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">
                                        {{ $agent->name }}
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        {{ $agent->description }}
                                    </p>
                                    <p class="text-xs text-gray-400 mt-1">
                                        Provider: {{ ucfirst($agent->provider) }} | 
                                        Model: {{ $agent->model ?? 'Default' }}
                                        @if(!$agent->is_active)
                                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                Inactive
                                            </span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="flex space-x-2">
                                <a href="{{ route('chatbot.agents.show', $agent) }}" class="text-indigo-600 hover:text-indigo-900">
                                    View
                                </a>
                                <a href="{{ route('chatbot.agents.edit', $agent) }}" class="text-indigo-600 hover:text-indigo-900">
                                    Edit
                                </a>
                                <form action="{{ route('chatbot.agents.destroy', $agent) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </li>
            @empty
                <li class="px-4 py-4 sm:px-6">
                    <p class="text-gray-500">No AI agents found. <a href="{{ route('chatbot.agents.create') }}" class="text-blue-500 hover:text-blue-700">Create one</a> to get started.</p>
                </li>
            @endforelse
        </ul>
    </div>
</div>
@endsection


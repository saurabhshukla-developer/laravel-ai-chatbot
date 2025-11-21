@extends('chatbot::layout')

@section('title', $tool->name)

@section('content')
<div class="px-4 py-6 sm:px-0">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-900">{{ $tool->name }}</h2>
        <div class="flex space-x-2">
            <a href="{{ route('chatbot.tools.edit', $tool) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Edit
            </a>
            <a href="{{ route('chatbot.tools.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Back
            </a>
        </div>
    </div>

    <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        <div class="mb-4">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Details</h3>
            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Slug</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $tool->slug }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Type</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($tool->type) }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        @if($tool->is_active)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Active</span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Inactive</span>
                        @endif
                    </dd>
                </div>
            </dl>
        </div>

        @if($tool->description)
            <div class="mb-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Description</h3>
                <p class="text-sm text-gray-700">{{ $tool->description }}</p>
            </div>
        @endif

        @if($tool->definition)
            <div class="mb-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Definition</h3>
                <pre class="bg-gray-50 p-4 rounded text-sm overflow-x-auto"><code>{{ json_encode($tool->definition, JSON_PRETTY_PRINT) }}</code></pre>
            </div>
        @endif

        @if($tool->implementation)
            <div class="mb-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Implementation</h3>
                <pre class="bg-gray-50 p-4 rounded text-sm overflow-x-auto"><code>{{ $tool->implementation }}</code></pre>
            </div>
        @endif
    </div>

    @if($agents->count() > 0)
        <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Used by Agents</h3>
            <ul class="divide-y divide-gray-200">
                @foreach($agents as $agent)
                    <li class="py-3">
                        <a href="{{ route('chatbot.agents.show', $agent) }}" class="text-indigo-600 hover:text-indigo-900">
                            {{ $agent->name }}
                        </a>
                        <span class="text-sm text-gray-500 ml-2">({{ ucfirst($agent->provider) }})</span>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
</div>
@endsection


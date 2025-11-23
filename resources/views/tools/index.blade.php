@extends('chatbot::layout')

@section('title', 'Tools')

@section('content')
<div class="px-4 py-6 sm:px-0">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Tools</h2>
        <div class="flex space-x-2">
            <a href="{{ route('chatbot.tools.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Create Database Tool
            </a>
            <a href="{{ route('chatbot.tools.folder-info') }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                📁 Tools Folder Info
            </a>
        </div>
    </div>

    @if(count($fileTools) > 0)
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-3">File-Based Tools</h3>
            <div class="bg-white shadow overflow-hidden sm:rounded-md">
                <ul class="divide-y divide-gray-200">
                    @foreach($fileTools as $tool)
                        <li>
                            <div class="px-4 py-4 sm:px-6">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">
                                                {{ $tool->name() }}
                                                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    File-Based
                                                </span>
                                            </p>
                                            <p class="text-sm text-gray-500">
                                                {{ $tool->description() }}
                                            </p>
                                            <p class="text-xs text-gray-400 mt-1">
                                                Slug: {{ $tool->slug() }} | 
                                                Class: {{ get_class($tool) }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        Auto-discovered from files
                                    </div>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div>
        <h3 class="text-lg font-semibold text-gray-900 mb-3">Database Tools</h3>
        <div class="bg-white shadow overflow-hidden sm:rounded-md">
            <ul class="divide-y divide-gray-200">
                @forelse($dbTools as $tool)
                    <li>
                        <div class="px-4 py-4 sm:px-6">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ $tool->name }}
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            {{ $tool->description }}
                                        </p>
                                        <p class="text-xs text-gray-400 mt-1">
                                            Type: {{ ucfirst($tool->type) }} | 
                                            Slug: {{ $tool->slug }}
                                            @if(!$tool->is_active)
                                                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                    Inactive
                                                </span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="flex space-x-2">
                                    <a href="{{ route('chatbot.tools.show', $tool) }}" class="text-indigo-600 hover:text-indigo-900">
                                        View
                                    </a>
                                    <a href="{{ route('chatbot.tools.edit', $tool) }}" class="text-indigo-600 hover:text-indigo-900">
                                        Edit
                                    </a>
                                    <form action="{{ route('chatbot.tools.destroy', $tool) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?')">
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
                        <p class="text-gray-500">No database tools found. <a href="{{ route('chatbot.tools.create') }}" class="text-blue-500 hover:text-blue-700">Create one</a> to get started.</p>
                    </li>
                @endforelse
            </ul>
        </div>
    </div>

    @if(count($fileTools) === 0 && count($dbTools) === 0)
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <h4 class="text-sm font-semibold text-blue-900 mb-2">💡 Quick Start: Create File-Based Tools</h4>
            <p class="text-sm text-blue-800 mb-2">The easiest way to create tools is by creating PHP files in the <code class="bg-blue-100 px-1 rounded">{{ config('chatbot.tools_path', 'app/Tools') }}</code> directory.</p>
            <p class="text-sm text-blue-800">Create a file like <code class="bg-blue-100 px-1 rounded">app/Tools/MyTool.php</code> that extends <code class="bg-blue-100 px-1 rounded">LaravelAI\Chatbot\Tools\BaseTool</code> and it will be automatically discovered!</p>
        </div>
    @endif
</div>
@endsection


@extends('chatbot::layout')

@section('title', 'Tools Folder Information')

@section('content')
<div class="px-4 py-6 sm:px-0">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Tools Folder Information</h2>
        <a href="{{ route('chatbot.tools.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
            Back to Tools
        </a>
    </div>

    <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Folder Details</h3>
        
        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
            <div>
                <dt class="text-sm font-medium text-gray-500">Relative Path</dt>
                <dd class="mt-1 text-sm text-gray-900 font-mono bg-gray-50 p-2 rounded">{{ $toolsPath }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Full Path</dt>
                <dd class="mt-1 text-sm text-gray-900 font-mono bg-gray-50 p-2 rounded break-all">{{ $fullPath }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Status</dt>
                <dd class="mt-1 text-sm text-gray-900">
                    @if($exists)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            ✓ Exists
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            ⚠ Not Found
                        </span>
                    @endif
                </dd>
            </div>
        </dl>
    </div>

    @if($exists && count($files) > 0)
        <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Files in Folder</h3>
            <ul class="divide-y divide-gray-200">
                @foreach($files as $file)
                    <li class="py-3">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-900 font-mono">{{ $file['name'] }}</p>
                                <p class="text-xs text-gray-500">{{ number_format($file['size']) }} bytes</p>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    @elseif($exists && count($files) === 0)
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
            <p class="text-sm text-yellow-800">The folder exists but is empty. Create your first tool file here!</p>
        </div>
    @else
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
            <h4 class="text-sm font-semibold text-blue-900 mb-2">📁 Folder Not Found</h4>
            <p class="text-sm text-blue-800 mb-2">The tools folder doesn't exist yet. It will be created automatically when you:</p>
            <ul class="list-disc list-inside text-sm text-blue-800 mb-2">
                <li>Run: <code class="bg-blue-100 px-1 rounded">php artisan chatbot:make-tool ToolName</code></li>
                <li>Or manually create the folder: <code class="bg-blue-100 px-1 rounded">{{ $fullPath }}</code></li>
            </ul>
        </div>
    @endif

    <div class="bg-white shadow-md rounded px-8 pt-6 pb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Start</h3>
        
        <div class="space-y-4">
            <div>
                <h4 class="text-sm font-semibold text-gray-700 mb-2">Method 1: Use Artisan Command (Recommended)</h4>
                <div class="bg-gray-50 p-3 rounded">
                    <code class="text-sm">php artisan chatbot:make-tool Calculator</code>
                </div>
                <p class="text-xs text-gray-500 mt-1">This will create the folder and tool file automatically.</p>
            </div>

            <div>
                <h4 class="text-sm font-semibold text-gray-700 mb-2">Method 2: Create Manually</h4>
                <div class="bg-gray-50 p-3 rounded">
                    <code class="text-sm">mkdir -p {{ $toolsPath }}<br>
                    # Then create your tool file: {{ $toolsPath }}/YourTool.php</code>
                </div>
            </div>

            <div>
                <h4 class="text-sm font-semibold text-gray-700 mb-2">Example Tool Structure</h4>
                <div class="bg-gray-50 p-3 rounded">
                    <pre class="text-xs overflow-x-auto"><code>{{ $exampleCode }}</code></pre>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


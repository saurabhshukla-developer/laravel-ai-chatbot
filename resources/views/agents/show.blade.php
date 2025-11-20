@extends('chatbot::layout')

@section('title', $agent->name)

@section('content')
<div class="px-4 py-6 sm:px-0">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-900">{{ $agent->name }}</h2>
        <div class="flex space-x-2">
            <a href="{{ route('chatbot.agents.edit', $agent) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Edit
            </a>
            <a href="{{ route('chatbot.agents.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
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
                    <dd class="mt-1 text-sm text-gray-900">{{ $agent->slug }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Provider</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($agent->provider) }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Model</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $agent->model ?? 'Default' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        @if($agent->is_active)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Active</span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Inactive</span>
                        @endif
                    </dd>
                </div>
            </dl>
        </div>

        @if($agent->description)
            <div class="mb-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Description</h3>
                <p class="text-sm text-gray-700">{{ $agent->description }}</p>
            </div>
        @endif

        @if($agent->system_prompt)
            <div class="mb-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">System Prompt</h3>
                <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $agent->system_prompt }}</p>
            </div>
        @endif
    </div>

    <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Test Chat</h3>
        <div id="chat-container">
            <div id="chat-messages" class="mb-4 p-4 bg-gray-50 rounded max-h-96 overflow-y-auto">
                <!-- Messages will appear here -->
            </div>
            <form id="chat-form" class="flex">
                <input type="text" id="chat-input" placeholder="Type your message..." class="flex-1 shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                <button type="submit" class="ml-2 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Send
                </button>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('chat-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const input = document.getElementById('chat-input');
    const message = input.value.trim();
    if (!message) return;

    const messagesDiv = document.getElementById('chat-messages');
    
    // Add user message
    const userMsg = document.createElement('div');
    userMsg.className = 'mb-2 text-right';
    userMsg.innerHTML = `<div class="inline-block bg-blue-500 text-white px-4 py-2 rounded">${message}</div>`;
    messagesDiv.appendChild(userMsg);
    
    input.value = '';
    messagesDiv.scrollTop = messagesDiv.scrollHeight;

    // Send to API
    try {
        const response = await fetch('{{ route("chatbot.agents.chat", $agent) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ message: message })
        });

        const data = await response.json();
        
        // Add AI response
        const aiMsg = document.createElement('div');
        aiMsg.className = 'mb-2';
        aiMsg.innerHTML = `<div class="inline-block bg-gray-200 text-gray-800 px-4 py-2 rounded">${data.content || 'No response'}</div>`;
        messagesDiv.appendChild(aiMsg);
        messagesDiv.scrollTop = messagesDiv.scrollHeight;
    } catch (error) {
        const errorMsg = document.createElement('div');
        errorMsg.className = 'mb-2 text-red-500';
        errorMsg.textContent = 'Error: ' + error.message;
        messagesDiv.appendChild(errorMsg);
    }
});
</script>
@endsection


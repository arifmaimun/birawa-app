<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Chat') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="chatApp()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg h-[600px] flex">
                
                <!-- Sidebar -->
                <div class="w-1/3 border-r border-gray-200 flex flex-col">
                    <div class="p-4 border-b border-gray-200 bg-gray-50">
                        <h3 class="font-medium text-gray-700">Percakapan</h3>
                    </div>
                    <div class="overflow-y-auto flex-1">
                        @if($recentChats->isEmpty() && $friends->isEmpty())
                            <div class="p-4 text-gray-500 text-center text-sm">
                                Belum ada percakapan. Mulai chat dari daftar teman!
                            </div>
                        @else
                            <!-- Recent Chats -->
                            @foreach($recentChats as $chatUser)
                                <a href="{{ route('chat.index', ['user_id' => $chatUser->id]) }}" 
                                   class="flex items-center p-4 hover:bg-gray-50 border-b border-gray-100 {{ $activeChatUser && $activeChatUser->id == $chatUser->id ? 'bg-indigo-50' : '' }}">
                                    <div class="h-10 w-10 rounded-full bg-indigo-200 flex items-center justify-center mr-3 overflow-hidden">
                                        @if($chatUser->avatar)
                                            <img src="{{ Storage::url($chatUser->avatar) }}" class="h-full w-full object-cover">
                                        @else
                                            <span class="font-bold text-indigo-700">{{ substr($chatUser->name, 0, 1) }}</span>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="font-medium text-gray-900 truncate">{{ $chatUser->name }}</div>
                                        <div class="text-xs text-gray-500 truncate">Klik untuk chat</div>
                                    </div>
                                </a>
                            @endforeach
                            
                            <!-- Other Friends (if not in recent) -->
                            @if($friends->count() > $recentChats->count())
                                <div class="p-2 bg-gray-100 text-xs font-semibold text-gray-500 uppercase tracking-wide">Teman Lainnya</div>
                                @foreach($friends as $friend)
                                    @if(!$recentChats->contains('id', $friend->id))
                                        <a href="{{ route('chat.index', ['user_id' => $friend->id]) }}" 
                                           class="flex items-center p-4 hover:bg-gray-50 border-b border-gray-100 {{ $activeChatUser && $activeChatUser->id == $friend->id ? 'bg-indigo-50' : '' }}">
                                            <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center mr-3 overflow-hidden">
                                                @if($friend->avatar)
                                                    <img src="{{ Storage::url($friend->avatar) }}" class="h-full w-full object-cover">
                                                @else
                                                    <span class="font-bold text-gray-600">{{ substr($friend->name, 0, 1) }}</span>
                                                @endif
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="font-medium text-gray-900 truncate">{{ $friend->name }}</div>
                                            </div>
                                        </a>
                                    @endif
                                @endforeach
                            @endif
                        @endif
                    </div>
                </div>

                <!-- Chat Area -->
                <div class="w-2/3 flex flex-col">
                    @if($activeChatUser)
                        <!-- Header -->
                        <div class="p-4 border-b border-gray-200 flex items-center justify-between bg-white">
                            <div class="flex items-center">
                                <div class="h-10 w-10 rounded-full bg-indigo-600 flex items-center justify-center text-white font-bold mr-3 overflow-hidden">
                                    @if($activeChatUser->avatar)
                                        <img src="{{ Storage::url($activeChatUser->avatar) }}" class="h-full w-full object-cover">
                                    @else
                                        {{ substr($activeChatUser->name, 0, 1) }}
                                    @endif
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900">{{ $activeChatUser->name }}</div>
                                    <div class="text-xs text-green-500">Online</div>
                                </div>
                            </div>
                        </div>

                        <!-- Messages -->
                        <div class="flex-1 overflow-y-auto p-4 space-y-4 bg-gray-50" id="messages-container" x-ref="messagesContainer">
                            @foreach($messages as $message)
                                <div class="flex {{ $message->sender_id === Auth::id() ? 'justify-end' : 'justify-start' }}">
                                    <div class="max-w-xs lg:max-w-md px-4 py-2 rounded-lg {{ $message->sender_id === Auth::id() ? 'bg-indigo-600 text-white rounded-br-none' : 'bg-white border border-gray-200 text-gray-900 rounded-bl-none' }}">
                                        <p class="text-sm">{{ $message->message }}</p>
                                        <div class="text-xs {{ $message->sender_id === Auth::id() ? 'text-indigo-200' : 'text-gray-400' }} mt-1 text-right">
                                            {{ $message->created_at->format('H:i') }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            
                            <!-- New messages appended via JS will go here -->
                            <template x-for="msg in newMessages" :key="msg.id">
                                <div class="flex" :class="msg.sender_id === {{ Auth::id() }} ? 'justify-end' : 'justify-start'">
                                    <div class="max-w-xs lg:max-w-md px-4 py-2 rounded-lg" 
                                         :class="msg.sender_id === {{ Auth::id() }} ? 'bg-indigo-600 text-white rounded-br-none' : 'bg-white border border-gray-200 text-gray-900 rounded-bl-none'">
                                        <p class="text-sm" x-text="msg.message"></p>
                                        <div class="text-xs mt-1 text-right" 
                                             :class="msg.sender_id === {{ Auth::id() }} ? 'text-indigo-200' : 'text-gray-400'">
                                            <span x-text="new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})"></span>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- Input -->
                        <div class="p-4 border-t border-gray-200 bg-white">
                            <form @submit.prevent="sendMessage" class="flex space-x-2">
                                <input type="text" x-model="messageInput" 
                                       class="flex-1 rounded-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" 
                                       placeholder="Tulis pesan...">
                                <button type="submit" 
                                        class="px-4 py-2 bg-indigo-600 text-white rounded-full hover:bg-indigo-700 disabled:opacity-50"
                                        :disabled="!messageInput.trim()">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transform rotate-90" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="flex-1 flex items-center justify-center bg-gray-50 text-gray-500">
                            <div class="text-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                                <p>Pilih teman untuk memulai percakapan</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if($activeChatUser)
    <script>
        function chatApp() {
            return {
                messageInput: '',
                newMessages: [],
                pollingInterval: null,
                
                init() {
                    this.scrollToBottom();
                    this.startPolling();
                    
                    // Watch for new messages to scroll
                    this.$watch('newMessages', () => {
                        this.$nextTick(() => this.scrollToBottom());
                    });
                },
                
                scrollToBottom() {
                    const container = this.$refs.messagesContainer;
                    if (container) {
                        container.scrollTop = container.scrollHeight;
                    }
                },
                
                async sendMessage() {
                    if (!this.messageInput.trim()) return;
                    
                    const message = this.messageInput;
                    this.messageInput = ''; // Optimistic clear
                    
                    try {
                        const response = await fetch("{{ route('chat.store') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                receiver_id: {{ $activeChatUser->id }},
                                message: message
                            })
                        });
                        
                        if (response.ok) {
                            const data = await response.json();
                            this.newMessages.push(data);
                        } else {
                            // Handle error, maybe restore input
                            console.error('Failed to send message');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                    }
                },
                
                startPolling() {
                    this.pollingInterval = setInterval(async () => {
                        try {
                            const response = await fetch("{{ route('chat.messages', $activeChatUser->id) }}");
                            if (response.ok) {
                                const allMessages = await response.json();
                                // Filter out messages we already have rendered server-side
                                // This is a bit naive, better to use IDs.
                                // But since we are appending to newMessages, we need to be careful.
                                // Actually, polling usually replaces the whole list or fetches "after ID".
                                // For simplicity, let's just use the server rendered count + newMessages count logic?
                                // No, better to fetch only messages AFTER the last one we have.
                                
                                // Simplified approach: 
                                // 1. We have server-side rendered messages (no JS array initially).
                                // 2. We have client-side appended messages (newMessages).
                                // 3. Polling returns ALL messages.
                                
                                // Let's just determine which ones are NEW.
                                // We need the ID of the last message we have.
                                
                                let lastId = 0;
                                // Get last ID from DOM
                                const messageElements = document.querySelectorAll('#messages-container > div');
                                // This is hard to parse.
                                
                                // Alternative: Polling replaces logic is simpler but causes flicker?
                                // Let's just use the API to get ALL messages and filter in JS.
                                // We need to know which IDs we already have.
                                
                                // Let's populate initial IDs from server
                                const initialIds = @json($messages->pluck('id'));
                                const currentIds = new Set([...initialIds, ...this.newMessages.map(m => m.id)]);
                                
                                allMessages.forEach(msg => {
                                    if (!currentIds.has(msg.id)) {
                                        this.newMessages.push(msg);
                                        currentIds.add(msg.id);
                                    }
                                });
                            }
                        } catch (error) {
                            console.error('Polling error:', error);
                        }
                    }, 3000); // Poll every 3 seconds
                }
            }
        }
    </script>
    @else
    <script>
        function chatApp() {
            return {
                // Empty for no active chat
            }
        }
    </script>
    @endif
</x-app-layout>

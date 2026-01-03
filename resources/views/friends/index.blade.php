<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Teman Saya') }}
            </h2>
            <a href="{{ route('friends.search') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm">
                Cari Dokter Lain
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <!-- Pending Requests -->
            @if($pendingRequests->isNotEmpty())
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Permintaan Pertemanan Masuk</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($pendingRequests as $request)
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center overflow-hidden">
                                        @if($request->user->avatar)
                                            <img src="{{ Storage::url($request->user->avatar) }}" alt="{{ $request->user->name }}" class="h-full w-full object-cover">
                                        @else
                                            <span class="text-gray-600 font-bold">{{ substr($request->user->name, 0, 1) }}</span>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900">{{ $request->user->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $request->created_at->diffForHumans() }}</div>
                                    </div>
                                </div>
                                <div class="flex space-x-2">
                                    <form action="{{ route('friends.accept', $request->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="px-3 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700">Terima</button>
                                    </form>
                                    <form action="{{ route('friends.destroy', $request->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700">Tolak</button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Friends List -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Daftar Teman</h3>
                    
                    @if($friends->isEmpty())
                        <p class="text-gray-500 text-center py-8">Belum ada teman. Cari dokter lain untuk terhubung!</p>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($friends as $friend)
                                <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:shadow-md transition">
                                    <div class="flex items-center space-x-3">
                                        <div class="h-12 w-12 rounded-full bg-indigo-100 flex items-center justify-center overflow-hidden">
                                            @if($friend->avatar)
                                                <img src="{{ Storage::url($friend->avatar) }}" alt="{{ $friend->name }}" class="h-full w-full object-cover">
                                            @else
                                                <span class="text-indigo-600 font-bold text-lg">{{ substr($friend->name, 0, 1) }}</span>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900">{{ $friend->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $friend->email }}</div>
                                        </div>
                                    </div>
                                    <div class="flex flex-col space-y-2">
                                        <a href="{{ route('chat.index', ['user_id' => $friend->id]) }}" class="px-3 py-1 bg-indigo-600 text-white text-xs rounded text-center hover:bg-indigo-700">
                                            Chat
                                        </a>
                                        <form action="{{ route('friends.destroy', $friend->friendship_id) }}" method="POST" onsubmit="return confirm('Hapus teman ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-3 py-1 bg-gray-200 text-gray-700 text-xs rounded text-center hover:bg-red-100 hover:text-red-700 w-full">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Sent Requests -->
            @if($sentRequests->isNotEmpty())
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Permintaan Terkirim</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($sentRequests as $request)
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-500">
                                        {{ substr($request->friend->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900">{{ $request->friend->name }}</div>
                                        <div class="text-xs text-gray-500">Menunggu konfirmasi</div>
                                    </div>
                                </div>
                                <form action="{{ route('friends.destroy', $request->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-gray-400 hover:text-red-500 text-sm">Batal</button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>

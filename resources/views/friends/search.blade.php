<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Cari Dokter') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    
                    <!-- Search Form -->
                    <form action="{{ route('friends.search') }}" method="GET" class="mb-8">
                        <div class="flex gap-4">
                            <input type="text" name="query" value="{{ $query }}" placeholder="Cari nama atau email dokter..." class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                                Cari
                            </button>
                        </div>
                    </form>

                    <!-- Results -->
                    @if(request()->has('query'))
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Hasil Pencarian</h3>
                        
                        @if($results->isEmpty())
                            <p class="text-gray-500">Tidak ditemukan dokter dengan kata kunci tersebut.</p>
                        @else
                            <div class="space-y-4">
                                @foreach($results as $user)
                                    <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                                        <div class="flex items-center space-x-4">
                                            <div class="h-12 w-12 rounded-full bg-gray-200 flex items-center justify-center overflow-hidden">
                                                @if($user->avatar)
                                                    <img src="{{ Storage::url($user->avatar) }}" alt="{{ $user->name }}" class="h-full w-full object-cover">
                                                @else
                                                    <span class="text-gray-600 font-bold text-lg">{{ substr($user->name, 0, 1) }}</span>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="font-medium text-gray-900">{{ $user->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                            </div>
                                        </div>
                                        
                                        <div>
                                            @if($user->friendship_status === 'accepted')
                                                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">Teman</span>
                                            @elseif($user->friendship_status === 'pending')
                                                @if($user->is_sender)
                                                    <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-semibold">Permintaan Terkirim</span>
                                                @else
                                                    <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-semibold">Menunggu Konfirmasi Anda</span>
                                                @endif
                                            @else
                                                <form action="{{ route('friends.request') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="friend_id" value="{{ $user->id }}">
                                                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700">
                                                        Tambah Teman
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>

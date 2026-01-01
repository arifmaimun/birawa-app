<x-app-layout>
    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['label' => 'Home', 'route' => route('dashboard')],
        ['label' => 'Clients']
    ]" />

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Header & Search -->
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between mb-6">
            <h1 class="font-bold text-2xl text-slate-800 leading-tight">
                {{ __('Client Management') }}
            </h1>
            <a href="{{ route('clients.create') }}" class="inline-flex justify-center items-center px-4 py-2 bg-birawa-600 border border-transparent rounded-xl font-bold text-sm text-white hover:bg-birawa-700 active:bg-birawa-800 focus:outline-none focus:ring-2 focus:ring-birawa-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-lg shadow-birawa-500/30">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Tambah Klien
            </a>
        </div>
            
            <!-- Search -->
            <div class="relative">
                <form action="{{ route('clients.index') }}" method="GET">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           class="pl-10 block w-full rounded-xl border-slate-200 shadow-sm focus:border-birawa-500 focus:ring focus:ring-birawa-500 focus:ring-opacity-50 py-3" 
                           placeholder="Search clients by name, phone, or email...">
                </form>
            </div>

            <!-- Client Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse ($clients as $client)
                    <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-100 flex flex-col gap-3 relative overflow-hidden">
                        <!-- Top Row: Name & Pets Count -->
                        <div class="flex justify-between items-start z-10">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center text-xl font-bold">
                                    {{ substr($client->name, 0, 1) }}
                                </div>
                                <div>
                                    <h3 class="font-bold text-slate-800 text-lg leading-tight">{{ $client->name }}</h3>
                                    <p class="text-xs text-slate-500">Joined {{ $client->created_at->format('M Y') }}</p>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wide bg-slate-50 text-slate-600 border border-slate-100">
                                {{ $client->pets_count ?? $client->patients_count }} Pets
                            </span>
                        </div>

                        <!-- Contact Info -->
                        <div class="space-y-2">
                            <div class="flex items-center gap-2 text-sm text-slate-600">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                                <span>{{ $client->phone }}</span>
                            </div>
                            @if($client->email && !str_contains($client->email, '@birawa.vet'))
                                <div class="flex items-center gap-2 text-sm text-slate-600">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                    <span class="truncate">{{ $client->email }}</span>
                                </div>
                            @endif
                            @if($client->address)
                                <div class="flex items-start gap-2 text-sm text-slate-600">
                                    <svg class="w-4 h-4 text-slate-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <span class="line-clamp-2">{{ $client->address }}</span>
                                </div>
                            @endif
                        </div>

                        <!-- Actions -->
                        <div class="grid grid-cols-2 gap-2 mt-2 pt-3 border-t border-slate-100">
                            <a href="{{ route('clients.edit', $client) }}" class="flex items-center justify-center gap-2 px-3 py-2 text-sm font-medium text-birawa-600 bg-birawa-50 rounded-lg hover:bg-birawa-100 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Edit
                            </a>
                            <form action="{{ route('clients.destroy', $client) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this client?');" class="w-full">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full flex items-center justify-center gap-2 px-3 py-2 text-sm font-medium text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="col-span-1 md:col-span-2 lg:col-span-3 text-center py-12">
                        <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-300">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-slate-900">No clients found</h3>
                        <p class="mt-1 text-slate-500">Try adjusting your search or add a new client.</p>
                        <div class="mt-6">
                            <a href="{{ route('clients.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-xl text-white bg-birawa-600 hover:bg-birawa-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-birawa-500">
                                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Add New Client
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if(method_exists($clients, 'hasPages') && $clients->hasPages())
                <div class="mt-6">
                    {{ $clients->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
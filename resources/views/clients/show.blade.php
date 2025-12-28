<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-slate-800">Client Details</h1>
            <a href="{{ route('clients.index') }}" class="text-sm font-medium text-birawa-600 hover:text-birawa-700 flex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to List
            </a>
        </div>

        <!-- Owner Card -->
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 mb-8 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-birawa-50 rounded-bl-full -mr-16 -mt-16 opacity-50 pointer-events-none"></div>
            
            <div class="flex flex-col md:flex-row md:items-start justify-between gap-6 relative z-10">
                <div class="flex gap-5">
                    <div class="w-20 h-20 rounded-2xl bg-slate-100 flex items-center justify-center text-3xl font-bold text-slate-600 shadow-inner">
                        {{ substr($client->name, 0, 1) }}
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-slate-800 mb-1">{{ $client->name }}</h2>
                        <div class="flex flex-col gap-1.5">
                            <div class="flex items-center gap-2 text-slate-600 text-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                {{ $client->phone }}
                            </div>
                            @if($client->user && $client->user->email && !str_contains($client->user->email, '@birawa.vet'))
                            <div class="flex items-center gap-2 text-slate-600 text-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                {{ $client->user->email }}
                            </div>
                            @endif
                            <div class="flex items-center gap-2 text-slate-600 text-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                {{ $client->address ?? 'No address provided' }}
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="flex flex-row md:flex-col gap-3 mt-4 md:mt-0 w-full md:w-auto">
                    <a href="{{ route('clients.edit', $client) }}" class="flex-1 md:flex-none px-5 py-2.5 bg-slate-100 text-slate-700 rounded-xl text-sm font-bold hover:bg-slate-200 text-center flex items-center justify-center gap-2 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                        Edit Profile
                    </a>
                    <form action="{{ route('clients.destroy', $client) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this client? This will remove all their pets and records.');" class="flex-1 md:flex-none">
                        @csrf 
                        @method('DELETE')
                        <button type="submit" class="w-full px-5 py-2.5 bg-red-50 text-red-600 rounded-xl text-sm font-bold hover:bg-red-100 flex items-center justify-center gap-2 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Pets Section -->
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                Pets
                <span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-600 text-xs font-bold">{{ $client->patients->count() }}</span>
            </h2>
            <a href="{{ route('patients.create') }}?client_id={{ $client->id }}" class="px-5 py-2.5 bg-birawa-500 text-white rounded-xl text-sm font-bold shadow-lg shadow-birawa-500/30 hover:bg-birawa-600 flex items-center gap-2 transition-all active:scale-95">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add Pet
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($client->patients as $pet)
                <a href="{{ route('patients.show', $pet) }}" class="bg-white rounded-2xl p-4 shadow-sm border border-slate-100 hover:border-birawa-300 hover:shadow-md transition-all group">
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 rounded-xl bg-birawa-50 text-birawa-600 flex items-center justify-center font-bold text-xl group-hover:bg-birawa-100 transition-colors">
                            {{ substr($pet->name, 0, 1) }}
                        </div>
                        <div class="flex-1">
                            <h3 class="font-bold text-slate-800 text-lg leading-tight group-hover:text-birawa-600 transition-colors">{{ $pet->name }}</h3>
                            <p class="text-sm text-slate-500">{{ $pet->species }} • {{ $pet->breed }}</p>
                            <div class="mt-2 flex items-center gap-2">
                                <span class="text-xs font-medium text-slate-400 bg-slate-50 px-2 py-1 rounded-lg">
                                    {{ $pet->age }}
                                </span>
                                <span class="text-xs font-medium text-slate-400 bg-slate-50 px-2 py-1 rounded-lg">
                                    {{ $pet->gender }}
                                </span>
                            </div>
                        </div>
                        <div class="text-slate-300 group-hover:text-birawa-500 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                    </div>
                </a>
            @empty
                <div class="col-span-full py-16 text-center bg-white rounded-3xl border-2 border-dashed border-slate-200">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-50 text-slate-300 mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800">No pets yet</h3>
                    <p class="text-slate-500 max-w-sm mx-auto mt-1 mb-6">Add a pet to this owner to start tracking their medical history.</p>
                    <a href="{{ route('patients.create') }}?owner_id={{ $owner->id }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-birawa-500 text-white rounded-xl text-sm font-bold shadow-lg shadow-birawa-500/30 hover:bg-birawa-600 transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add First Pet
                    </a>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                {{ __('Patient Management') }}
            </h2>
            <a href="{{ route('patients.create') }}" class="inline-flex justify-center items-center px-4 py-2 bg-birawa-600 border border-transparent rounded-xl font-semibold text-sm text-white hover:bg-birawa-700 active:bg-birawa-800 focus:outline-none focus:ring-2 focus:ring-birawa-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add New Patient
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            
            <!-- Search -->
            <div class="relative">
                <form action="{{ route('patients.index') }}" method="GET">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           class="pl-10 block w-full rounded-xl border-slate-200 shadow-sm focus:border-birawa-500 focus:ring focus:ring-birawa-500 focus:ring-opacity-50 py-3" 
                           placeholder="Search patients or owners...">
                </form>
            </div>

            <!-- Patient Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse ($patients as $patient)
                    <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-100 flex flex-col gap-3 relative overflow-hidden">
                        <!-- Top Row: Name & Species -->
                        <div class="flex justify-between items-start z-10">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-full flex items-center justify-center text-xl font-bold
                                    {{ in_array(strtolower($patient->species), ['cat', 'kucing']) ? 'bg-orange-100 text-orange-600' : 
                                       (in_array(strtolower($patient->species), ['dog', 'anjing']) ? 'bg-blue-100 text-blue-600' : 'bg-slate-100 text-slate-600') }}">
                                    {{ substr($patient->name, 0, 1) }}
                                </div>
                                <div>
                                    <h3 class="font-bold text-slate-800 text-lg leading-tight">{{ $patient->name }}</h3>
                                    <p class="text-sm text-slate-500">{{ $patient->breed ?? 'Unknown Breed' }}</p>
                                </div>
                            </div>
                            @php
                                $species = strtolower($patient->species);
                                $badgeColor = match(true) {
                                    in_array($species, ['cat', 'kucing']) => 'bg-orange-50 text-orange-600 border-orange-100',
                                    in_array($species, ['dog', 'anjing']) => 'bg-blue-50 text-blue-600 border-blue-100',
                                    default => 'bg-slate-50 text-slate-600 border-slate-100'
                                };
                            @endphp
                            <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wide border {{ $badgeColor }}">
                                {{ $patient->species }}
                            </span>
                        </div>

                        <!-- Owner Info -->
                        <div class="flex items-center gap-2 p-3 bg-slate-50 rounded-xl border border-slate-100">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <div class="flex-1">
                                @if($patient->owners->first())
                                    <p class="text-sm font-medium text-slate-700">{{ $patient->owners->first()->name }}</p>
                                @else
                                    <p class="text-sm text-slate-400 italic">No Owner</p>
                                @endif
                            </div>
                        </div>

                        <!-- Details Row -->
                        <div class="flex items-center gap-4 text-sm text-slate-500">
                            <div class="flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>{{ $patient->dob ? \Carbon\Carbon::parse($patient->dob)->diffForHumans(null, true) . ' old' : 'Age Unknown' }}</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                @if(in_array(strtolower($patient->gender), ['jantan', 'male']))
                                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    <span>Male</span>
                                @else
                                    <svg class="w-4 h-4 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    <span>Female</span>
                                @endif
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="grid grid-cols-2 gap-2 mt-2 pt-3 border-t border-slate-100">
                            <a href="{{ route('patients.edit', $patient) }}" class="flex items-center justify-center gap-2 px-3 py-2 text-sm font-medium text-birawa-600 bg-birawa-50 rounded-lg hover:bg-birawa-100 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Edit
                            </a>
                            <form action="{{ route('patients.destroy', $patient) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this patient?');" class="w-full">
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
                        <h3 class="text-lg font-medium text-slate-900">No patients found</h3>
                        <p class="mt-1 text-slate-500">Try adjusting your search or add a new patient.</p>
                        <div class="mt-6">
                            <a href="{{ route('patients.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-xl text-white bg-birawa-600 hover:bg-birawa-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-birawa-500">
                                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Add New Patient
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($patients->hasPages())
                <div class="mt-6">
                    {{ $patients->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
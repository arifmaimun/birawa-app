<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Referrals</h1>
                <p class="text-sm text-slate-500">Manage patient referral letters</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-4">
                <form method="GET" action="{{ route('referrals.index') }}" class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search referrals..." class="w-full sm:w-64 pl-10 pr-4 py-2 rounded-xl border-slate-200 shadow-sm focus:border-birawa-500 focus:ring-birawa-500 transition-colors">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </form>
                <a href="{{ route('referrals.create') }}" class="inline-flex justify-center items-center px-4 py-2 bg-birawa-600 border border-transparent rounded-xl font-bold text-xs text-white uppercase tracking-widest hover:bg-birawa-700 focus:bg-birawa-700 active:bg-birawa-900 focus:outline-none focus:ring-2 focus:ring-birawa-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-lg shadow-birawa-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Create Referral
                </a>
            </div>
        </div>

        <!-- Referrals Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($referrals as $referral)
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 flex flex-col gap-4 hover:border-birawa-200 transition-colors relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-16 h-16 bg-slate-50 rounded-bl-full -mr-8 -mt-8 transition-colors group-hover:bg-birawa-50"></div>
                    
                    <!-- Header -->
                    <div class="relative z-10 flex justify-between items-start">
                        <div>
                            <h3 class="font-bold text-slate-800 text-lg line-clamp-1" title="{{ $referral->target_clinic_name }}">{{ $referral->target_clinic_name }}</h3>
                            <p class="text-xs text-slate-500 flex items-center gap-1 mt-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                {{ $referral->created_at->format('d M Y') }}
                            </p>
                        </div>
                        <div class="text-right">
                             @if($referral->isValid)
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wide bg-emerald-50 text-emerald-600 border border-emerald-100">
                                    Active
                                </span>
                            @else
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wide bg-rose-50 text-rose-600 border border-rose-100">
                                    Expired
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Patient Info -->
                    <div class="flex items-center gap-3 py-3 border-t border-b border-slate-50">
                        <div class="w-10 h-10 rounded-full bg-birawa-50 text-birawa-600 flex items-center justify-center font-bold text-sm shrink-0">
                            {{ substr($referral->patient->name, 0, 1) }}
                        </div>
                        <div class="min-w-0">
                            <p class="font-bold text-slate-700 text-sm truncate">{{ $referral->patient->name }}</p>
                            <p class="text-xs text-slate-500 truncate">{{ $referral->patient->client->name ?? 'Unknown Owner' }}</p>
                        </div>
                    </div>

                    <!-- Footer: Actions -->
                    <div class="flex justify-between items-center mt-auto pt-1">
                        <div class="text-xs text-slate-400">
                            Expires in {{ $referral->valid_until->diffForHumans() }}
                        </div>
                        <a href="{{ route('referrals.public', $referral->access_token) }}" target="_blank" class="px-4 py-2 bg-slate-800 text-white text-sm font-bold rounded-xl hover:bg-slate-700 transition-colors shadow-lg shadow-slate-200 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                            View
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-12 text-center bg-white rounded-3xl border-2 border-dashed border-slate-200">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-50 text-slate-300 mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800">No Referrals Found</h3>
                    <p class="text-slate-500 max-w-sm mx-auto mt-1">Create a referral letter for a patient to get started.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-6">
            {{ $referrals->links() }}
        </div>
    </div>
</x-app-layout>

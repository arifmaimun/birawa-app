<x-app-layout>
    <div class="space-y-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Welcome Section -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-slate-800">Hello, {{ Auth::user()->name }}!</h2>
                <p class="text-sm text-slate-500">Here's your schedule for today.</p>
            </div>
            <div class="hidden md:block">
                <p class="text-sm font-bold text-birawa-600 bg-birawa-50 px-4 py-2 rounded-xl border border-birawa-100">
                    {{ now()->format('l, d M Y') }}
                </p>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-2 gap-4">
            <!-- Today's Visits Stat -->
            <div class="bg-white p-5 rounded-3xl shadow-sm border border-slate-100 flex flex-col justify-between h-32 relative overflow-hidden group">
                <div class="absolute right-0 top-0 p-3 opacity-5 group-hover:opacity-10 transition-opacity">
                    <svg class="w-20 h-20 text-birawa-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
                <div class="relative z-10 flex items-start justify-between">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Today's Visits</span>
                    <div class="p-2 bg-birawa-50 rounded-xl text-birawa-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <div class="relative z-10">
                    <span class="text-3xl font-bold text-slate-800">{{ $todayVisits->count() }}</span>
                    <span class="text-xs text-slate-400 font-medium ml-1">appointments</span>
                </div>
            </div>

            <!-- Pending Invoices Stat -->
            <div class="bg-white p-5 rounded-3xl shadow-sm border border-slate-100 flex flex-col justify-between h-32 relative overflow-hidden group">
                <div class="absolute right-0 top-0 p-3 opacity-5 group-hover:opacity-10 transition-opacity">
                    <svg class="w-20 h-20 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                </div>
                <div class="relative z-10 flex items-start justify-between">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Pending Invoices</span>
                    <div class="p-2 bg-orange-50 rounded-xl text-orange-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <div class="relative z-10">
                    <span class="text-3xl font-bold text-slate-800">{{ $pendingInvoicesCount }}</span>
                    <span class="text-xs text-slate-400 font-medium ml-1">unpaid</span>
                </div>
            </div>
        </div>

        <!-- Action Grid -->
        <div class="grid grid-cols-4 gap-4">
            <a href="{{ route('visits.create') }}" class="group flex flex-col items-center gap-2">
                <div class="w-16 h-16 rounded-2xl bg-birawa-50 border border-birawa-100 flex items-center justify-center text-birawa-600 shadow-sm group-active:scale-95 transition-all group-hover:bg-birawa-100 group-hover:shadow-md">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                </div>
                <span class="text-[10px] font-bold text-slate-600 text-center group-hover:text-birawa-700 transition-colors">New Visit</span>
            </a>
            
            <a href="{{ route('patients.create') }}" class="group flex flex-col items-center gap-2">
                <div class="w-16 h-16 rounded-2xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600 shadow-sm group-active:scale-95 transition-all group-hover:bg-indigo-100 group-hover:shadow-md">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                </div>
                <span class="text-[10px] font-bold text-slate-600 text-center group-hover:text-indigo-700 transition-colors">Add Patient</span>
            </a>
            
            <a href="{{ route('visits.index') }}" class="group flex flex-col items-center gap-2">
                <div class="w-16 h-16 rounded-2xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600 shadow-sm group-active:scale-95 transition-all group-hover:bg-emerald-100 group-hover:shadow-md">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
                <span class="text-[10px] font-bold text-slate-600 text-center group-hover:text-emerald-700 transition-colors">Calendar</span>
            </a>
            
            <a href="{{ route('inventory.index') }}" class="group flex flex-col items-center gap-2">
                <div class="w-16 h-16 rounded-2xl bg-amber-50 border border-amber-100 flex items-center justify-center text-amber-600 shadow-sm group-active:scale-95 transition-all group-hover:bg-amber-100 group-hover:shadow-md">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                </div>
                <span class="text-[10px] font-bold text-slate-600 text-center group-hover:text-amber-700 transition-colors">Inventory</span>
            </a>
        </div>

        <!-- Today's Visits List -->
        <div>
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-slate-800 text-lg">Today's Visits</h3>
                <a href="{{ route('visits.index') }}" class="text-xs font-bold text-birawa-600 hover:text-birawa-700 bg-birawa-50 px-3 py-1.5 rounded-lg transition-colors">View All</a>
            </div>
            
            @if($todayVisits->isEmpty())
                <div class="bg-white rounded-3xl p-8 text-center border border-slate-100 shadow-sm">
                    <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-300">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="text-slate-800 font-bold mb-1">No Visits Today</h3>
                    <p class="text-sm text-slate-400">Enjoy your free time or check upcoming schedule.</p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($todayVisits as $visit)
                        <a href="{{ route('visits.show', $visit) }}" class="block bg-white rounded-2xl p-4 shadow-sm border border-slate-100 active:scale-[0.99] transition-all hover:border-birawa-200 hover:shadow-md group">
                            <div class="flex items-start justify-between">
                                <div class="flex gap-4">
                                    <div class="w-12 h-12 rounded-2xl bg-birawa-50 border border-birawa-100 flex items-center justify-center text-birawa-600 font-bold text-lg shrink-0 group-hover:bg-birawa-100 transition-colors">
                                        {{ substr($visit->patient->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-slate-800 group-hover:text-birawa-700 transition-colors">{{ $visit->patient->name }}</h4>
                                        <p class="text-xs text-slate-500 font-medium">{{ $visit->patient->owners->first()->name ?? 'No Owner' }}</p>
                                        
                                        <div class="flex items-center gap-1 mt-1 text-slate-400">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                            <p class="text-[10px] truncate max-w-[150px]">{{ $visit->patient->owners->first()->address ?? 'No address' }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex flex-col items-end gap-2">
                                    <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-600 text-[10px] font-bold border border-slate-200">
                                        {{ \Carbon\Carbon::parse($visit->scheduled_at)->format('H:i') }}
                                    </span>
                                    <span class="text-[10px] font-bold uppercase tracking-wide {{ $visit->status === 'completed' ? 'text-emerald-600' : ($visit->status === 'in_progress' ? 'text-blue-600' : 'text-slate-400') }}">
                                        {{ str_replace('_', ' ', $visit->status) }}
                                    </span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Low Stock Alert -->
        @if($lowStockItems->isNotEmpty())
            <div class="bg-rose-50 rounded-3xl p-5 border border-rose-100 shadow-sm">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-2 bg-rose-100 rounded-xl text-rose-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-rose-700">Low Stock Alert</h3>
                        <p class="text-xs text-rose-500">Action required for the following items</p>
                    </div>
                </div>
                <div class="space-y-2">
                    @foreach($lowStockItems->take(3) as $item)
                        <div class="flex justify-between items-center bg-white/60 p-3 rounded-xl border border-rose-100/50">
                            <span class="text-sm font-bold text-rose-800">{{ $item->item_name }}</span>
                            <span class="text-xs font-bold bg-rose-100 text-rose-700 px-2 py-1 rounded-lg">{{ $item->stock_qty }} {{ $item->base_unit }} left</span>
                        </div>
                    @endforeach
                </div>
                @if($lowStockItems->count() > 3)
                    <div class="mt-3 text-center">
                        <a href="{{ route('inventory.index') }}" class="text-xs font-bold text-rose-600 hover:text-rose-700">View all {{ $lowStockItems->count() }} alerts &rarr;</a>
                    </div>
                @endif
            </div>
        @endif
    </div>
</x-app-layout>
<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Header & Search -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Inventory Management</h1>
                <p class="text-sm text-slate-500">Track stock, costs, and unit conversions</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                <form method="GET" action="{{ route('inventory.index') }}" class="relative w-full md:w-64">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           class="block w-full pl-10 pr-3 py-2 border border-slate-200 rounded-xl leading-5 bg-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-birawa-500 focus:border-birawa-500 sm:text-sm transition-shadow" 
                           placeholder="Search items or SKU...">
                </form>
                <a href="{{ route('inventory.create') }}" class="inline-flex justify-center items-center px-4 py-2 bg-birawa-600 border border-transparent rounded-xl font-bold text-xs text-white uppercase tracking-widest hover:bg-birawa-700 focus:bg-birawa-700 active:bg-birawa-900 focus:outline-none focus:ring-2 focus:ring-birawa-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-lg shadow-birawa-100 whitespace-nowrap">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Add Item
                </a>
            </div>
        </div>

        <!-- Inventory Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($items as $item)
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 flex flex-col gap-4 hover:border-birawa-200 transition-colors relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-16 h-16 bg-slate-50 rounded-bl-full -mr-8 -mt-8 transition-colors group-hover:bg-birawa-50"></div>
                    
                    <!-- Header -->
                    <div class="relative z-10 flex justify-between items-start">
                        <div>
                            <h3 class="font-bold text-slate-800 text-lg line-clamp-1" title="{{ $item->item_name }}">{{ $item->item_name }}</h3>
                            @if($item->sku)
                                <p class="text-xs text-slate-400 font-mono mt-1">SKU: {{ $item->sku }}</p>
                            @else
                                <p class="text-xs text-slate-300 italic mt-1">No SKU</p>
                            @endif
                        </div>
                        <div class="text-right">
                             @if($item->stock_qty <= $item->alert_threshold)
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wide bg-rose-50 text-rose-600 border border-rose-100">
                                    Low Stock
                                </span>
                            @else
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wide bg-emerald-50 text-emerald-600 border border-emerald-100">
                                    In Stock
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Stock Info -->
                    <div class="flex items-center gap-4 py-3 border-t border-b border-slate-50">
                        <div class="flex-1">
                            <p class="text-xs text-slate-500 uppercase font-bold tracking-wider">Available</p>
                            <p class="text-xl font-bold text-slate-800">
                                {{ $item->stock_qty }} <span class="text-sm text-slate-400 font-normal">{{ $item->base_unit }}</span>
                            </p>
                        </div>
                        <div class="flex-1 border-l border-slate-100 pl-4">
                            <p class="text-xs text-slate-500 uppercase font-bold tracking-wider">Cost (Avg)</p>
                            <p class="text-sm font-bold text-slate-700">Rp {{ number_format($item->average_cost_price, 0, ',', '.') }}</p>
                        </div>
                    </div>
                    
                    <div class="text-xs text-slate-400 bg-slate-50 p-2 rounded-lg">
                        <span class="font-bold">Config:</span> 1 {{ $item->purchase_unit }} = {{ $item->conversion_ratio }} {{ $item->base_unit }}
                    </div>

                    <!-- Footer: Actions -->
                    <div class="grid grid-cols-3 gap-2 mt-auto pt-3 border-t border-slate-50">
                        <a href="{{ route('inventory.restock', $item) }}" class="flex items-center justify-center px-2 py-2 bg-emerald-50 text-emerald-700 text-xs font-bold rounded-xl hover:bg-emerald-100 transition-colors border border-emerald-100" title="Restock">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                        </a>
                        <a href="{{ route('inventory.adjust', $item) }}" class="flex items-center justify-center px-2 py-2 bg-amber-50 text-amber-700 text-xs font-bold rounded-xl hover:bg-amber-100 transition-colors border border-amber-100" title="Adjust Stock">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </a>
                        <a href="{{ route('inventory.edit', $item) }}" class="flex items-center justify-center px-2 py-2 bg-slate-50 text-slate-700 text-xs font-bold rounded-xl hover:bg-slate-100 transition-colors border border-slate-100" title="Edit">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $items->links() }}
        </div>
    </div>
</x-app-layout>

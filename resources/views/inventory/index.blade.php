<x-app-layout>
    <div x-data="{ 
        showLocationModal: false, 
        showTransferModal: false, 
        transferItem: null,
        editingLocation: null,
        locationForm: { name: '', type: 'warehouse', description: '', capacity: '' },
        initLocationForm(location) {
            if (location) {
                this.editingLocation = location;
                this.locationForm = {
                    name: location.name,
                    type: location.type,
                    description: location.description || '',
                    capacity: location.capacity || ''
                };
            } else {
                this.editingLocation = null;
                this.locationForm = { name: '', type: 'warehouse', description: '', capacity: '' };
            }
            this.showLocationModal = true;
        }
    }" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        
        <!-- Location Tabs -->
        <div class="flex items-center gap-3 mb-8 overflow-x-auto pb-2 no-scrollbar">
            @foreach($locations as $location)
                <div class="group relative">
                    <a href="{{ route('inventory.index', ['location_id' => $location->id]) }}" 
                       class="flex items-center px-5 py-2.5 rounded-2xl text-sm font-bold whitespace-nowrap transition-all duration-200 {{ $activeLocation && $activeLocation->id === $location->id ? 'bg-birawa-600 text-white shadow-lg shadow-birawa-200 ring-2 ring-birawa-600 ring-offset-2' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200 hover:border-birawa-200' }}">
                        
                        @if($location->type === 'bag')
                            <svg class="w-4 h-4 mr-2 {{ $activeLocation && $activeLocation->id === $location->id ? 'text-birawa-100' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        @else
                            <svg class="w-4 h-4 mr-2 {{ $activeLocation && $activeLocation->id === $location->id ? 'text-birawa-100' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        @endif
                        
                        {{ $location->name }}
                        <span class="ml-2 text-xs {{ $activeLocation && $activeLocation->id === $location->id ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-500' }} px-2 py-0.5 rounded-full">
                            {{ $location->inventories()->count() }}
                        </span>
                    </a>
                </div>
            @endforeach
            
            <button @click="initLocationForm(null)" class="flex items-center px-4 py-2.5 rounded-2xl bg-white border border-dashed border-slate-300 text-slate-500 hover:text-birawa-600 hover:border-birawa-400 hover:bg-birawa-50 transition-all duration-200 group whitespace-nowrap">
                <div class="bg-slate-100 rounded-full p-1 mr-2 group-hover:bg-birawa-100 transition-colors">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                </div>
                <span class="text-xs font-bold uppercase tracking-wide">Add Location</span>
            </button>
        </div>

        <!-- Header & Search -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-2xl font-bold text-slate-800">{{ $activeLocation->name }} Inventory</h1>
                    <button @click="initLocationForm({{ json_encode($activeLocation->only(['id', 'name', 'type', 'description', 'capacity'])) }})" class="text-slate-400 hover:text-birawa-600 p-1 rounded-full hover:bg-birawa-50 transition-colors" title="Edit Location">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                    </button>
                    @if(!$activeLocation->is_default)
                        <form action="{{ route('storage-locations.destroy', $activeLocation) }}" method="POST" onsubmit="return confirm('Are you sure? All items in this location will be deleted or need to be cleared first.');" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-slate-400 hover:text-rose-500 p-1 rounded-full hover:bg-rose-50 transition-colors" title="Delete Location">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </form>
                    @endif
                </div>
                <p class="text-sm text-slate-500 mt-1">
                    {{ $activeLocation->type === 'warehouse' ? 'Main storage facility' : 'Mobile unit / Bag' }} • 
                    {{ $activeLocation->description ?? 'No description' }}
                </p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                <form method="GET" action="{{ route('inventory.index') }}" class="relative w-full md:w-64">
                    <input type="hidden" name="location_id" value="{{ $activeLocation->id }}">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           class="block w-full pl-10 pr-3 py-2.5 border border-slate-200 rounded-xl leading-5 bg-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-birawa-500 focus:border-birawa-500 text-sm transition-shadow shadow-sm" 
                           placeholder="Search items or SKU...">
                </form>
                
                <!-- Create Item Button -->
                <div class="flex gap-3">
                    <form action="{{ route('inventory.create') }}" method="GET">
                        <input type="hidden" name="storage_location_id" value="{{ $activeLocation->id }}">
                        <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center px-5 py-2.5 bg-birawa-600 border border-transparent rounded-xl font-bold text-sm text-white hover:bg-birawa-700 focus:outline-none focus:ring-2 focus:ring-birawa-500 focus:ring-offset-2 transition-all shadow-lg shadow-birawa-200 whitespace-nowrap">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Add Item
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Inventory Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($items as $item)
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 flex flex-col gap-4 hover:border-birawa-200 transition-all hover:shadow-md relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-16 h-16 bg-slate-50 rounded-bl-full -mr-8 -mt-8 transition-colors group-hover:bg-birawa-50"></div>
                    
                    <!-- Header -->
                    <div class="relative z-10 flex justify-between items-start">
                        <div>
                            <h3 class="font-bold text-slate-800 text-lg line-clamp-1" title="{{ $item->item_name }}">{{ $item->item_name }}</h3>
                            <div class="flex items-center gap-2 mt-1">
                                @if($item->sku)
                                    <span class="text-xs text-slate-400 font-mono bg-slate-50 px-1.5 py-0.5 rounded border border-slate-100">{{ $item->sku }}</span>
                                @endif
                                <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">{{ $item->category ?? 'GEN' }}</span>
                            </div>
                        </div>
                        <div class="text-right">
                             @if($item->stock_qty <= $item->alert_threshold)
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wide bg-rose-50 text-rose-600 border border-rose-100 shadow-sm">
                                    Low Stock
                                </span>
                            @else
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wide bg-emerald-50 text-emerald-600 border border-emerald-100 shadow-sm">
                                    In Stock
                                </span>
                            @endif
                            @if(!$item->is_sold)
                                <span class="ml-1 px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wide bg-slate-100 text-slate-500 border border-slate-200 shadow-sm">
                                    Not for Sale
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Stock Info -->
                    <div class="flex items-center gap-4 py-3 border-t border-b border-slate-50">
                        <div class="flex-1">
                            <p class="text-xs text-slate-500 uppercase font-bold tracking-wider mb-0.5">Available</p>
                            <p class="text-2xl font-black text-slate-800 tracking-tight">
                                {{ $item->stock_qty + 0 }} <span class="text-sm text-slate-400 font-medium">{{ $item->base_unit }}</span>
                            </p>
                        </div>
                        <div class="flex-1 border-l border-slate-100 pl-4">
                            <p class="text-xs text-slate-500 uppercase font-bold tracking-wider mb-0.5">Prices</p>
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-birawa-700" title="Selling Price">
                                    Rp {{ number_format($item->selling_price, 0, ',', '.') }}
                                </span>
                                <span class="text-[10px] text-slate-400" title="Average Cost">
                                    Cost: Rp {{ number_format($item->average_cost_price, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Footer: Actions -->
                    <div class="grid grid-cols-3 gap-2 mt-auto pt-3">
                        <a href="{{ route('inventory.restock', $item) }}" class="flex items-center justify-center px-3 py-2 bg-emerald-50 text-emerald-700 text-xs font-bold rounded-xl hover:bg-emerald-100 transition-colors border border-emerald-100 group/btn" title="Restock / Add Stock">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5 group-hover/btn:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Add
                        </a>
                        <button @click="showTransferModal = true; transferItem = { id: {{ $item->id }}, name: '{{ addslashes($item->item_name) }}', max: {{ $item->stock_qty }} }" class="flex items-center justify-center px-3 py-2 bg-indigo-50 text-indigo-700 text-xs font-bold rounded-xl hover:bg-indigo-100 transition-colors border border-indigo-100 group/btn" title="Transfer Stock">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5 group-hover/btn:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                            </svg>
                            Move
                        </button>
                        <a href="{{ route('inventory.edit', $item) }}" class="flex items-center justify-center px-3 py-2 bg-slate-50 text-slate-700 text-xs font-bold rounded-xl hover:bg-slate-100 transition-colors border border-slate-100" title="Edit Details">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-12 text-center bg-white rounded-2xl border border-dashed border-slate-200">
                    <div class="bg-slate-50 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800">No items in this location</h3>
                    <p class="text-slate-500 text-sm mt-1 mb-6">Start by adding items or transferring from another location.</p>
                    <form action="{{ route('inventory.create') }}" method="GET" class="inline-block">
                        <input type="hidden" name="storage_location_id" value="{{ $activeLocation->id }}">
                        <button type="submit" class="px-6 py-2 bg-birawa-600 text-white rounded-xl text-sm font-bold hover:bg-birawa-700 transition-colors shadow-lg shadow-birawa-100">
                            Add First Item
                        </button>
                    </form>
                </div>
            @endforelse
        </div>

        <div class="mt-6">
            {{ $items->appends(['search' => request('search'), 'location_id' => $activeLocation->id])->links() }}
        </div>

        <!-- Add Location Modal -->
        <div x-show="showLocationModal" 
             class="fixed inset-0 z-50 overflow-y-auto" 
             style="display: none;"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            
            <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" @click="showLocationModal = false"></div>

            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                    
                    <form :action="editingLocation ? '{{ route('storage-locations.index') }}/' + editingLocation.id : '{{ route('storage-locations.store') }}'" method="POST">
                        @csrf
                        <template x-if="editingLocation">
                            <input type="hidden" name="_method" value="PUT">
                        </template>
                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-birawa-100 sm:mx-0 sm:h-10 sm:w-10">
                                    <svg class="h-6 w-6 text-birawa-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72m-13.5 8.65h3.75a.75.75 0 00.75-.75V13.5a.75.75 0 00-.75-.75H6.75a.75.75 0 00-.75.75v3.75c0 .415.336.75.75.75z" />
                                    </svg>
                                </div>
                                <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                                    <h3 class="text-lg font-bold leading-6 text-slate-900" x-text="editingLocation ? 'Edit Storage Location' : 'Add Storage Location'"></h3>
                                    <div class="mt-4 space-y-4">
                                        <div>
                                            <label class="block text-sm font-bold text-slate-700 mb-1">Location Name</label>
                                            <input type="text" name="name" x-model="locationForm.name" required placeholder="e.g., Bag A, Emergency Kit" class="w-full rounded-xl border-slate-200 focus:border-birawa-500 focus:ring-birawa-500 text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-bold text-slate-700 mb-1">Type</label>
                                            <select name="type" x-model="locationForm.type" required class="w-full rounded-xl border-slate-200 focus:border-birawa-500 focus:ring-birawa-500 text-sm">
                                                <option value="warehouse">Warehouse (Gudang)</option>
                                                <option value="bag">Visit Bag (Tas)</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-bold text-slate-700 mb-1">Capacity (Optional)</label>
                                            <input type="number" name="capacity" x-model="locationForm.capacity" placeholder="Max items" class="w-full rounded-xl border-slate-200 focus:border-birawa-500 focus:ring-birawa-500 text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-bold text-slate-700 mb-1">Description</label>
                                            <textarea name="description" x-model="locationForm.description" rows="2" class="w-full rounded-xl border-slate-200 focus:border-birawa-500 focus:ring-birawa-500 text-sm"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-slate-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                            <button type="submit" class="inline-flex w-full justify-center rounded-xl bg-birawa-600 px-3 py-2 text-sm font-bold text-white shadow-sm hover:bg-birawa-500 sm:ml-3 sm:w-auto" x-text="editingLocation ? 'Update Location' : 'Create Location'"></button>
                            <button type="button" @click="showLocationModal = false" class="mt-3 inline-flex w-full justify-center rounded-xl bg-white px-3 py-2 text-sm font-bold text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 hover:bg-slate-50 sm:mt-0 sm:w-auto">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Transfer Modal -->
        <div x-show="showTransferModal" class="relative z-10" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: none;">
            <div class="fixed inset-0 bg-slate-500 bg-opacity-75 transition-opacity" @click="showTransferModal = false"></div>
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                        <form action="{{ route('internal-transfers.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="inventory_id" :value="transferItem?.id">
                            <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                                <div class="sm:flex sm:items-start">
                                    <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-indigo-100 sm:mx-0 sm:h-10 sm:w-10">
                                        <svg class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5" />
                                        </svg>
                                    </div>
                                    <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                                        <h3 class="text-base font-bold leading-6 text-slate-900" id="modal-title">Transfer Stock</h3>
                                        <div class="mt-2">
                                            <p class="text-sm text-slate-500 mb-4">
                                                Move <strong x-text="transferItem?.name"></strong> to another location.
                                            </p>
                                            
                                            <div class="space-y-4">
                                                <div>
                                                    <label class="block text-sm font-bold text-slate-700 mb-1">Destination Location</label>
                                                    <select name="target_location_id" required class="w-full rounded-xl border-slate-200 focus:border-birawa-500 focus:ring-birawa-500 text-sm">
                                                        @foreach($locations as $loc)
                                                            @if($loc->id !== $activeLocation->id)
                                                                <option value="{{ $loc->id }}">{{ $loc->name }} ({{ ucfirst($loc->type) }})</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-bold text-slate-700 mb-1">Quantity</label>
                                                    <div class="relative">
                                                        <input type="number" name="quantity" required min="0.01" step="0.01" :max="transferItem?.max" class="w-full rounded-xl border-slate-200 focus:border-birawa-500 focus:ring-birawa-500 text-sm pr-12">
                                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                                            <span class="text-slate-400 text-xs font-bold">MAX <span x-text="transferItem?.max"></span></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-slate-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                                <button type="submit" class="inline-flex w-full justify-center rounded-xl bg-indigo-600 px-3 py-2 text-sm font-bold text-white shadow-sm hover:bg-indigo-500 sm:ml-3 sm:w-auto">Transfer</button>
                                <button type="button" @click="showTransferModal = false" class="mt-3 inline-flex w-full justify-center rounded-xl bg-white px-3 py-2 text-sm font-bold text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 hover:bg-slate-50 sm:mt-0 sm:w-auto">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

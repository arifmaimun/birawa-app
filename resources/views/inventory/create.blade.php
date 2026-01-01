<x-app-layout>
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-slate-800">Add New Inventory Item</h1>
            <p class="text-sm text-slate-500">Register a new medical item or consumable</p>
        </div>

        <div class="bg-white rounded-3xl shadow-lg border border-slate-100 overflow-hidden">
            <div class="p-8">
                <form action="{{ route('inventory.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="storage_location_id" value="{{ request('storage_location_id') }}">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <!-- Item Name & Search -->
                        <div class="col-span-1 md:col-span-2" x-data="{
                            search: '',
                            open: false,
                            selected: null,
                            products: {{ $products->map(fn($p) => ['id' => $p->id, 'name' => $p->name, 'sku' => $p->sku, 'category' => $p->category, 'price' => $p->price])->toJson() }},
                            
                            init() {
                                this.search = '{{ old('item_name') }}';
                            },
                            
                            get filteredProducts() {
                                if (this.search === '') return [];
                                return this.products.filter(p => {
                                    return p.name.toLowerCase().includes(this.search.toLowerCase()) || 
                                           p.sku.toLowerCase().includes(this.search.toLowerCase());
                                });
                            },
                            
                            select(product) {
                                this.selected = product;
                                this.search = product.name;
                                this.open = false;
                                
                                // Populate other fields
                                document.getElementById('sku').value = product.sku;
                                document.getElementById('category').value = product.category;
                                document.getElementById('selling_price').value = product.price;
                                document.getElementById('product_id').value = product.id;
                            }
                        }" @click.away="open = false">

                            <label for="item_name" class="block text-sm font-bold text-slate-700 mb-2">Item Name (or Search Catalog)</label>
                            <div class="relative">
                                <input type="hidden" name="product_id" id="product_id" :value="selected ? selected.id : ''">
                                <input type="text" id="item_name" name="item_name" 
                                       x-model="search"
                                       @input="open = true; selected = null; document.getElementById('product_id').value = '';"
                                       @focus="open = true"
                                       class="w-full rounded-xl border-slate-200 focus:border-birawa-500 focus:ring-birawa-500 placeholder-slate-400 transition-colors shadow-sm"
                                       placeholder="Start typing to search catalog or enter new name..." required autocomplete="off">
                                
                                <!-- Dropdown -->
                                <div x-show="open && filteredProducts.length > 0" 
                                     class="absolute z-50 w-full mt-1 bg-white rounded-xl shadow-lg border border-slate-100 max-h-60 overflow-auto"
                                     style="display: none;">
                                    <template x-for="product in filteredProducts" :key="product.id">
                                        <div @click="select(product)" class="px-4 py-3 hover:bg-birawa-50 cursor-pointer border-b border-slate-50 last:border-0">
                                            <div class="flex justify-between items-center">
                                                <div>
                                                    <p class="text-sm font-bold text-slate-800" x-text="product.name"></p>
                                                    <p class="text-xs text-slate-500" x-text="product.sku"></p>
                                                </div>
                                                <span class="text-xs font-bold text-birawa-600 bg-birawa-50 px-2 py-1 rounded-lg" x-text="'Select'"></span>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            <p class="mt-1.5 text-xs text-slate-500" x-show="!selected">Creating a new item will automatically add it to the global Product Catalog.</p>
                            <p class="mt-1.5 text-xs text-birawa-600 font-bold" x-show="selected">Linked to existing Product in Catalog.</p>
                            <x-input-error :messages="$errors->get('item_name')" class="mt-2" />
                        </div>

                        <!-- Category -->
                        <div>
                            <label for="category" class="block text-sm font-bold text-slate-700 mb-2">Category</label>
                            <input type="text" id="category" name="category" 
                                   class="w-full rounded-xl border-slate-200 focus:border-birawa-500 focus:ring-birawa-500 placeholder-slate-400 transition-colors shadow-sm"
                                   value="{{ old('category') }}" placeholder="e.g. Medicine, Consumable">
                            <x-input-error :messages="$errors->get('category')" class="mt-2" />
                        </div>

                        <!-- SKU -->
                        <div>
                            <label for="sku" class="block text-sm font-bold text-slate-700 mb-2">SKU <span class="text-slate-400 font-normal">(Auto-generated if empty)</span></label>
                            <input type="text" id="sku" name="sku" 
                                   class="w-full rounded-xl border-slate-200 focus:border-birawa-500 focus:ring-birawa-500 placeholder-slate-400 transition-colors shadow-sm"
                                   value="{{ old('sku') }}" placeholder="Leave empty to auto-generate">
                            <x-input-error :messages="$errors->get('sku')" class="mt-2" />
                        </div>

                        <!-- Selling Price -->
                        <div>
                            <label for="selling_price" class="block text-sm font-bold text-slate-700 mb-2">Selling Price <span class="text-slate-400 font-normal">(Per Base Unit)</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-slate-500 font-bold sm:text-sm">Rp</span>
                                </div>
                                <input type="number" id="selling_price" name="selling_price" 
                                       class="w-full pl-10 rounded-xl border-slate-200 focus:border-birawa-500 focus:ring-birawa-500 placeholder-slate-400 transition-colors shadow-sm"
                                       value="{{ old('selling_price') }}" min="0" step="100" placeholder="0">
                            </div>
                            <p class="text-xs text-slate-500 mt-1">Leave 0 to auto-calculate (Cost + 20%)</p>
                            <x-input-error :messages="$errors->get('selling_price')" class="mt-2" />

                            <!-- Is Sold -->
                            <div class="flex items-center mt-4">
                                <input type="checkbox" id="is_sold" name="is_sold" value="1" 
                                       class="w-5 h-5 rounded border-slate-300 text-birawa-600 focus:ring-birawa-500"
                                       {{ old('is_sold', true) ? 'checked' : '' }}>
                                <label for="is_sold" class="ml-3 block text-sm font-bold text-slate-700">
                                    Sellable Item <span class="text-slate-400 font-normal block text-xs mt-0.5">Include this item in invoices when used</span>
                                </label>
                            </div>
                        </div>

                        <!-- Base Unit -->
                        <div>
                            <label for="base_unit" class="block text-sm font-bold text-slate-700 mb-2">Base Unit <span class="text-slate-400 font-normal">(Usage)</span></label>
                            <select id="base_unit" name="base_unit" class="w-full rounded-xl border-slate-200 focus:border-birawa-500 focus:ring-birawa-500 shadow-sm transition-colors cursor-pointer">
                                <option value="tablet">Tablet</option>
                                <option value="ml">ml</option>
                                <option value="gram">gram</option>
                                <option value="pcs">pcs</option>
                                <option value="capsule">capsule</option>
                                <option value="drop">drop</option>
                            </select>
                            <x-input-error :messages="$errors->get('base_unit')" class="mt-2" />
                        </div>

                        <!-- Purchase Unit -->
                        <div>
                            <label for="purchase_unit" class="block text-sm font-bold text-slate-700 mb-2">Purchase Unit <span class="text-slate-400 font-normal">(Stocking)</span></label>
                            <select id="purchase_unit" name="purchase_unit" class="w-full rounded-xl border-slate-200 focus:border-birawa-500 focus:ring-birawa-500 shadow-sm transition-colors cursor-pointer">
                                <option value="box">Box</option>
                                <option value="bottle">Bottle</option>
                                <option value="vial">Vial</option>
                                <option value="strip">Strip</option>
                                <option value="can">Can</option>
                                <option value="tube">Tube</option>
                            </select>
                            <x-input-error :messages="$errors->get('purchase_unit')" class="mt-2" />
                        </div>

                        <!-- Conversion Ratio -->
                        <div>
                            <label for="conversion_ratio" class="block text-sm font-bold text-slate-700 mb-2">Conversion Ratio</label>
                            <input type="number" id="conversion_ratio" name="conversion_ratio" 
                                   class="w-full rounded-xl border-slate-200 focus:border-birawa-500 focus:ring-birawa-500 placeholder-slate-400 transition-colors shadow-sm"
                                   value="{{ old('conversion_ratio') }}" required min="1" placeholder="e.g. 10">
                            <p class="mt-1.5 text-xs text-slate-500">
                                How many <span class="font-bold text-slate-700">Base Units</span> are in 1 <span class="font-bold text-slate-700">Purchase Unit</span>?
                            </p>
                            <x-input-error :messages="$errors->get('conversion_ratio')" class="mt-2" />
                        </div>

                        <!-- Alert Threshold -->
                        <div>
                            <label for="alert_threshold" class="block text-sm font-bold text-slate-700 mb-2">Low Stock Alert</label>
                            <input type="number" id="alert_threshold" name="alert_threshold" 
                                   class="w-full rounded-xl border-slate-200 focus:border-birawa-500 focus:ring-birawa-500 placeholder-slate-400 transition-colors shadow-sm"
                                   value="{{ old('alert_threshold', 10) }}" required min="0">
                            <p class="mt-1.5 text-xs text-slate-500">Alert when stock (in Base Units) falls below this.</p>
                            <x-input-error :messages="$errors->get('alert_threshold')" class="mt-2" />
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end gap-4 pt-6 border-t border-slate-100">
                        <a href="{{ route('inventory.index') }}" class="text-sm font-bold text-slate-500 hover:text-slate-800 transition-colors">
                            Cancel
                        </a>
                        <button type="submit" class="px-6 py-2.5 bg-birawa-600 text-white text-sm font-bold rounded-xl shadow-lg hover:bg-birawa-700 focus:ring-4 focus:ring-birawa-100 transition-all active:scale-95">
                            Save Item
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

<x-app-layout>
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-slate-800">Add New Product</h1>
            <p class="text-sm text-slate-500">Register a new product or service to the catalog</p>
        </div>

        <div class="bg-white rounded-3xl shadow-lg border border-slate-100 overflow-hidden">
            <div class="p-8">
                <form action="{{ route('products.store') }}" method="POST" x-data="productForm()" @submit="isSubmitting = true">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <!-- Product Type Toggle -->
                        <div class="md:col-span-2 mb-4">
                            <label class="block text-sm font-bold text-slate-700 mb-2">Item Type</label>
                            <div class="flex p-1 bg-slate-100 rounded-xl w-fit">
                                <button type="button" @click="type = 'goods'"
                                    :class="{'bg-white text-birawa-600 shadow-sm': type === 'goods', 'text-slate-500 hover:text-slate-700': type !== 'goods'}"
                                    class="px-6 py-2 text-sm font-bold rounded-lg transition-all">
                                    Product (Barang)
                                </button>
                                <button type="button" @click="type = 'service'"
                                    :class="{'bg-white text-birawa-600 shadow-sm': type === 'service', 'text-slate-500 hover:text-slate-700': type !== 'service'}"
                                    class="px-6 py-2 text-sm font-bold rounded-lg transition-all">
                                    Service (Jasa)
                                </button>
                            </div>
                            <input type="hidden" name="type" x-model="type">
                        </div>

                        <!-- Category -->
                        <div class="md:col-span-2">
                            <label for="category" class="block text-sm font-bold text-slate-700 mb-2">Category</label>
                            <div class="relative">
                                <input type="text" name="category" id="category" x-model="category" list="category-list"
                                    class="w-full rounded-xl border-slate-200 focus:border-birawa-500 focus:ring-birawa-500 placeholder-slate-400 transition-colors shadow-sm"
                                    placeholder="Select or type new category...">
                                <datalist id="category-list">
                                    @foreach($categories ?? [] as $cat)
                                        <option value="{{ $cat }}"></option>
                                    @endforeach
                                </datalist>
                            </div>
                            <p class="text-xs text-slate-500 mt-1">Used for grouping and SKU prefix.</p>
                            <x-input-error :messages="$errors->get('category')" class="mt-2" />
                        </div>

                        <!-- SKU -->
                        <div class="md:col-span-2 bg-slate-50 p-4 rounded-xl border border-slate-200">
                            <label class="block text-sm font-bold text-slate-700 mb-4">SKU Generation</label>
                            
                            <div class="flex items-center gap-6 mb-4">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="sku_mode" value="auto" x-model="skuMode" class="text-birawa-600 focus:ring-birawa-500">
                                    <span class="text-sm font-medium text-slate-700">Auto-generated</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="sku_mode" value="manual" x-model="skuMode" class="text-birawa-600 focus:ring-birawa-500">
                                    <span class="text-sm font-medium text-slate-700">Manual Input</span>
                                </label>
                            </div>

                            <div x-show="skuMode === 'auto'" class="text-sm text-slate-600">
                                <p>Format: <span class="font-mono font-bold text-birawa-600" x-text="generatePreview()"></span></p>
                                <p class="text-xs text-slate-500 mt-1">Sequence number will be assigned automatically based on category.</p>
                            </div>

                            <div x-show="skuMode === 'manual'">
                                <label for="sku" class="block text-sm font-bold text-slate-700 mb-2">Manual SKU</label>
                                <div class="relative">
                                    <input type="text" name="sku" id="sku" x-model="sku" @input.debounce.500ms="checkSku()"
                                           class="w-full rounded-xl border-slate-200 focus:border-birawa-500 focus:ring-birawa-500 placeholder-slate-400 transition-colors shadow-sm"
                                           :class="{'border-red-500 focus:border-red-500': skuError, 'border-green-500 focus:border-green-500': skuValid}"
                                           placeholder="e.g. PRD-001">
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <span x-show="checkingSku" class="text-slate-400 text-xs">Checking...</span>
                                        <span x-show="skuValid && !checkingSku" class="text-green-500">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        </span>
                                        <span x-show="skuError && !checkingSku" class="text-red-500">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </span>
                                    </div>
                                </div>
                                <p x-show="skuError" class="text-xs text-red-500 mt-1" x-text="skuErrorMessage"></p>
                                <x-input-error :messages="$errors->get('sku')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-sm font-bold text-slate-700 mb-2">Product/Service Name</label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" 
                                   class="w-full rounded-xl border-slate-200 focus:border-birawa-500 focus:ring-birawa-500 placeholder-slate-400 transition-colors shadow-sm"
                                   required placeholder="e.g. Vaccination Service">
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Stock -->
                        <div x-show="type === 'goods'">
                            <label for="stock" class="block text-sm font-bold text-slate-700 mb-2">Stock</label>
                            <input type="number" name="stock" id="stock" value="{{ old('stock', 0) }}" min="0" 
                                   class="w-full rounded-xl border-slate-200 focus:border-birawa-500 focus:ring-birawa-500 placeholder-slate-400 transition-colors shadow-sm">
                            <x-input-error :messages="$errors->get('stock')" class="mt-2" />
                        </div>
                        <div x-show="type === 'service'">
                             <input type="hidden" name="stock" value="0">
                        </div>

                        <!-- Cost (Harga Beli) -->
                        <div>
                            <label for="cost" class="block text-sm font-bold text-slate-700 mb-2">Cost (Harga Beli)</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-slate-500 font-bold sm:text-sm">Rp</span>
                                </div>
                                <input type="number" name="cost" id="cost" value="{{ old('cost', 0) }}" min="0" step="100" 
                                       class="w-full pl-10 rounded-xl border-slate-200 focus:border-birawa-500 focus:ring-birawa-500 placeholder-slate-400 transition-colors shadow-sm"
                                       required>
                            </div>
                            <x-input-error :messages="$errors->get('cost')" class="mt-2" />
                        </div>

                        <!-- Price (Harga Jual) -->
                        <div>
                            <label for="price" class="block text-sm font-bold text-slate-700 mb-2">Price (Harga Jual)</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-slate-500 font-bold sm:text-sm">Rp</span>
                                </div>
                                <input type="number" name="price" id="price" value="{{ old('price', 0) }}" min="0" step="100" 
                                       class="w-full pl-10 rounded-xl border-slate-200 focus:border-birawa-500 focus:ring-birawa-500 placeholder-slate-400 transition-colors shadow-sm"
                                       required>
                            </div>
                            <x-input-error :messages="$errors->get('price')" class="mt-2" />
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end gap-4 pt-6 border-t border-slate-100">
                        <a href="{{ route('products.index') }}" class="text-sm font-bold text-slate-500 hover:text-slate-800 transition-colors">
                            Cancel
                        </a>
                        <button type="submit" 
                            :disabled="isSubmitting"
                            :class="{'opacity-75 cursor-not-allowed': isSubmitting}"
                            class="px-6 py-2.5 bg-birawa-600 text-white text-sm font-bold rounded-xl shadow-lg hover:bg-birawa-700 focus:ring-4 focus:ring-birawa-100 transition-all active:scale-95 flex items-center gap-2">
                            <span x-show="isSubmitting" class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"></span>
                            <span x-text="isSubmitting ? 'Saving...' : 'Save Product'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('productForm', () => ({
                isSubmitting: false,
                type: 'goods',
                category: '',
                skuMode: 'auto',
                sku: '',
                checkingSku: false,
                skuValid: false,
                skuError: false,
                skuErrorMessage: '',

                generatePreview() {
                    let prefix = '';
                    if (this.category) {
                        prefix = this.category.substring(0, 3).toUpperCase().replace(/[^A-Z]/g, '');
                    }
                    
                    if (prefix.length < 3) {
                        if (!this.category) {
                             prefix = this.type === 'goods' ? 'PRD' : 'SVC';
                        } else {
                             prefix = prefix.padEnd(3, 'X');
                        }
                    }
                    
                    return `${prefix}-XXXX`;
                },

                checkSku() {
                    if (this.sku.length < 3) {
                        this.skuValid = false;
                        this.skuError = false;
                        this.skuErrorMessage = '';
                        return;
                    }

                    this.checkingSku = true;
                    this.skuValid = false;
                    this.skuError = false;

                    fetch(`{{ route('products.check-sku') }}?sku=${this.sku}`)
                        .then(response => response.json())
                        .then(data => {
                            this.checkingSku = false;
                            if (data.exists) {
                                this.skuError = true;
                                this.skuErrorMessage = 'SKU already exists.';
                            } else {
                                this.skuValid = true;
                            }
                        })
                        .catch(error => {
                            console.error('Error checking SKU:', error);
                            this.checkingSku = false;
                            this.skuError = true;
                            this.skuErrorMessage = 'Error validating SKU.';
                        });
                }
            }));
        });
    </script>
    @endpush
</x-app-layout>

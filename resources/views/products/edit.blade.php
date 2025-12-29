<x-app-layout>
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-slate-800">Edit Product</h1>
            <p class="text-sm text-slate-500">Update details for {{ $product->name }}</p>
        </div>

        <div class="bg-white rounded-3xl shadow-lg border border-slate-100 overflow-hidden">
            <div class="p-8">
                <form action="{{ route('products.update', $product) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <!-- Category -->
                        <div class="md:col-span-2">
                            <label for="category" class="block text-sm font-bold text-slate-700 mb-2">Category</label>
                            <input type="text" name="category" id="category" value="{{ old('category', $product->category) }}"
                                   class="w-full rounded-xl border-slate-200 focus:border-birawa-500 focus:ring-birawa-500 placeholder-slate-400 transition-colors shadow-sm"
                                   placeholder="e.g. VAKSIN, MAKANAN">
                            <x-input-error :messages="$errors->get('category')" class="mt-2" />
                        </div>

                        <!-- SKU -->
                        <div>
                            <label for="sku" class="block text-sm font-bold text-slate-700 mb-2">SKU</label>
                            <input type="text" name="sku" id="sku" value="{{ old('sku', $product->sku) }}" 
                                   class="w-full rounded-xl border-slate-200 focus:border-birawa-500 focus:ring-birawa-500 placeholder-slate-400 transition-colors shadow-sm"
                                   required>
                            <x-input-error :messages="$errors->get('sku')" class="mt-2" />
                        </div>

                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-sm font-bold text-slate-700 mb-2">Product Name</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}" 
                                   class="w-full rounded-xl border-slate-200 focus:border-birawa-500 focus:ring-birawa-500 placeholder-slate-400 transition-colors shadow-sm"
                                   required>
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Type (Hidden, default to barang) -->
                        <input type="hidden" name="type" value="barang">

                        <!-- Stock -->
                        <div>
                            <label for="stock" class="block text-sm font-bold text-slate-700 mb-2">Stock</label>
                            <input type="number" name="stock" id="stock" value="{{ old('stock', $product->stock) }}" min="0" 
                                   class="w-full rounded-xl border-slate-200 focus:border-birawa-500 focus:ring-birawa-500 placeholder-slate-400 transition-colors shadow-sm"
                                   required>
                            <x-input-error :messages="$errors->get('stock')" class="mt-2" />
                        </div>

                        <!-- Cost (Harga Beli) -->
                        <div>
                            <label for="cost" class="block text-sm font-bold text-slate-700 mb-2">Cost (Harga Beli)</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-slate-500 font-bold sm:text-sm">Rp</span>
                                </div>
                                <input type="number" name="cost" id="cost" value="{{ old('cost', $product->cost) }}" min="0" step="100" 
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
                                <input type="number" name="price" id="price" value="{{ old('price', $product->price) }}" min="0" step="100" 
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
                        <button type="submit" class="px-6 py-2.5 bg-birawa-600 text-white text-sm font-bold rounded-xl shadow-lg hover:bg-birawa-700 focus:ring-4 focus:ring-birawa-100 transition-all active:scale-95">
                            Update Product
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

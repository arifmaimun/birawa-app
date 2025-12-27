<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Product') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('products.update', $product) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- SKU -->
                            <div>
                                <label for="sku" class="block text-sm font-medium text-gray-700">SKU</label>
                                <input type="text" name="sku" id="sku" value="{{ old('sku', $product->sku) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-birawa-500 focus:ring focus:ring-birawa-500 focus:ring-opacity-50" required>
                                @error('sku')
                                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Name -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                                <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-birawa-500 focus:ring focus:ring-birawa-500 focus:ring-opacity-50" required>
                                @error('name')
                                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Type -->
                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700">Type</label>
                                <select name="type" id="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-birawa-500 focus:ring focus:ring-birawa-500 focus:ring-opacity-50" required>
                                    <option value="">Select Type</option>
                                    <option value="barang" {{ old('type', $product->type) == 'barang' ? 'selected' : '' }}>Barang (Goods)</option>
                                    <option value="jasa" {{ old('type', $product->type) == 'jasa' ? 'selected' : '' }}>Jasa (Service)</option>
                                </select>
                                @error('type')
                                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Stock -->
                            <div>
                                <label for="stock" class="block text-sm font-medium text-gray-700">Stock</label>
                                <input type="number" name="stock" id="stock" value="{{ old('stock', $product->stock) }}" min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-birawa-500 focus:ring focus:ring-birawa-500 focus:ring-opacity-50" required>
                                @error('stock')
                                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Cost (Harga Beli) -->
                            <div>
                                <label for="cost" class="block text-sm font-medium text-gray-700">Cost (Harga Beli)</label>
                                <input type="number" name="cost" id="cost" value="{{ old('cost', $product->cost) }}" min="0" step="100" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-birawa-500 focus:ring focus:ring-birawa-500 focus:ring-opacity-50" required>
                                @error('cost')
                                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Price (Harga Jual) -->
                            <div>
                                <label for="price" class="block text-sm font-medium text-gray-700">Price (Harga Jual)</label>
                                <input type="number" name="price" id="price" value="{{ old('price', $product->price) }}" min="0" step="100" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-birawa-500 focus:ring focus:ring-birawa-500 focus:ring-opacity-50" required>
                                @error('price')
                                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('products.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900 mr-4">
                                Cancel
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-birawa-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-birawa-500 active:bg-birawa-700 focus:outline-none focus:border-birawa-700 focus:ring focus:ring-birawa-300 disabled:opacity-25 transition">
                                Simpan Data
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

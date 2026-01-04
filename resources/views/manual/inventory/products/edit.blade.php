<x-manual.layouts.app>
    <x-slot name="header">
        Edit Product: {{ $product->name }}
    </x-slot>

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <form method="POST" action="{{ route('manual.products.update', $product) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Name -->
                        <div>
                            <x-manual.input-label for="name" value="Product Name" />
                            <x-manual.text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $product->name)" required autofocus />
                            <x-manual.input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Type -->
                        <div>
                            <x-manual.input-label for="type" value="Type" />
                            <select id="type" name="type" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="goods" {{ old('type', $product->type) == 'goods' ? 'selected' : '' }}>Goods</option>
                                <option value="service" {{ old('type', $product->type) == 'service' ? 'selected' : '' }}>Service</option>
                            </select>
                            <x-manual.input-error :messages="$errors->get('type')" class="mt-2" />
                        </div>

                        <!-- Category -->
                        <div>
                            <x-manual.input-label for="category" value="Category" />
                            <input list="categories" id="category" name="category" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" value="{{ old('category', $product->category) }}">
                            <datalist id="categories">
                                @foreach($categories as $category)
                                    <option value="{{ $category }}">
                                @endforeach
                            </datalist>
                            <x-manual.input-error :messages="$errors->get('category')" class="mt-2" />
                        </div>

                        <!-- SKU -->
                        <div>
                            <x-manual.input-label for="sku" value="SKU Code" />
                            <x-manual.text-input id="sku" class="block mt-1 w-full bg-gray-100" type="text" name="sku" :value="old('sku', $product->sku)" readonly />
                            <p class="text-xs text-gray-500 mt-1">SKU cannot be changed to maintain data integrity.</p>
                            <x-manual.input-error :messages="$errors->get('sku')" class="mt-2" />
                        </div>

                        <!-- Cost -->
                        <div>
                            <x-manual.input-label for="cost" value="Cost (HPP)" />
                            <x-manual.text-input id="cost" class="block mt-1 w-full" type="number" name="cost" :value="old('cost', $product->cost)" required />
                            <x-manual.input-error :messages="$errors->get('cost')" class="mt-2" />
                        </div>

                        <!-- Price -->
                        <div>
                            <x-manual.input-label for="price" value="Selling Price" />
                            <x-manual.text-input id="price" class="block mt-1 w-full" type="number" name="price" :value="old('price', $product->price)" required />
                            <x-manual.input-error :messages="$errors->get('price')" class="mt-2" />
                        </div>

                        <!-- Stock -->
                        <div>
                            <x-manual.input-label for="stock" value="Stock" />
                            <x-manual.text-input id="stock" class="block mt-1 w-full" type="number" name="stock" :value="old('stock', $product->stock)" required />
                            <x-manual.input-error :messages="$errors->get('stock')" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex items-center justify-between mt-8">
                        <!-- Delete Button -->
                        <button type="button" onclick="confirmDelete()" class="text-red-600 hover:text-red-900 text-sm font-medium">
                            Delete Product
                        </button>

                        <div class="flex items-center">
                            <a href="{{ route('manual.products.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">Cancel</a>
                            <x-manual.primary-button class="ml-4">
                                {{ __('Update Product') }}
                            </x-manual.primary-button>
                        </div>
                    </div>
                </form>

                <form id="delete-form" action="{{ route('manual.products.destroy', $product) }}" method="POST" class="hidden">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>
    </div>

    <script>
        function confirmDelete() {
            if (confirm('Are you sure you want to delete this product? This action cannot be undone.')) {
                document.getElementById('delete-form').submit();
            }
        }
    </script>
</x-manual.layouts.app>

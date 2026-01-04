<x-manual.layouts.app>
    <x-slot name="header">
        Create Product
    </x-slot>

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <form method="POST" action="{{ route('manual.products.store') }}">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Name -->
                        <div>
                            <x-manual.input-label for="name" value="Product Name" />
                            <x-manual.text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                            <x-manual.input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Type -->
                        <div>
                            <x-manual.input-label for="type" value="Type" />
                            <select id="type" name="type" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="goods" {{ old('type') == 'goods' ? 'selected' : '' }}>Goods</option>
                                <option value="service" {{ old('type') == 'service' ? 'selected' : '' }}>Service</option>
                            </select>
                            <x-manual.input-error :messages="$errors->get('type')" class="mt-2" />
                        </div>

                        <!-- Category -->
                        <div>
                            <x-manual.input-label for="category" value="Category" />
                            <input list="categories" id="category" name="category" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" value="{{ old('category') }}">
                            <datalist id="categories">
                                @foreach($categories as $category)
                                    <option value="{{ $category }}">
                                @endforeach
                            </datalist>
                            <x-manual.input-error :messages="$errors->get('category')" class="mt-2" />
                        </div>

                        <!-- SKU Mode -->
                        <div>
                            <x-manual.input-label for="sku_mode" value="SKU Generation" />
                            <select id="sku_mode" name="sku_mode" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" onchange="toggleSkuInput()">
                                <option value="auto" {{ old('sku_mode', 'auto') == 'auto' ? 'selected' : '' }}>Automatic</option>
                                <option value="manual" {{ old('sku_mode') == 'manual' ? 'selected' : '' }}>Manual Input</option>
                            </select>
                        </div>

                        <!-- SKU Input (Hidden by default) -->
                        <div id="sku_container" class="{{ old('sku_mode', 'auto') == 'auto' ? 'hidden' : '' }}">
                            <x-manual.input-label for="sku" value="SKU Code" />
                            <x-manual.text-input id="sku" class="block mt-1 w-full" type="text" name="sku" :value="old('sku')" />
                            <x-manual.input-error :messages="$errors->get('sku')" class="mt-2" />
                        </div>

                        <!-- Cost -->
                        <div>
                            <x-manual.input-label for="cost" value="Cost (HPP)" />
                            <x-manual.text-input id="cost" class="block mt-1 w-full" type="number" name="cost" :value="old('cost', 0)" required />
                            <x-manual.input-error :messages="$errors->get('cost')" class="mt-2" />
                        </div>

                        <!-- Price -->
                        <div>
                            <x-manual.input-label for="price" value="Selling Price" />
                            <x-manual.text-input id="price" class="block mt-1 w-full" type="number" name="price" :value="old('price', 0)" required />
                            <x-manual.input-error :messages="$errors->get('price')" class="mt-2" />
                        </div>

                        <!-- Stock -->
                        <div>
                            <x-manual.input-label for="stock" value="Initial Stock" />
                            <x-manual.text-input id="stock" class="block mt-1 w-full" type="number" name="stock" :value="old('stock', 0)" required />
                            <x-manual.input-error :messages="$errors->get('stock')" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-6">
                        <a href="{{ route('manual.products.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">Cancel</a>
                        <x-manual.primary-button class="ml-4">
                            {{ __('Create Product') }}
                        </x-manual.primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function toggleSkuInput() {
            const mode = document.getElementById('sku_mode').value;
            const container = document.getElementById('sku_container');
            if (mode === 'manual') {
                container.classList.remove('hidden');
            } else {
                container.classList.add('hidden');
            }
        }
    </script>
</x-manual.layouts.app>

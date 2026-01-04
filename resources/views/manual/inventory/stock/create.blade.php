<x-manual.layouts.app>
    <x-slot name="header">
        Add Inventory Item
    </x-slot>

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <form method="POST" action="{{ route('manual.inventory.store') }}">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Product -->
                        <div>
                            <x-manual.input-label for="product_id" value="Select Product" />
                            <select id="product_id" name="product_id" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                <option value="">-- Choose Product --</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                        {{ $product->name }} ({{ $product->sku }})
                                    </option>
                                @endforeach
                            </select>
                            <x-manual.input-error :messages="$errors->get('product_id')" class="mt-2" />
                        </div>

                        <!-- Location -->
                        <div>
                            <x-manual.input-label for="storage_location_id" value="Storage Location" />
                            <select id="storage_location_id" name="storage_location_id" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                <option value="">-- Choose Location --</option>
                                @foreach($locations as $location)
                                    <option value="{{ $location->id }}" {{ old('storage_location_id') == $location->id ? 'selected' : '' }}>
                                        {{ $location->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-manual.input-error :messages="$errors->get('storage_location_id')" class="mt-2" />
                        </div>

                        <!-- Initial Stock -->
                        <div>
                            <x-manual.input-label for="stock_qty" value="Initial Stock Quantity" />
                            <x-manual.text-input id="stock_qty" class="block mt-1 w-full" type="number" name="stock_qty" :value="old('stock_qty', 0)" required min="0" />
                            <x-manual.input-error :messages="$errors->get('stock_qty')" class="mt-2" />
                        </div>

                        <!-- Min Stock Level -->
                        <div>
                            <x-manual.input-label for="min_stock_level" value="Minimum Stock Alert Level" />
                            <x-manual.text-input id="min_stock_level" class="block mt-1 w-full" type="number" name="min_stock_level" :value="old('min_stock_level', 5)" min="0" />
                            <x-manual.input-error :messages="$errors->get('min_stock_level')" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-6">
                        <a href="{{ route('manual.inventory.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">Cancel</a>
                        <x-manual.primary-button class="ml-4">
                            {{ __('Add to Inventory') }}
                        </x-manual.primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-manual.layouts.app>

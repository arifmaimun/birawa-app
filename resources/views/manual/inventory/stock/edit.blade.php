<x-manual.layouts.app>
    <x-slot name="header">
        Adjust Stock: {{ $inventory->item_name }}
    </x-slot>

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <form method="POST" action="{{ route('manual.inventory.update', $inventory) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Product (Readonly) -->
                        <div>
                            <x-manual.input-label for="item_name" value="Product" />
                            <x-manual.text-input id="item_name" class="block mt-1 w-full bg-gray-100" type="text" :value="$inventory->item_name" readonly />
                        </div>

                        <!-- Location -->
                        <div>
                            <x-manual.input-label for="storage_location_id" value="Storage Location" />
                            <select id="storage_location_id" name="storage_location_id" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                @foreach($locations as $location)
                                    <option value="{{ $location->id }}" {{ old('storage_location_id', $inventory->storage_location_id) == $location->id ? 'selected' : '' }}>
                                        {{ $location->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-manual.input-error :messages="$errors->get('storage_location_id')" class="mt-2" />
                        </div>

                        <!-- Stock -->
                        <div>
                            <x-manual.input-label for="stock_qty" value="Current Stock" />
                            <x-manual.text-input id="stock_qty" class="block mt-1 w-full" type="number" name="stock_qty" :value="old('stock_qty', $inventory->stock_qty)" required min="0" />
                            <p class="text-xs text-gray-500 mt-1">Directly adjusting stock level.</p>
                            <x-manual.input-error :messages="$errors->get('stock_qty')" class="mt-2" />
                        </div>

                        <!-- Min Stock Level -->
                        <div>
                            <x-manual.input-label for="min_stock_level" value="Minimum Stock Alert Level" />
                            <x-manual.text-input id="min_stock_level" class="block mt-1 w-full" type="number" name="min_stock_level" :value="old('min_stock_level', $inventory->min_stock_level)" min="0" />
                            <x-manual.input-error :messages="$errors->get('min_stock_level')" class="mt-2" />
                        </div>

                         <!-- Selling Price -->
                         <div>
                            <x-manual.input-label for="selling_price" value="Selling Price" />
                            <x-manual.text-input id="selling_price" class="block mt-1 w-full" type="number" name="selling_price" :value="old('selling_price', $inventory->selling_price)" min="0" />
                            <x-manual.input-error :messages="$errors->get('selling_price')" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-6">
                        <a href="{{ route('manual.inventory.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">Cancel</a>
                        <x-manual.primary-button class="ml-4">
                            {{ __('Update Stock') }}
                        </x-manual.primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-manual.layouts.app>

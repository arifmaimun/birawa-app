<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Add New Inventory Item') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('inventory.store') }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="item_name" :value="__('Item Name')" />
                                <x-text-input id="item_name" class="block mt-1 w-full" type="text" name="item_name" :value="old('item_name')" required autofocus />
                                <x-input-error :messages="$errors->get('item_name')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="sku" :value="__('SKU (Optional)')" />
                                <x-text-input id="sku" class="block mt-1 w-full" type="text" name="sku" :value="old('sku')" />
                                <x-input-error :messages="$errors->get('sku')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="base_unit" :value="__('Base Unit (Usage)')" />
                                <select id="base_unit" name="base_unit" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="tablet">Tablet</option>
                                    <option value="ml">ml</option>
                                    <option value="gram">gram</option>
                                    <option value="pcs">pcs</option>
                                    <option value="capsule">capsule</option>
                                    <option value="drop">drop</option>
                                </select>
                                <x-input-error :messages="$errors->get('base_unit')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="purchase_unit" :value="__('Purchase Unit (Stocking)')" />
                                <select id="purchase_unit" name="purchase_unit" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="box">Box</option>
                                    <option value="bottle">Bottle</option>
                                    <option value="vial">Vial</option>
                                    <option value="strip">Strip</option>
                                    <option value="can">Can</option>
                                    <option value="tube">Tube</option>
                                </select>
                                <x-input-error :messages="$errors->get('purchase_unit')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="conversion_ratio" :value="__('Conversion Ratio (Base Units per Purchase Unit)')" />
                                <x-text-input id="conversion_ratio" class="block mt-1 w-full" type="number" name="conversion_ratio" :value="old('conversion_ratio', 1)" required min="1" />
                                <p class="text-sm text-gray-500 mt-1">Example: 1 Box = 100 Tablets</p>
                                <x-input-error :messages="$errors->get('conversion_ratio')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="min_stock_alert" :value="__('Low Stock Alert Threshold (Base Unit)')" />
                                <x-text-input id="min_stock_alert" class="block mt-1 w-full" type="number" name="min_stock_alert" :value="old('min_stock_alert', 10)" required min="0" />
                                <x-input-error :messages="$errors->get('min_stock_alert')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ml-4">
                                {{ __('Save Item') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

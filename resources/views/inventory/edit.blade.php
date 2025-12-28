<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Inventory Item') }}: {{ $doctorInventory->item_name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('inventory.update', $doctorInventory) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="item_name" :value="__('Item Name')" />
                                <x-text-input id="item_name" class="block mt-1 w-full" type="text" name="item_name" :value="old('item_name', $doctorInventory->item_name)" required autofocus />
                                <x-input-error :messages="$errors->get('item_name')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="sku" :value="__('SKU (Optional)')" />
                                <x-text-input id="sku" class="block mt-1 w-full" type="text" name="sku" :value="old('sku', $doctorInventory->sku)" />
                                <x-input-error :messages="$errors->get('sku')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="base_unit" :value="__('Base Unit (Usage)')" />
                                <select id="base_unit" name="base_unit" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    @foreach(['tablet', 'ml', 'gram', 'pcs', 'capsule', 'drop'] as $unit)
                                        <option value="{{ $unit }}" {{ $doctorInventory->base_unit == $unit ? 'selected' : '' }}>{{ ucfirst($unit) }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('base_unit')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="purchase_unit" :value="__('Purchase Unit (Stocking)')" />
                                <select id="purchase_unit" name="purchase_unit" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    @foreach(['box', 'bottle', 'vial', 'strip', 'can', 'tube'] as $unit)
                                        <option value="{{ $unit }}" {{ $doctorInventory->purchase_unit == $unit ? 'selected' : '' }}>{{ ucfirst($unit) }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('purchase_unit')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="conversion_ratio" :value="__('Conversion Ratio')" />
                                <x-text-input id="conversion_ratio" class="block mt-1 w-full" type="number" name="conversion_ratio" :value="old('conversion_ratio', $doctorInventory->conversion_ratio)" required min="1" />
                                <x-input-error :messages="$errors->get('conversion_ratio')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="alert_threshold" :value="__('Low Stock Alert Threshold')" />
                                <x-text-input id="alert_threshold" class="block mt-1 w-full" type="number" name="alert_threshold" :value="old('alert_threshold', $doctorInventory->alert_threshold)" required min="0" />
                                <x-input-error :messages="$errors->get('alert_threshold')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ml-4">
                                {{ __('Update Item') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Restock Inventory Item') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900">{{ $doctorInventory->item_name }}</h3>
                        <p class="mt-1 text-sm text-gray-600">
                            Current Stock: <span class="font-bold">{{ $doctorInventory->stock_qty }} {{ $doctorInventory->base_unit }}</span>
                        </p>
                        <p class="text-sm text-gray-600">
                            Purchase Config: 1 {{ $doctorInventory->purchase_unit }} = {{ $doctorInventory->conversion_ratio }} {{ $doctorInventory->base_unit }}
                        </p>
                    </div>

                    <form action="{{ route('inventory.restock.store', $doctorInventory) }}" method="POST">
                        @csrf

                        <!-- Quantity (Purchase Unit) -->
                        <div class="mb-4">
                            <label for="quantity_purchase_unit" class="block text-sm font-medium text-gray-700">
                                Quantity to Buy (in {{ $doctorInventory->purchase_unit }})
                            </label>
                            <input type="number" step="0.1" name="quantity_purchase_unit" id="quantity_purchase_unit" 
                                class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                required min="0.1">
                            <p class="mt-1 text-xs text-gray-500">
                                You will receive <span id="preview_base_qty">0</span> {{ $doctorInventory->base_unit }}.
                            </p>
                        </div>

                        <!-- Cost per Purchase Unit -->
                        <div class="mb-4">
                            <label for="cost_per_purchase_unit" class="block text-sm font-medium text-gray-700">
                                Cost per {{ $doctorInventory->purchase_unit }} (Rp)
                            </label>
                            <input type="number" step="0.01" name="cost_per_purchase_unit" id="cost_per_purchase_unit" 
                                class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                required min="0">
                        </div>

                        <!-- Total Cost Preview -->
                        <div class="mb-6 bg-gray-50 p-4 rounded-md">
                            <p class="text-sm font-medium text-gray-700">Summary:</p>
                            <p class="text-sm text-gray-600">Total Cost: Rp <span id="preview_total_cost">0</span></p>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('inventory.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">Cancel</a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                                Confirm Restock
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const qtyInput = document.getElementById('quantity_purchase_unit');
            const costInput = document.getElementById('cost_per_purchase_unit');
            const previewBaseQty = document.getElementById('preview_base_qty');
            const previewTotalCost = document.getElementById('preview_total_cost');
            const conversionRatio = {{ $doctorInventory->conversion_ratio }};

            function updatePreview() {
                const qty = parseFloat(qtyInput.value) || 0;
                const cost = parseFloat(costInput.value) || 0;

                const baseQty = qty * conversionRatio;
                const totalCost = qty * cost;

                previewBaseQty.textContent = baseQty.toLocaleString();
                previewTotalCost.textContent = totalCost.toLocaleString('id-ID');
            }

            qtyInput.addEventListener('input', updatePreview);
            costInput.addEventListener('input', updatePreview);
        });
    </script>
</x-app-layout>

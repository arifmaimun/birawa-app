<x-app-layout>
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-slate-800">Restock Inventory</h1>
            <p class="text-sm text-slate-500">Add stock for {{ $doctorInventory->item_name }}</p>
        </div>

        <div class="bg-white rounded-3xl shadow-lg border border-slate-100 overflow-hidden">
            <div class="p-8">
                <!-- Current Status -->
                <div class="bg-slate-50 rounded-2xl p-6 mb-8 border border-slate-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Current Stock</p>
                        <p class="text-2xl font-bold text-slate-800">{{ $doctorInventory->stock_qty }} <span class="text-sm font-medium text-slate-500">{{ $doctorInventory->base_unit }}</span></p>
                    </div>
                    <div class="text-left md:text-right">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Configuration</p>
                        <p class="text-sm font-medium text-slate-600">
                            1 <span class="font-bold text-slate-800">{{ $doctorInventory->purchase_unit }}</span> = 
                            <span class="font-bold text-slate-800">{{ $doctorInventory->conversion_ratio }}</span> {{ $doctorInventory->base_unit }}
                        </p>
                    </div>
                </div>

                <form action="{{ route('inventory.restock.store', $doctorInventory) }}" method="POST">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <!-- Quantity -->
                        <div>
                            <label for="quantity_purchase_unit" class="block text-sm font-bold text-slate-700 mb-2">
                                Quantity to Buy <span class="text-slate-400 font-normal">(in {{ $doctorInventory->purchase_unit }})</span>
                            </label>
                            <input type="number" step="0.1" name="quantity_purchase_unit" id="quantity_purchase_unit" 
                                   class="w-full rounded-xl border-slate-200 focus:border-birawa-500 focus:ring-birawa-500 placeholder-slate-400 transition-colors shadow-sm"
                                   required min="0.1" placeholder="e.g. 5">
                            <p class="mt-2 text-xs text-slate-500 flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                You will receive <span id="preview_base_qty" class="font-bold text-birawa-600 mx-1">0</span> {{ $doctorInventory->base_unit }}
                            </p>
                        </div>

                        <!-- Cost -->
                        <div>
                            <label for="cost_per_purchase_unit" class="block text-sm font-bold text-slate-700 mb-2">
                                Cost per {{ $doctorInventory->purchase_unit }} <span class="text-slate-400 font-normal">(Rp)</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-slate-500 font-bold sm:text-sm">Rp</span>
                                </div>
                                <input type="number" step="0.01" name="cost_per_purchase_unit" id="cost_per_purchase_unit" 
                                       class="w-full pl-10 rounded-xl border-slate-200 focus:border-birawa-500 focus:ring-birawa-500 placeholder-slate-400 transition-colors shadow-sm"
                                       required min="0" placeholder="0">
                            </div>
                        </div>
                    </div>

                    <!-- Summary Card -->
                    <div class="bg-birawa-50 rounded-2xl p-6 mb-8 border border-birawa-100">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-bold text-birawa-800">Total Cost Estimate</span>
                            <span class="text-xl font-bold text-birawa-700">Rp <span id="preview_total_cost">0</span></span>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end gap-4 pt-6 border-t border-slate-100">
                        <a href="{{ route('inventory.index') }}" class="text-sm font-bold text-slate-500 hover:text-slate-800 transition-colors">
                            Cancel
                        </a>
                        <button type="submit" class="px-6 py-2.5 bg-birawa-600 text-white text-sm font-bold rounded-xl shadow-lg hover:bg-birawa-700 focus:ring-4 focus:ring-birawa-100 transition-all active:scale-95">
                            Confirm Restock
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const qtyInput = document.getElementById('quantity_purchase_unit');
            const costInput = document.getElementById('cost_per_purchase_unit');
            const previewBaseQty = document.getElementById('preview_base_qty');
            const previewTotalCost = document.getElementById('preview_total_cost');
            const conversionRatio = Number("{{ $doctorInventory->conversion_ratio ?? 1 }}");

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

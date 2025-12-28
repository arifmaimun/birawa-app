<x-app-layout>
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-slate-800">Adjust Stock Level</h1>
            <p class="text-sm text-slate-500">Correct inventory count for {{ $doctorInventory->item_name }}</p>
        </div>

        <div class="bg-white rounded-3xl shadow-lg border border-slate-100 overflow-hidden">
            <div class="p-8">
                <!-- Current Status -->
                <div class="bg-slate-50 rounded-2xl p-6 mb-8 border border-slate-100 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">System Stock</p>
                        <p class="text-2xl font-bold text-slate-800">{{ $doctorInventory->stock_qty }} <span class="text-sm font-medium text-slate-500">{{ $doctorInventory->base_unit }}</span></p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Avg Cost</p>
                        <p class="text-sm font-bold text-slate-600">Rp {{ number_format($doctorInventory->average_cost_price, 0, ',', '.') }}</p>
                    </div>
                </div>

                <form action="{{ route('inventory.adjust.store', $doctorInventory) }}" method="POST">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <!-- Actual Stock -->
                        <div>
                            <label for="actual_stock" class="block text-sm font-bold text-slate-700 mb-2">
                                Actual Physical Stock <span class="text-slate-400 font-normal">(in {{ $doctorInventory->base_unit }})</span>
                            </label>
                            <input type="number" step="0.01" name="actual_stock" id="actual_stock" 
                                   class="w-full rounded-xl border-slate-200 focus:border-birawa-500 focus:ring-birawa-500 placeholder-slate-400 transition-colors shadow-sm"
                                   required min="0" value="{{ old('actual_stock', $doctorInventory->stock_qty) }}">
                            <p id="diff_preview" class="mt-2 text-xs font-bold text-slate-500">
                                Difference: <span class="text-slate-800">0</span>
                            </p>
                        </div>

                        <!-- Reason -->
                        <div>
                            <label for="reason" class="block text-sm font-bold text-slate-700 mb-2">Reason for Adjustment</label>
                            <select id="reason" name="reason" class="w-full rounded-xl border-slate-200 focus:border-birawa-500 focus:ring-birawa-500 shadow-sm transition-colors cursor-pointer">
                                <option value="Stock Opname / Correction">Stock Opname / Correction</option>
                                <option value="Damaged / Broken">Damaged / Broken</option>
                                <option value="Expired">Expired</option>
                                <option value="Theft / Lost">Theft / Lost</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>

                        <!-- Notes -->
                        <div class="col-span-1 md:col-span-2">
                            <label for="notes" class="block text-sm font-bold text-slate-700 mb-2">Additional Notes <span class="text-slate-400 font-normal">(Optional)</span></label>
                            <textarea id="notes" name="notes" rows="3" 
                                      class="w-full rounded-xl border-slate-200 focus:border-birawa-500 focus:ring-birawa-500 placeholder-slate-400 transition-colors shadow-sm"
                                      placeholder="Explain why the stock is being adjusted..."></textarea>
                        </div>
                    </div>

                    <!-- Warning Message (Hidden by default) -->
                    <div id="loss_warning" class="hidden bg-rose-50 border border-rose-100 rounded-2xl p-4 mb-8 flex items-start gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-rose-500 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <div>
                            <h4 class="text-sm font-bold text-rose-700">Stock Reduction Warning</h4>
                            <p class="text-xs text-rose-600 mt-1">
                                Reducing stock will be recorded as a <span class="font-bold">Loss Expense</span> of approximately 
                                <span id="loss_amount" class="font-bold">Rp 0</span> based on average cost.
                            </p>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end gap-4 pt-6 border-t border-slate-100">
                        <a href="{{ route('inventory.index') }}" class="text-sm font-bold text-slate-500 hover:text-slate-800 transition-colors">
                            Cancel
                        </a>
                        <button type="submit" class="px-6 py-2.5 bg-birawa-600 text-white text-sm font-bold rounded-xl shadow-lg hover:bg-birawa-700 focus:ring-4 focus:ring-birawa-100 transition-all active:scale-95">
                            Update Stock
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const actualInput = document.getElementById('actual_stock');
            const diffPreview = document.getElementById('diff_preview');
            const lossWarning = document.getElementById('loss_warning');
            const lossAmountSpan = document.getElementById('loss_amount');
            
            const currentStock = Number("{{ $doctorInventory->stock_qty ?? 0 }}");
            const avgCost = Number("{{ $doctorInventory->average_cost_price ?? 0 }}");

            function updatePreview() {
                const actual = parseFloat(actualInput.value) || 0;
                const diff = actual - currentStock;
                
                let diffText = '';
                let diffClass = '';

                if (diff > 0) {
                    diffText = '+' + diff.toLocaleString() + ' (Found)';
                    diffClass = 'text-emerald-600';
                    lossWarning.classList.add('hidden');
                } else if (diff < 0) {
                    diffText = diff.toLocaleString() + ' (Loss)';
                    diffClass = 'text-rose-600';
                    
                    // Show warning
                    const lossValue = Math.abs(diff) * avgCost;
                    lossAmountSpan.textContent = 'Rp ' + lossValue.toLocaleString('id-ID');
                    lossWarning.classList.remove('hidden');
                } else {
                    diffText = 'No Change';
                    diffClass = 'text-slate-500';
                    lossWarning.classList.add('hidden');
                }

                diffPreview.innerHTML = 'Difference: <span class="' + diffClass + '">' + diffText + '</span>';
            }

            actualInput.addEventListener('input', updatePreview);
        });
    </script>
</x-app-layout>
<x-app-layout>
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-slate-800">Record Expense</h1>
            <p class="text-sm text-slate-500">Add a new operational or capital expenditure</p>
        </div>

        <div class="bg-white rounded-3xl shadow-lg border border-slate-100 overflow-hidden">
            <div class="p-8">
                <form action="{{ route('expenses.store') }}" method="POST">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <!-- Transaction Date -->
                        <div>
                            <label for="transaction_date" class="block text-sm font-bold text-slate-700 mb-2">Transaction Date</label>
                            <input type="date" id="transaction_date" name="transaction_date" 
                                   class="w-full rounded-xl border-slate-200 focus:border-birawa-500 focus:ring-birawa-500 placeholder-slate-400 transition-colors shadow-sm"
                                   value="{{ old('transaction_date', date('Y-m-d')) }}" required>
                            <x-input-error :messages="$errors->get('transaction_date')" class="mt-2" />
                        </div>

                        <!-- Type -->
                        <div>
                            <label for="type" class="block text-sm font-bold text-slate-700 mb-2">Expense Type</label>
                            <select id="type" name="type" class="w-full rounded-xl border-slate-200 focus:border-birawa-500 focus:ring-birawa-500 shadow-sm transition-colors cursor-pointer">
                                <option value="OPEX">OPEX (Operational Expenditure)</option>
                                <option value="CAPEX">CAPEX (Capital Expenditure)</option>
                            </select>
                            <x-input-error :messages="$errors->get('type')" class="mt-2" />
                        </div>

                        <!-- Category -->
                        <div class="col-span-1 md:col-span-2">
                            <label for="category" class="block text-sm font-bold text-slate-700 mb-2">Category</label>
                            <input type="text" id="category" name="category" 
                                   class="w-full rounded-xl border-slate-200 focus:border-birawa-500 focus:ring-birawa-500 placeholder-slate-400 transition-colors shadow-sm"
                                   value="{{ old('category') }}" placeholder="e.g. Rent, Electricity, Equipment" required>
                            <x-input-error :messages="$errors->get('category')" class="mt-2" />
                        </div>

                        <!-- Amount -->
                        <div>
                            <label for="amount" class="block text-sm font-bold text-slate-700 mb-2">Amount <span class="text-slate-400 font-normal">(Rp)</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-slate-500 font-bold sm:text-sm">Rp</span>
                                </div>
                                <input type="number" id="amount" name="amount" 
                                       class="w-full pl-10 rounded-xl border-slate-200 focus:border-birawa-500 focus:ring-birawa-500 placeholder-slate-400 transition-colors shadow-sm"
                                       value="{{ old('amount') }}" step="0.01" required placeholder="0">
                            </div>
                            <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                        </div>

                        <!-- Notes -->
                        <div class="col-span-1 md:col-span-2">
                            <label for="notes" class="block text-sm font-bold text-slate-700 mb-2">Notes <span class="text-slate-400 font-normal">(Optional)</span></label>
                            <textarea id="notes" name="notes" rows="3" 
                                      class="w-full rounded-xl border-slate-200 focus:border-birawa-500 focus:ring-birawa-500 placeholder-slate-400 transition-colors shadow-sm"
                                      placeholder="Additional details about this expense...">{{ old('notes') }}</textarea>
                            <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end gap-4 pt-6 border-t border-slate-100">
                        <a href="{{ route('expenses.index') }}" class="text-sm font-bold text-slate-500 hover:text-slate-800 transition-colors">
                            Cancel
                        </a>
                        <button type="submit" class="px-6 py-2.5 bg-birawa-600 text-white text-sm font-bold rounded-xl shadow-lg hover:bg-birawa-700 focus:ring-4 focus:ring-birawa-100 transition-all active:scale-95">
                            Save Expense
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

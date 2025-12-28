<x-app-layout>
    @php
        $opexPercent = $totalExpenses > 0 ? number_format(($opex / $totalExpenses) * 100, 2) : 0;
        $capexPercent = $totalExpenses > 0 ? number_format(($capex / $totalExpenses) * 100, 2) : 0;
        $opexLabel = $totalExpenses > 0 ? number_format(($opex / $totalExpenses) * 100, 1) : 0;
        $capexLabel = $totalExpenses > 0 ? number_format(($capex / $totalExpenses) * 100, 1) : 0;
    @endphp

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-slate-800">Financial Dashboard</h1>
            <p class="text-sm text-slate-500">Overview of income, expenses, and profitability</p>
        </div>

        <!-- Date Filter -->
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-100 mb-6">
            <form action="{{ route('finance.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
                <div class="w-full md:w-auto">
                    <label for="start_date" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Start Date</label>
                    <input type="date" name="start_date" id="start_date" value="{{ $startDate->format('Y-m-d') }}" 
                           class="w-full rounded-xl border-slate-200 focus:border-birawa-500 focus:ring-birawa-500 text-sm">
                </div>
                <div class="w-full md:w-auto">
                    <label for="end_date" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">End Date</label>
                    <input type="date" name="end_date" id="end_date" value="{{ $endDate->format('Y-m-d') }}" 
                           class="w-full rounded-xl border-slate-200 focus:border-birawa-500 focus:ring-birawa-500 text-sm">
                </div>
                <button type="submit" class="w-full md:w-auto px-6 py-2.5 bg-slate-800 text-white text-sm font-bold rounded-xl hover:bg-slate-700 transition-colors shadow-lg">
                    Filter Period
                </button>
            </form>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <!-- Income -->
            <div class="bg-emerald-50 rounded-2xl p-5 border border-emerald-100 flex flex-col justify-between h-32 relative overflow-hidden">
                <div class="absolute right-0 top-0 p-4 opacity-10">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="relative z-10">
                    <p class="text-xs font-bold text-emerald-600 uppercase tracking-wider">Total Income</p>
                    <p class="text-2xl font-bold text-emerald-700 mt-1">Rp {{ number_format($income, 0, ',', '.') }}</p>
                </div>
                <div class="relative z-10 text-xs text-emerald-600 font-medium">
                    Revenue from Payments
                </div>
            </div>

            <!-- OPEX -->
            <div class="bg-orange-50 rounded-2xl p-5 border border-orange-100 flex flex-col justify-between h-32 relative overflow-hidden">
                <div class="absolute right-0 top-0 p-4 opacity-10">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <div class="relative z-10">
                    <p class="text-xs font-bold text-orange-600 uppercase tracking-wider">OPEX</p>
                    <p class="text-2xl font-bold text-orange-700 mt-1">Rp {{ number_format($opex, 0, ',', '.') }}</p>
                </div>
                <div class="relative z-10 text-xs text-orange-600 font-medium">
                    Operational Expenses
                </div>
            </div>

            <!-- CAPEX -->
            <div class="bg-blue-50 rounded-2xl p-5 border border-blue-100 flex flex-col justify-between h-32 relative overflow-hidden">
                <div class="absolute right-0 top-0 p-4 opacity-10">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <div class="relative z-10">
                    <p class="text-xs font-bold text-blue-600 uppercase tracking-wider">CAPEX</p>
                    <p class="text-2xl font-bold text-blue-700 mt-1">Rp {{ number_format($capex, 0, ',', '.') }}</p>
                </div>
                <div class="relative z-10 text-xs text-blue-600 font-medium">
                    Capital Expenses
                </div>
            </div>

            <!-- Net Profit -->
            <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm flex flex-col justify-between h-32 relative overflow-hidden">
                 <div class="absolute right-0 top-0 p-4 opacity-5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 text-slate-800" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <div class="relative z-10">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Net Profit</p>
                    <p class="text-2xl font-bold {{ $netProfit >= 0 ? 'text-birawa-600' : 'text-rose-600' }} mt-1">
                        Rp {{ number_format($netProfit, 0, ',', '.') }}
                    </p>
                </div>
                <div class="relative z-10 text-xs text-slate-400 font-medium">
                    Income - (OPEX + CAPEX)
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Recent Payments -->
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="p-6 border-b border-slate-50 flex justify-between items-center">
                    <h3 class="font-bold text-slate-800">Recent Income</h3>
                    <!-- TODO: Link to full payment history -->
                </div>
                
                <div class="divide-y divide-slate-50">
                    @forelse($recentPayments as $payment)
                        <div class="p-4 hover:bg-slate-50 transition-colors">
                            <div class="flex justify-between items-start mb-1">
                                <div>
                                    <h4 class="text-sm font-bold text-slate-800">{{ $payment->invoice->visit->patient->name ?? 'Unknown' }}</h4>
                                    <p class="text-xs text-slate-400">{{ $payment->paid_at->format('d M Y') }} • {{ ucfirst($payment->method) }}</p>
                                </div>
                                <span class="text-sm font-bold text-emerald-600">+ Rp {{ number_format($payment->amount, 0, ',', '.') }}</span>
                            </div>
                            @if($payment->notes)
                                <p class="text-xs text-slate-500 truncate mt-1">{{ $payment->notes }}</p>
                            @endif
                        </div>
                    @empty
                        <div class="p-8 text-center text-slate-400">
                            <p class="text-sm">No recent payments.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Recent Expenses -->
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="p-6 border-b border-slate-50 flex justify-between items-center">
                    <h3 class="font-bold text-slate-800">Recent Expenses</h3>
                    <a href="{{ route('expenses.create') }}" class="text-xs font-bold text-birawa-600 hover:text-birawa-700 bg-birawa-50 px-3 py-1.5 rounded-lg border border-birawa-100 transition-colors">
                        + Add Expense
                    </a>
                </div>
                
                <div class="divide-y divide-slate-50">
                    @forelse($recentExpenses as $expense)
                        <div class="p-4 hover:bg-slate-50 transition-colors">
                            <div class="flex justify-between items-start mb-1">
                                <div>
                                    <h4 class="text-sm font-bold text-slate-800">{{ $expense->category }}</h4>
                                    <p class="text-xs text-slate-400">{{ \Carbon\Carbon::parse($expense->transaction_date)->format('d M Y') }}</p>
                                </div>
                                <span class="text-sm font-bold text-slate-800">Rp {{ number_format($expense->amount, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between items-center mt-2">
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wide {{ $expense->type === 'OPEX' ? 'bg-orange-50 text-orange-600 border border-orange-100' : 'bg-blue-50 text-blue-600 border border-blue-100' }}">
                                    {{ $expense->type }}
                                </span>
                                @if($expense->notes)
                                    <p class="text-xs text-slate-500 truncate max-w-[200px]">{{ $expense->notes }}</p>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-slate-400">
                            <p class="text-sm">No recent expenses found.</p>
                        </div>
                    @endforelse
                </div>
                
                <div class="p-4 bg-slate-50 border-t border-slate-100 text-center">
                    <a href="{{ route('expenses.index') }}" class="text-sm font-bold text-birawa-600 hover:text-birawa-700">View All Expenses &rarr;</a>
                </div>
            </div>

            <!-- Financial Insights -->
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6 flex flex-col">
                <h3 class="font-bold text-slate-800 mb-4">Expense Analysis</h3>
                <p class="text-sm text-slate-500 mb-6">
                    Breakdown of your operational vs capital expenditure for the selected period.
                </p>
                
                <div class="flex-1 flex flex-col justify-center">
                    @if($totalExpenses > 0)
                        <div class="flex items-center gap-4 mb-2">
                            <div class="w-24 text-xs font-bold text-slate-500 text-right">OPEX</div>
                            <div class="flex-1 h-3 bg-slate-100 rounded-full overflow-hidden">
                                <div class="h-full bg-orange-400 rounded-full" style="width: {{ $opexPercent }}%"></div>
                            </div>
                            <div class="w-16 text-xs font-bold text-slate-800">{{ $opexLabel }}%</div>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="w-24 text-xs font-bold text-slate-500 text-right">CAPEX</div>
                            <div class="flex-1 h-3 bg-slate-100 rounded-full overflow-hidden">
                                <div class="h-full bg-blue-400 rounded-full" style="width: {{ $capexPercent }}%"></div>
                            </div>
                            <div class="w-16 text-xs font-bold text-slate-800">{{ $capexLabel }}%</div>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-slate-100 text-slate-400 mb-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </div>
                            <p class="text-sm text-slate-400">No expense data available for visualization.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

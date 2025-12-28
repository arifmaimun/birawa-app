<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Financial Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Date Filter -->
            <div class="mb-6 bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('finance.index') }}" method="GET" class="flex gap-4 items-end">
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                        <input type="date" name="start_date" id="start_date" value="{{ $startDate->format('Y-m-d') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-birawa-500 focus:ring-birawa-500">
                    </div>
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                        <input type="date" name="end_date" id="end_date" value="{{ $endDate->format('Y-m-d') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-birawa-500 focus:ring-birawa-500">
                    </div>
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                        Filter
                    </button>
                </form>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <!-- Income -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500 truncate">Total Income</div>
                    <div class="mt-1 text-2xl font-semibold text-green-600">
                        Rp {{ number_format($income, 0, ',', '.') }}
                    </div>
                </div>

                <!-- OPEX -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500 truncate">OPEX (Operational)</div>
                    <div class="mt-1 text-2xl font-semibold text-orange-600">
                        Rp {{ number_format($opex, 0, ',', '.') }}
                    </div>
                </div>

                <!-- CAPEX -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500 truncate">CAPEX (Capital)</div>
                    <div class="mt-1 text-2xl font-semibold text-blue-600">
                        Rp {{ number_format($capex, 0, ',', '.') }}
                    </div>
                </div>

                <!-- Net Profit -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500 truncate">Net Profit</div>
                    <div class="mt-1 text-2xl font-semibold {{ $netProfit >= 0 ? 'text-indigo-600' : 'text-red-600' }}">
                        Rp {{ number_format($netProfit, 0, ',', '.') }}
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Recent Expenses -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="text-lg font-medium text-gray-900">Recent Expenses</h3>
                        <a href="{{ route('expenses.create') }}" class="text-sm text-indigo-600 hover:text-indigo-900">+ Add New</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($recentExpenses as $expense)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ \Carbon\Carbon::parse($expense->transaction_date)->format('d M Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $expense->category }}
                                        <div class="text-xs text-gray-500">{{ Str::limit($expense->notes, 30) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        Rp {{ number_format($expense->amount, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $expense->type === 'OPEX' ? 'bg-orange-100 text-orange-800' : 'bg-blue-100 text-blue-800' }}">
                                            {{ $expense->type }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">No recent expenses.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="p-4 border-t border-gray-200 bg-gray-50 text-right">
                        <a href="{{ route('expenses.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">View all expenses &rarr;</a>
                    </div>
                </div>

                <!-- Quick Actions / Placeholders for Charts -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Financial Insights</h3>
                    <p class="text-gray-600 mb-4">
                        Comparison of Operational (OPEX) vs Capital (CAPEX) spending for the selected period.
                    </p>
                    
                    @if($totalExpenses > 0)
                    <div class="w-full bg-gray-200 rounded-full h-4 mb-2 flex overflow-hidden">
                        <div class="bg-orange-500 h-4" style="width: {{ ($opex / $totalExpenses) * 100 }}%"></div>
                        <div class="bg-blue-500 h-4" style="width: {{ ($capex / $totalExpenses) * 100 }}%"></div>
                    </div>
                    <div class="flex justify-between text-xs text-gray-500">
                        <span>OPEX: {{ number_format(($opex / $totalExpenses) * 100, 1) }}%</span>
                        <span>CAPEX: {{ number_format(($capex / $totalExpenses) * 100, 1) }}%</span>
                    </div>
                    @else
                    <p class="text-sm text-gray-500 italic">No expense data available for this period.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

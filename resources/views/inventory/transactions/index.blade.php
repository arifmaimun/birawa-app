<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Inventory Mutation History') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <!-- Filters -->
                    <form method="GET" action="{{ route('inventory.transactions.index') }}" class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <x-input-label for="search" value="Search Item" />
                            <x-text-input id="search" name="search" type="text" class="mt-1 block w-full" :value="request('search')" placeholder="Name or SKU" />
                        </div>
                        <div>
                            <x-input-label for="type" value="Type" />
                            <select id="type" name="type" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">All Types</option>
                                <option value="IN" {{ request('type') == 'IN' ? 'selected' : '' }}>IN (Restock/Transfer)</option>
                                <option value="OUT" {{ request('type') == 'OUT' ? 'selected' : '' }}>OUT (Usage/Transfer)</option>
                                <option value="ADJUSTMENT" {{ request('type') == 'ADJUSTMENT' ? 'selected' : '' }}>ADJUSTMENT (Opname)</option>
                            </select>
                        </div>
                        <div>
                            <x-input-label for="start_date" value="Start Date" />
                            <x-text-input id="start_date" name="start_date" type="date" class="mt-1 block w-full" :value="request('start_date')" />
                        </div>
                        <div>
                            <x-input-label for="end_date" value="End Date" />
                            <div class="flex gap-2">
                                <x-text-input id="end_date" name="end_date" type="date" class="mt-1 block w-full" :value="request('end_date')" />
                                <button type="submit" class="mt-1 bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">Filter</button>
                            </div>
                        </div>
                    </form>

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity Change</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($transactions as $transaction)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $transaction->created_at->format('d M Y H:i') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $transaction->inventory->item_name }}
                                            <div class="text-xs text-gray-500">{{ $transaction->inventory->sku }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @php
                                                $color = match($transaction->type) {
                                                    'IN' => 'bg-green-100 text-green-800',
                                                    'OUT' => 'bg-red-100 text-red-800',
                                                    'ADJUSTMENT' => 'bg-yellow-100 text-yellow-800',
                                                    default => 'bg-gray-100 text-gray-800'
                                                };
                                            @endphp
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $color }}">
                                                {{ $transaction->type }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm {{ $transaction->quantity_change > 0 ? 'text-green-600' : 'text-red-600' }} font-bold">
                                            {{ ($transaction->quantity_change > 0 ? '+' : '') . ($transaction->quantity_change + 0) }} {{ $transaction->inventory->unit }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            {{ $transaction->notes }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            No transactions found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $transactions->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

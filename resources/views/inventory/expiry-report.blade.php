<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Expiry Report') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-bold">Batch Expiry Tracking</h3>
                        <a href="{{ route('inventory.index') }}" class="text-indigo-600 hover:text-indigo-900">
                            &larr; Back to Inventory
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Batch No</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expiry Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Remaining Qty</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($batches as $batch)
                                    @php
                                        $isExpired = $batch->expiry_date && $batch->expiry_date->isPast();
                                        $isSoon = $batch->expiry_date && $batch->expiry_date->diffInDays(now()) < 30 && !$isExpired;
                                        $rowClass = $isExpired ? 'bg-red-50' : ($isSoon ? 'bg-yellow-50' : '');
                                        $textClass = $isExpired ? 'text-red-600 font-bold' : ($isSoon ? 'text-yellow-600 font-bold' : 'text-gray-900');
                                        $statusLabel = $isExpired ? 'Expired' : ($isSoon ? 'Expiring Soon' : 'OK');
                                    @endphp
                                    <tr class="{{ $rowClass }}">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $batch->batch_number }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $batch->inventory->item_name }}
                                            <div class="text-xs text-gray-500">{{ $batch->inventory->sku }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm {{ $textClass }}">
                                            {{ $batch->expiry_date ? $batch->expiry_date->format('d M Y') : 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $batch->quantity + 0 }} {{ $batch->inventory->unit }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $isExpired ? 'bg-red-100 text-red-800' : ($isSoon ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                                                {{ $statusLabel }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            No active batches found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $batches->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

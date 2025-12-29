<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Transfer Details: {{ $inventoryTransfer->transfer_number }}
            </h2>
            <div class="flex gap-2">
                @if($inventoryTransfer->status === 'pending')
                    @php
                        $userId = Auth::id();
                        $canApprove = false;
                        if ($inventoryTransfer->source_type === 'central' && $userId === 1) $canApprove = true;
                        if ($inventoryTransfer->source_type === 'doctor' && $inventoryTransfer->source_id === $userId) $canApprove = true;
                    @endphp

                    @if($canApprove)
                        <form action="{{ route('inventory-transfers.approve', $inventoryTransfer) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                                Approve & Process
                            </button>
                        </form>
                        
                        <form action="{{ route('inventory-transfers.reject', $inventoryTransfer) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">
                                Reject
                            </button>
                        </form>
                    @endif
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Transfer Info</h3>
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Status</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $inventoryTransfer->status === 'approved' ? 'bg-green-100 text-green-800' : 
                                           ($inventoryTransfer->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                        {{ ucfirst($inventoryTransfer->status) }}
                                    </span>
                                </dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Date</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $inventoryTransfer->created_at->format('d M Y H:i') }}</dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Source</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    @if($inventoryTransfer->source_type === 'central')
                                        Central
                                    @else
                                        Doctor (ID: {{ $inventoryTransfer->source_id }})
                                    @endif
                                </dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Target</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    @if($inventoryTransfer->target_type === 'central')
                                        Central
                                    @else
                                        Doctor (ID: {{ $inventoryTransfer->target_id }})
                                    @endif
                                </dd>
                            </div>
                            <div class="sm:col-span-2">
                                <dt class="text-sm font-medium text-gray-500">Notes</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $inventoryTransfer->notes ?? '-' }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Items</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKU</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty Requested</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty Approved</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($inventoryTransfer->items as $item)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $item->item_name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->sku ?? '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->quantity_requested }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->quantity_approved ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

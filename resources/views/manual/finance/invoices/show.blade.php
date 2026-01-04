<x-manual.layouts.app>
    <x-slot name="header">
        Invoice Details: {{ $invoice->invoice_number }}
    </x-slot>

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-8 bg-white border-b border-gray-200">
                <!-- Header -->
                <div class="flex justify-between items-start mb-8 border-b pb-8">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-800">INVOICE</h1>
                        <p class="text-gray-500 mt-1">#{{ $invoice->invoice_number }}</p>
                        <div class="mt-4">
                            <span class="px-3 py-1 rounded-full text-sm font-semibold 
                                {{ $invoice->payment_status === 'paid' ? 'bg-green-100 text-green-800' : 
                                   ($invoice->payment_status === 'partial' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                {{ ucfirst($invoice->payment_status) }}
                            </span>
                        </div>
                    </div>
                    <div class="text-right">
                        <h3 class="font-bold text-gray-700">Bill To:</h3>
                        <p class="text-gray-800 font-medium">{{ $invoice->patient->client->name ?? 'N/A' }}</p>
                        <p class="text-gray-600 text-sm">{{ $invoice->patient->client->address ?? '' }}</p>
                        <p class="text-gray-600 text-sm mt-2">Patient: <span class="font-medium">{{ $invoice->patient->name ?? 'N/A' }}</span></p>
                        
                        <div class="mt-4">
                            <p class="text-sm text-gray-600">Date: <span class="text-gray-800 font-medium">{{ $invoice->created_at->format('d M Y') }}</span></p>
                            <p class="text-sm text-gray-600">Due Date: <span class="text-gray-800 font-medium">{{ $invoice->due_date ? $invoice->due_date->format('d M Y') : '-' }}</span></p>
                        </div>
                    </div>
                </div>

                <!-- Items -->
                <div class="mb-8">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Price</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($invoice->items as $item)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $item->description }}
                                    @if($item->product)
                                        <br><span class="text-xs text-gray-500">{{ $item->product->sku }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900">
                                    {{ $item->quantity }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900">
                                    Rp {{ number_format($item->unit_price, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium text-gray-900">
                                    Rp {{ number_format($item->quantity * $item->unit_price, 0, ',', '.') }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50">
                             <tr>
                                <td colspan="3" class="px-6 py-4 text-right font-bold text-gray-700">Subtotal</td>
                                <td class="px-6 py-4 text-right font-bold text-gray-900">Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</td>
                            </tr>
                            @if($invoice->discount_amount > 0)
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-right font-bold text-gray-700">Discount</td>
                                <td class="px-6 py-4 text-right font-bold text-red-600">- Rp {{ number_format($invoice->discount_amount, 0, ',', '.') }}</td>
                            </tr>
                            @endif
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-right font-bold text-gray-900 text-lg">Total</td>
                                <td class="px-6 py-4 text-right font-bold text-indigo-600 text-lg">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Footer / Actions -->
                <div class="flex justify-end space-x-4 print:hidden">
                    <a href="{{ route('manual.invoices.index') }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">
                        Back to List
                    </a>
                    <button onclick="window.print()" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                        Print Invoice
                    </button>
                    <!-- Payment Button could go here -->
                </div>
            </div>
        </div>
    </div>
</x-manual.layouts.app>

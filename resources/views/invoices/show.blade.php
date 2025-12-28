<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Invoice Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-8" id="invoice-print">
                
                <!-- Header -->
                <div class="flex justify-between items-start mb-8 border-b pb-8">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">INVOICE</h1>
                        <p class="text-gray-500 mt-1">#{{ $invoice->invoice_number }}</p>
                        <p class="text-gray-500 text-sm mt-1">{{ $invoice->created_at->format('d F Y') }}</p>
                    </div>
                    <div class="text-right">
                        <h3 class="font-bold text-lg">{{ Auth::user()->name }}</h3>
                        <p class="text-gray-500 text-sm">{{ Auth::user()->email }}</p>
                        <!-- Doctor Address/Phone if available in profile -->
                    </div>
                </div>

                <!-- Patient & Owner Info -->
                <div class="grid grid-cols-2 gap-8 mb-8">
                    <div>
                        <h4 class="font-bold text-gray-700 mb-2">Bill To:</h4>
                        <p class="text-gray-600">{{ $invoice->visit->patient->owners->first()->name ?? 'Unknown Owner' }}</p>
                        <p class="text-gray-500 text-sm">{{ $invoice->visit->patient->owners->first()->address ?? '' }}</p>
                        <p class="text-gray-500 text-sm">{{ $invoice->visit->patient->owners->first()->phone ?? '' }}</p>
                    </div>
                    <div class="text-right">
                        <h4 class="font-bold text-gray-700 mb-2">Patient:</h4>
                        <p class="text-gray-600 font-medium">{{ $invoice->visit->patient->name }}</p>
                        <p class="text-gray-500 text-sm">{{ ucfirst($invoice->visit->patient->species) }} - {{ $invoice->visit->patient->breed }}</p>
                    </div>
                </div>

                <!-- Items -->
                <table class="w-full mb-8">
                    <thead>
                        <tr class="border-b-2 border-gray-200">
                            <th class="text-left py-3 text-gray-600 font-bold">Description</th>
                            <th class="text-right py-3 text-gray-600 font-bold">Qty</th>
                            <th class="text-right py-3 text-gray-600 font-bold">Unit Price</th>
                            <th class="text-right py-3 text-gray-600 font-bold">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoice->invoiceItems as $item)
                            <tr class="border-b border-gray-100">
                                <td class="py-4 text-gray-800">{{ $item->description }}</td>
                                <td class="py-4 text-right text-gray-600">{{ $item->quantity + 0 }}</td> <!-- +0 to remove trailing zeros if float -->
                                <td class="py-4 text-right text-gray-600">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                <td class="py-4 text-right text-gray-800 font-medium">Rp {{ number_format($item->quantity * $item->unit_price, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-right py-4 font-bold text-gray-700">Total Amount</td>
                            <td class="text-right py-4 font-bold text-xl text-gray-900">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>

                <!-- Actions -->
                <div class="flex justify-between items-center mt-8 pt-8 border-t border-gray-200 no-print">
                    <div>
                        <span class="text-sm font-medium text-gray-500 uppercase tracking-wider">Status:</span>
                        @if($invoice->payment_status === 'paid')
                            <span class="ml-2 px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800">PAID</span>
                        @else
                            <span class="ml-2 px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-red-100 text-red-800">UNPAID</span>
                        @endif
                    </div>
                    <div class="flex space-x-4">
                        <button onclick="window.print()" class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">Print</button>
                        
                        @if($invoice->payment_status !== 'paid')
                            <form action="{{ route('invoices.markPaid', $invoice) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700" onclick="return confirm('Mark this invoice as PAID?')">
                                    Mark as Paid
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        @media print {
            .no-print {
                display: none;
            }
            body {
                background-color: white;
            }
            .shadow-sm {
                box-shadow: none;
            }
        }
    </style>
</x-app-layout>

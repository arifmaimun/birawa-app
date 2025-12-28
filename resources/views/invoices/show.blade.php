<x-app-layout>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Back Link -->
        <div class="mb-6 flex items-center justify-between no-print">
            <a href="{{ route('invoices.index') }}" class="text-sm font-medium text-birawa-600 hover:text-birawa-700 flex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Invoices
            </a>
            <button onclick="window.print()" class="px-4 py-2 bg-slate-800 text-white rounded-xl text-sm font-bold shadow-lg hover:bg-slate-700 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Print Invoice
            </button>
        </div>

        <div class="bg-white rounded-3xl shadow-lg border border-slate-100 overflow-hidden" id="invoice-print">
            <!-- Header Banner -->
            <div class="bg-slate-900 text-white p-8 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-64 h-64 bg-slate-800 rounded-bl-full -mr-32 -mt-32 opacity-50"></div>
                <div class="relative z-10 flex justify-between items-start">
                    <div>
                        <h1 class="text-3xl font-bold tracking-tight">INVOICE</h1>
                        <p class="text-slate-400 mt-1 text-sm font-medium tracking-wide">#{{ $invoice->invoice_number }}</p>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-lg">{{ Auth::user()->name }}</p>
                        <p class="text-slate-400 text-sm">{{ Auth::user()->email }}</p>
                    </div>
                </div>
            </div>

            <div class="p-8">
                <!-- Info Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-10 border-b border-slate-100 pb-10">
                    <div>
                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Bill To</h4>
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-full bg-birawa-50 text-birawa-600 flex items-center justify-center font-bold">
                                {{ substr($invoice->visit->patient->owners->first()->name ?? 'U', 0, 1) }}
                            </div>
                            <div>
                                <p class="font-bold text-slate-800">{{ $invoice->visit->patient->owners->first()->name ?? 'Unknown Owner' }}</p>
                                <p class="text-sm text-slate-500 mt-0.5">{{ $invoice->visit->patient->owners->first()->address ?? 'No Address' }}</p>
                                <p class="text-sm text-slate-500 mt-0.5">{{ $invoice->visit->patient->owners->first()->phone ?? 'No Phone' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="md:text-right">
                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Patient Details</h4>
                        <p class="font-bold text-slate-800 text-lg">{{ $invoice->visit->patient->name }}</p>
                        <p class="text-sm text-slate-500 mt-0.5">{{ ucfirst($invoice->visit->patient->species) }} • {{ $invoice->visit->patient->breed }}</p>
                        <p class="text-xs text-slate-400 mt-2">Visit Date: {{ $invoice->visit->created_at->format('d M Y, H:i') }}</p>
                    </div>
                </div>

                <!-- Items Table -->
                <div class="overflow-x-auto mb-8">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b-2 border-slate-100">
                                <th class="text-left py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Description</th>
                                <th class="text-right py-4 text-xs font-bold text-slate-500 uppercase tracking-wider w-24">Qty</th>
                                <th class="text-right py-4 text-xs font-bold text-slate-500 uppercase tracking-wider w-32">Price</th>
                                <th class="text-right py-4 text-xs font-bold text-slate-500 uppercase tracking-wider w-32">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach($invoice->invoiceItems as $item)
                                <tr>
                                    <td class="py-4 text-slate-700 font-medium">{{ $item->description }}</td>
                                    <td class="py-4 text-right text-slate-500">{{ $item->quantity + 0 }}</td>
                                    <td class="py-4 text-right text-slate-500">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                    <td class="py-4 text-right text-slate-800 font-bold">Rp {{ number_format($item->quantity * $item->unit_price, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Total & Status -->
                <div class="flex flex-col md:flex-row justify-between items-end gap-6 bg-slate-50 rounded-2xl p-6">
                    <div class="w-full md:w-auto">
                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Payment Status</h4>
                        @if($invoice->payment_status === 'paid')
                            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-emerald-100 text-emerald-700 text-sm font-bold border border-emerald-200">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                PAID
                            </div>
                        @else
                            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-rose-100 text-rose-700 text-sm font-bold border border-rose-200">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                UNPAID
                            </div>
                        @endif
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-medium text-slate-500 mb-1">Total Amount</p>
                        <p class="text-3xl font-bold text-slate-900">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</p>
                    </div>
                </div>

                <!-- Public Link -->
                <div class="mt-8 pt-8 border-t border-slate-100 no-print">
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Public Share Link (Valid 48h)</label>
                    <div class="flex gap-2">
                        <input type="text" readonly value="{{ route('invoices.public', $invoice->access_token) }}" 
                               class="flex-1 rounded-xl border-slate-200 bg-slate-50 text-slate-500 text-sm focus:border-birawa-500 focus:ring-birawa-500" 
                               onclick="this.select()">
                        <a href="{{ route('invoices.public', $invoice->access_token) }}" target="_blank" 
                           class="px-4 py-2 bg-white border border-slate-200 text-slate-700 font-bold rounded-xl hover:bg-slate-50 hover:text-slate-900 transition-colors">
                            Open
                        </a>
                    </div>
                </div>

                <!-- Mark as Paid Action -->
                @if($invoice->payment_status !== 'paid')
                    <div class="mt-6 pt-6 border-t border-slate-100 no-print flex justify-end">
                        <form action="{{ route('invoices.markPaid', $invoice) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="px-6 py-3 bg-emerald-500 text-white rounded-xl font-bold shadow-lg shadow-emerald-500/30 hover:bg-emerald-600 transition-all active:scale-95 flex items-center gap-2" onclick="return confirm('Mark this invoice as PAID?')">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Mark as Paid
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>

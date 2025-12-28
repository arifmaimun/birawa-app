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
                                {{ substr($invoice->visit->patient->client->name ?? 'U', 0, 1) }}
                            </div>
                            <div>
                                <p class="font-bold text-slate-800">{{ $invoice->visit->patient->client->name ?? 'Unknown Owner' }}</p>
                                <p class="text-sm text-slate-500 mt-0.5">{{ $invoice->visit->patient->client->address ?? 'No Address' }}</p>
                                <p class="text-sm text-slate-500 mt-0.5">{{ $invoice->visit->patient->client->phone ?? 'No Phone' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="md:text-right">
                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Patient Details</h4>
                        <p class="font-bold text-slate-800 text-lg">{{ $invoice->visit->patient->name }}</p>
                        <p class="text-sm text-slate-500 mt-0.5">{{ ucfirst($invoice->visit->patient->species) }} • {{ $invoice->visit->patient->breed }}</p>
                        <p class="text-xs text-slate-400 mt-2">Visit Date: {{ $invoice->visit->created_at->format('d M Y, H:i') }}</p>
                        @if($invoice->due_date)
                            <p class="text-xs text-rose-500 font-bold mt-1">Due Date: {{ $invoice->due_date->format('d M Y') }}</p>
                        @endif
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

                <!-- Financial Summary -->
                <div class="flex flex-col md:flex-row justify-between items-start gap-6 bg-slate-50 rounded-2xl p-6">
                    <div class="w-full md:w-auto">
                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Payment Status</h4>
                        @if($invoice->payment_status === 'paid')
                            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-emerald-100 text-emerald-700 text-sm font-bold border border-emerald-200">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                PAID
                            </div>
                        @elseif($invoice->payment_status === 'partial')
                            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-amber-100 text-amber-700 text-sm font-bold border border-amber-200">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                PARTIALLY PAID
                            </div>
                        @else
                            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-rose-100 text-rose-700 text-sm font-bold border border-rose-200">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                UNPAID
                            </div>
                        @endif

                        @if($invoice->notes)
                            <div class="mt-4 max-w-xs">
                                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Notes</h4>
                                <p class="text-sm text-slate-600 italic">{{ $invoice->notes }}</p>
                            </div>
                        @endif
                    </div>
                    
                    <div class="w-full md:w-64 space-y-2">
                        <div class="flex justify-between text-sm text-slate-600">
                            <span>Subtotal</span>
                            <span>Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</span>
                        </div>
                        @if($invoice->deposit_amount > 0)
                            <div class="flex justify-between text-sm text-emerald-600">
                                <span>Deposit</span>
                                <span>- Rp {{ number_format($invoice->deposit_amount, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        @php $paid = $invoice->payments->sum('amount'); @endphp
                        @if($paid > 0)
                            <div class="flex justify-between text-sm text-emerald-600">
                                <span>Paid</span>
                                <span>- Rp {{ number_format($paid, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        <div class="border-t border-slate-200 pt-2 flex justify-between items-baseline">
                            <span class="text-sm font-bold text-slate-900">Remaining</span>
                            <span class="text-2xl font-bold {{ $invoice->remaining_balance > 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                                Rp {{ number_format($invoice->remaining_balance, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Payment History -->
                @if($invoice->payments->count() > 0)
                <div class="mt-10 border-t border-slate-100 pt-8">
                    <h3 class="text-lg font-bold text-slate-900 mb-4">Payment History</h3>
                    <div class="overflow-hidden rounded-xl border border-slate-200">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Date</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Method</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Notes</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Amount</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider no-print">Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-slate-200">
                                @foreach($invoice->payments as $payment)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">{{ $payment->paid_at->format('d M Y H:i') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900 capitalize">{{ $payment->method }}</td>
                                        <td class="px-6 py-4 text-sm text-slate-500">{{ $payment->notes ?: '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-bold text-emerald-600">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium no-print">
                                            <form action="{{ route('invoices.payments.destroy', [$invoice, $payment]) }}" method="POST" class="inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-rose-600 hover:text-rose-900" onclick="return confirm('Are you sure you want to delete this payment?')">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

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
            </div>
        </div>

        <!-- Management Section (No Print) -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6 no-print">
            <!-- Update Invoice Details -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                <h3 class="text-lg font-bold text-slate-900 mb-4">Invoice Settings</h3>
                <form action="{{ route('invoices.update', $invoice) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="space-y-4">
                        <div>
                            <label for="deposit_amount" class="block text-sm font-medium text-slate-700">Deposit Amount (Rp)</label>
                            <input type="number" name="deposit_amount" id="deposit_amount" value="{{ old('deposit_amount', $invoice->deposit_amount + 0) }}" step="0.01"
                                   class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm focus:border-birawa-500 focus:ring-birawa-500 sm:text-sm">
                        </div>

                        <div>
                            <label for="due_date" class="block text-sm font-medium text-slate-700">Due Date</label>
                            <input type="date" name="due_date" id="due_date" value="{{ old('due_date', $invoice->due_date ? $invoice->due_date->format('Y-m-d') : '') }}"
                                   class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm focus:border-birawa-500 focus:ring-birawa-500 sm:text-sm">
                        </div>

                        <div>
                            <label for="notes" class="block text-sm font-medium text-slate-700">Notes / Terms</label>
                            <textarea name="notes" id="notes" rows="3" 
                                      class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm focus:border-birawa-500 focus:ring-birawa-500 sm:text-sm">{{ old('notes', $invoice->notes) }}</textarea>
                        </div>

                        <div class="pt-2">
                            <button type="submit" class="w-full px-4 py-2 bg-slate-800 text-white rounded-xl font-bold hover:bg-slate-700 transition-colors">
                                Update Settings
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Add Payment -->
            @if($invoice->remaining_balance > 0)
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                <h3 class="text-lg font-bold text-slate-900 mb-4">Record Payment</h3>
                <form action="{{ route('invoices.payments.store', $invoice) }}" method="POST">
                    @csrf
                    
                    <div class="space-y-4">
                        <div>
                            <label for="amount" class="block text-sm font-medium text-slate-700">Amount (Rp)</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-slate-500 sm:text-sm">Rp</span>
                                </div>
                                <input type="number" name="amount" id="amount" value="{{ old('amount', $invoice->remaining_balance + 0) }}" max="{{ $invoice->remaining_balance + 0 }}" step="0.01" required
                                       class="focus:ring-birawa-500 focus:border-birawa-500 block w-full pl-10 sm:text-sm border-slate-300 rounded-xl">
                            </div>
                            <p class="mt-1 text-xs text-slate-500">Max: {{ number_format($invoice->remaining_balance, 0, ',', '.') }}</p>
                        </div>

                        <div>
                            <label for="method" class="block text-sm font-medium text-slate-700">Payment Method</label>
                            <select name="method" id="method" class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm focus:border-birawa-500 focus:ring-birawa-500 sm:text-sm">
                                <option value="cash">Cash</option>
                                <option value="transfer">Bank Transfer</option>
                                <option value="qris">QRIS</option>
                                <option value="card">Card</option>
                                <option value="insurance">Insurance</option>
                            </select>
                        </div>

                        <div>
                            <label for="paid_at" class="block text-sm font-medium text-slate-700">Date Paid</label>
                            <input type="date" name="paid_at" id="paid_at" value="{{ date('Y-m-d') }}"
                                   class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm focus:border-birawa-500 focus:ring-birawa-500 sm:text-sm">
                        </div>

                        <div>
                            <label for="payment_notes" class="block text-sm font-medium text-slate-700">Payment Notes</label>
                            <input type="text" name="notes" id="payment_notes" placeholder="Optional"
                                   class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm focus:border-birawa-500 focus:ring-birawa-500 sm:text-sm">
                        </div>

                        <div class="pt-2">
                            <button type="submit" class="w-full px-4 py-2 bg-birawa-600 text-white rounded-xl font-bold hover:bg-birawa-700 transition-colors shadow-lg shadow-birawa-500/30">
                                Record Payment
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            @else
            <div class="bg-emerald-50 rounded-2xl border border-emerald-100 p-6 flex flex-col justify-center items-center text-center">
                <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mb-4 text-emerald-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-emerald-800">Fully Paid</h3>
                <p class="text-emerald-600 mt-2">This invoice has been fully settled.</p>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>

<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Header & Search -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <h1 class="text-2xl font-bold text-slate-800">Invoices</h1>
            
            <form method="GET" action="{{ route('invoices.index') }}" class="relative w-full md:w-64">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                    </svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" 
                       class="block w-full pl-10 pr-3 py-2 border border-slate-200 rounded-xl leading-5 bg-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-birawa-500 focus:border-birawa-500 sm:text-sm transition-shadow" 
                       placeholder="Search invoice or patient...">
            </form>
        </div>

        <!-- Invoices Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($invoices as $invoice)
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 flex flex-col gap-4 hover:border-birawa-200 transition-colors relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-16 h-16 bg-slate-50 rounded-bl-full -mr-8 -mt-8 transition-colors group-hover:bg-birawa-50"></div>

                    <!-- Header: Number & Date -->
                    <div class="flex justify-between items-start relative z-10">
                        <div>
                            <h3 class="font-bold text-slate-800 text-lg">{{ $invoice->invoice_number }}</h3>
                            <p class="text-xs text-slate-500 flex items-center gap-1 mt-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                {{ $invoice->created_at->format('d M Y') }}
                            </p>
                        </div>
                        <div class="text-right">
                             @if($invoice->payment_status === 'paid')
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wide bg-emerald-50 text-emerald-600 border border-emerald-100">
                                    Paid
                                </span>
                            @else
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wide bg-rose-50 text-rose-600 border border-rose-100">
                                    Unpaid
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Patient Info -->
                    <div class="flex items-center gap-3 py-3 border-t border-b border-slate-50">
                        <div class="w-10 h-10 rounded-full bg-birawa-50 text-birawa-600 flex items-center justify-center font-bold text-sm">
                            {{ substr($invoice->visit->patient->name, 0, 1) }}
                        </div>
                        <div>
                            <p class="font-bold text-slate-700 text-sm">{{ $invoice->visit->patient->name }}</p>
                            <p class="text-xs text-slate-500">{{ $invoice->visit->patient->client->name ?? 'Unknown Owner' }}</p>
                        </div>
                    </div>

                    <!-- Footer: Amount & Action -->
                    <div class="flex justify-between items-center mt-auto pt-1">
                        <div>
                            <p class="text-xs text-slate-500 font-medium uppercase tracking-wider">Total Amount</p>
                            <p class="text-lg font-bold text-slate-800">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</p>
                        </div>
                        <a href="{{ route('invoices.show', $invoice) }}" class="px-4 py-2 bg-slate-800 text-white text-sm font-bold rounded-xl hover:bg-slate-700 transition-colors shadow-lg shadow-slate-200">
                            View
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-12 text-center bg-white rounded-3xl border-2 border-dashed border-slate-200">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-50 text-slate-300 mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800">No Invoices Found</h3>
                    <p class="text-slate-500 max-w-sm mx-auto mt-1">Invoices will appear here once you generate them from visits.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-6">
            {{ $invoices->links() }}
        </div>
    </div>
</x-app-layout>

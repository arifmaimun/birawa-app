<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $invoice->invoice_number }} - Birawa Vet</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Nunito', 'ui-sans-serif', 'system-ui', '-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'Roboto', 'Helvetica Neue', 'Arial', 'sans-serif'],
                    },
                    colors: {
                        birawa: {
                            50: '#f0fdfa',
                            100: '#ccfbf1',
                            200: '#99f6e4',
                            300: '#5eead4',
                            400: '#2dd4bf',
                            500: '#14b8a6',
                            600: '#0d9488',
                            700: '#0f766e',
                            800: '#115e59',
                            900: '#134e4a',
                            950: '#042f2e',
                        }
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Nunito', sans-serif; }
        @media print {
            .no-print { display: none !important; }
            body { background-color: white; }
            .shadow-lg, .shadow-xl { box-shadow: none !important; }
            .border { border-color: #e2e8f0 !important; } /* slate-200 */
            .bg-slate-50 { background-color: white !important; }
        }
    </style>
</head>
<body class="bg-slate-50 font-sans antialiased min-h-screen py-10 px-4">
    <div class="max-w-3xl mx-auto">
        <!-- Actions -->
        <div class="mb-6 flex justify-between items-center no-print">
            <a href="{{ url('/') }}" class="text-sm font-bold text-slate-500 hover:text-birawa-600 transition-colors flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Home
            </a>
            <button onclick="window.print()" class="px-5 py-2.5 bg-slate-800 text-white rounded-xl text-sm font-bold shadow-lg hover:bg-slate-700 flex items-center gap-2 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Print Invoice
            </button>
        </div>

        <div class="bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden relative">
            <!-- Header Banner -->
            <div class="bg-slate-900 text-white p-8 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-64 h-64 bg-slate-800 rounded-bl-full -mr-32 -mt-32 opacity-50"></div>
                <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <h1 class="text-3xl font-bold tracking-tight">INVOICE</h1>
                            @if($invoice->payment_status === 'paid')
                                <span class="bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider">
                                    PAID
                                </span>
                            @else
                                <span class="bg-rose-500/20 text-rose-300 border border-rose-500/30 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider">
                                    UNPAID
                                </span>
                            @endif
                        </div>
                        <p class="text-slate-400 text-sm font-medium tracking-wide font-mono">#{{ $invoice->invoice_number }}</p>
                    </div>
                    <div class="text-left md:text-right">
                        <p class="font-bold text-lg">{{ $invoice->visit->user->name }}</p>
                        <p class="text-slate-400 text-sm">{{ $invoice->visit->user->email }}</p>
                        <p class="text-slate-400 text-sm">{{ $invoice->visit->user->doctorProfile->clinic_name ?? 'Birawa Vet' }}</p>
                    </div>
                </div>
            </div>

            <div class="p-8">
                <!-- Info Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-10 border-b border-slate-100 pb-10">
                    <div>
                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Bill To</h4>
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-birawa-50 text-birawa-600 flex items-center justify-center font-bold text-xl flex-shrink-0 shadow-sm border border-birawa-100">
                                {{ substr($invoice->visit->patient->client->name ?? 'U', 0, 1) }}
                            </div>
                            <div>
                                <p class="font-bold text-slate-800 text-lg">{{ $invoice->visit->patient->client->name ?? 'Unknown Owner' }}</p>
                                <p class="text-sm text-slate-500 mt-1 flex items-center gap-1.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    {{ $invoice->visit->patient->client->address ?? 'No Address' }}
                                </p>
                                <p class="text-sm text-slate-500 mt-1 flex items-center gap-1.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                    {{ $invoice->visit->patient->client->phone ?? 'No Phone' }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="md:text-right">
                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Patient Details</h4>
                        <div class="inline-block bg-slate-50 rounded-2xl p-4 border border-slate-100 text-left min-w-[200px]">
                            <p class="font-bold text-slate-800 text-lg">{{ $invoice->visit->patient->name }}</p>
                            <p class="text-sm text-slate-500 mt-0.5">{{ ucfirst($invoice->visit->patient->species) }} • {{ $invoice->visit->patient->breed }}</p>
                            <div class="mt-3 pt-3 border-t border-slate-200">
                                <p class="text-xs text-slate-400 font-bold uppercase tracking-wider mb-1">Visit Date</p>
                                <p class="text-sm font-mono text-slate-600">{{ $invoice->visit->created_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Items List (Responsive) -->
                <div class="mb-8 rounded-xl border border-slate-100 overflow-hidden">
                    <!-- Mobile View (Cards) -->
                    <div class="md:hidden divide-y divide-slate-100 bg-white">
                        @foreach($invoice->invoiceItems as $item)
                            <div class="p-4 flex flex-col gap-2">
                                <div class="flex justify-between items-start">
                                    <span class="font-bold text-slate-800 text-sm">{{ $item->description }}</span>
                                    <span class="font-bold text-slate-800 text-sm font-mono">Rp {{ number_format($item->quantity * $item->unit_price, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between items-center text-xs text-slate-500">
                                    <span>{{ $item->quantity }} x Rp {{ number_format($item->unit_price, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Desktop View (Table) -->
                    <table class="w-full hidden md:table">
                        <thead class="bg-slate-50 border-b border-slate-100">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Description</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Qty</th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Price</th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($invoice->invoiceItems as $item)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="px-6 py-4 text-sm font-medium text-slate-800">{{ $item->description }}</td>
                                    <td class="px-6 py-4 text-sm text-slate-600 text-center">{{ $item->quantity }}</td>
                                    <td class="px-6 py-4 text-sm text-slate-600 text-right font-mono">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4 text-sm font-bold text-slate-800 text-right font-mono">Rp {{ number_format($item->quantity * $item->unit_price, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    
                    <!-- Footer Total (Shared) -->
                    <div class="bg-slate-50 border-t border-slate-200 p-4 md:px-6 md:py-4 flex justify-between items-center">
                        <span class="text-sm font-bold text-slate-600">Total Amount</span>
                        <span class="text-xl font-bold text-birawa-700 font-mono">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</span>
                    </div>
                </div>

                <!-- Footer -->
                <div class="text-center text-sm text-slate-400 mt-12 pt-8 border-t border-slate-100">
                    <p class="font-bold text-slate-500 mb-1">Thank you for trusting us with your pet's care!</p>
                    <p>If you have any questions about this invoice, please contact us.</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

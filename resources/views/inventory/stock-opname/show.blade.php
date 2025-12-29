<x-app-layout>
    <div class="mb-6 flex justify-between items-center">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('stock-opnames.index') }}" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <h1 class="text-3xl font-bold text-gray-800">Detail Stok Opname #{{ $stockOpname->id }}</h1>
            </div>
            <p class="text-gray-500 mt-1">
                Status: <span class="font-bold {{ $stockOpname->status === 'completed' ? 'text-green-600' : 'text-yellow-600' }}">{{ ucfirst($stockOpname->status) }}</span>
                | Mulai: {{ $stockOpname->started_at->format('d M Y H:i') }}
            </p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('stock-opnames.export', $stockOpname) }}" target="_blank" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg font-bold hover:bg-gray-50 transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Export / Print
            </a>
            @if($stockOpname->status === 'draft')
            <form action="{{ route('stock-opnames.complete', $stockOpname) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menyelesaikan stok opname ini? Stok di sistem akan diperbarui sesuai hasil hitungan.');">
                @csrf
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg font-bold hover:bg-green-700 transition-colors shadow-sm">
                    Selesai & Update Stok
                </button>
            </form>
            @endif
        </div>
    </div>

    @if($stockOpname->notes)
        <div class="bg-blue-50 border border-blue-100 rounded-lg p-4 mb-6">
            <h4 class="font-bold text-blue-800 text-sm mb-1">Catatan:</h4>
            <p class="text-blue-700 text-sm">{{ $stockOpname->notes }}</p>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">Barang / SKU</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stok Sistem</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Stok Fisik</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Selisih</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catatan Item</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($stockOpname->items as $item)
                        <tr x-data="{ 
                            system: {{ $item->system_qty }}, 
                            actual: {{ $item->actual_qty }},
                            notes: '{{ $item->notes ?? '' }}',
                            saving: false,
                            get diff() { return (this.actual - this.system).toFixed(2); },
                            update() {
                                if ({{ $stockOpname->status !== 'draft' ? 'true' : 'false' }}) return;
                                this.saving = true;
                                fetch('{{ route('stock-opnames.items.update', [$stockOpname, $item]) }}', {
                                    method: 'POST',
                                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                                    body: JSON.stringify({ actual_qty: this.actual, notes: this.notes })
                                }).then(res => res.json()).then(data => {
                                    this.saving = false;
                                }).catch(err => {
                                    alert('Gagal menyimpan perubahan');
                                    this.saving = false;
                                });
                            }
                        }" class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-gray-900">{{ $item->doctorInventory->item_name }}</div>
                                <div class="text-xs text-gray-500">{{ $item->doctorInventory->sku }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <span x-text="system"></span> {{ $item->doctorInventory->unit }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($stockOpname->status === 'draft')
                                    <div class="relative">
                                        <input type="number" step="0.01" x-model="actual" @change="update()" 
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-birawa-500 focus:ring-birawa-500 sm:text-sm">
                                        <div x-show="saving" class="absolute right-2 top-2.5 text-gray-400">
                                            <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-sm font-bold text-gray-900" x-text="actual"></span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold">
                                <span x-text="diff > 0 ? '+' + diff : diff" 
                                      :class="diff == 0 ? 'text-gray-400' : (diff > 0 ? 'text-green-600' : 'text-red-600')"></span>
                            </td>
                            <td class="px-6 py-4">
                                @if($stockOpname->status === 'draft')
                                    <input type="text" x-model="notes" @change="update()" placeholder="Catatan..." 
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-birawa-500 focus:ring-birawa-500 sm:text-xs">
                                @else
                                    <span class="text-sm text-gray-500" x-text="notes"></span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>

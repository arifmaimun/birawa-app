<x-app-layout>
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Stok Opname</h1>
            <p class="text-gray-500">Kelola dan sesuaikan stok inventaris Anda.</p>
        </div>
        <button onclick="document.getElementById('start-opname-modal').showModal()" class="bg-birawa-600 text-white px-4 py-2 rounded-lg font-bold hover:bg-birawa-700 transition-colors">
            + Mulai Stok Opname Baru
        </button>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Mulai</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catatan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Selesai Pada</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($opnames as $opname)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#{{ $opname->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $opname->started_at->format('d M Y H:i') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $opname->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ ucfirst($opname->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 truncate max-w-xs">{{ $opname->notes ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $opname->completed_at ? $opname->completed_at->format('d M Y H:i') : '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('stock-opnames.show', $opname) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Detail</a>
                            @if($opname->status === 'completed')
                                <a href="{{ route('stock-opnames.export', $opname) }}" target="_blank" class="text-gray-600 hover:text-gray-900">Export</a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">Belum ada riwayat stok opname.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $opnames->links() }}
        </div>
    </div>

    <!-- Modal Start Opname -->
    <dialog id="start-opname-modal" class="modal rounded-xl shadow-xl p-0 w-full max-w-md backdrop:bg-gray-900/50">
        <div class="bg-white p-6">
            <h3 class="font-bold text-lg mb-4">Mulai Stok Opname Baru</h3>
            <p class="text-sm text-gray-500 mb-4">Sistem akan mengambil snapshot stok saat ini. Pastikan tidak ada transaksi lain yang berjalan.</p>
            <form action="{{ route('stock-opnames.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="notes">
                        Catatan (Optional)
                    </label>
                    <textarea name="notes" id="notes" rows="3" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="Contoh: Stok Opname Bulan Desember"></textarea>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="document.getElementById('start-opname-modal').close()" class="bg-gray-200 text-gray-800 px-4 py-2 rounded-lg font-bold hover:bg-gray-300">Batal</button>
                    <button type="submit" class="bg-birawa-600 text-white px-4 py-2 rounded-lg font-bold hover:bg-birawa-700">Mulai</button>
                </div>
            </form>
        </div>
    </dialog>
</x-app-layout>

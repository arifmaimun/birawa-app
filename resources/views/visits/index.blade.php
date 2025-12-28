<x-app-layout>
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Daftar Kunjungan (Visits)</h1>
        <a href="{{ route('visits.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            + Jadwalkan Kunjungan
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal & Waktu</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pasien</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pemilik</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keluhan</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach ($visits as $visit)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ \Carbon\Carbon::parse($visit->scheduled_at)->format('d M Y H:i') }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <a href="{{ route('patients.show', $visit->patient) }}" class="text-sm text-blue-600 hover:text-blue-900 font-medium">{{ $visit->patient->name }}</a>
                        <div class="text-xs text-gray-500">{{ $visit->patient->species }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($visit->patient->owners->first())
                        <a href="{{ route('owners.show', $visit->patient->owners->first()) }}" class="text-sm text-blue-600 hover:text-blue-900">{{ $visit->patient->owners->first()->name }}</a>
                        @else
                        <span class="text-sm text-gray-500">No Owner</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex flex-col space-y-1">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $visit->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                   ($visit->status === 'cancelled' ? 'bg-red-100 text-red-800' : 
                                   ($visit->status === 'otw' ? 'bg-blue-100 text-blue-800' : 
                                   ($visit->status === 'arrived' ? 'bg-purple-100 text-purple-800' : 'bg-yellow-100 text-yellow-800'))) }}">
                                {{ ucfirst($visit->status) }}
                            </span>
                            
                            @if($visit->status !== 'completed' && $visit->status !== 'cancelled')
                                <div class="flex space-x-1 mt-1">
                                    <form action="{{ route('visits.update-status', $visit) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="status" value="otw">
                                        <button type="submit" class="text-xs text-blue-600 hover:underline" {{ $visit->status === 'otw' ? 'disabled' : '' }}>OTW</button>
                                    </form>
                                    <span class="text-gray-300">|</span>
                                    <form action="{{ route('visits.update-status', $visit) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="status" value="arrived">
                                        <button type="submit" class="text-xs text-purple-600 hover:underline" {{ $visit->status === 'arrived' ? 'disabled' : '' }}>Arrived</button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-500">{{ Str::limit($visit->complaint, 30) }}</div>
                        @if($visit->medicalRecords->count() > 0)
                            <a href="{{ route('medical-records.show', $visit->medicalRecords->first()) }}" class="block mt-1 text-xs text-green-600 font-bold hover:underline">
                                ✓ Medical Record
                            </a>
                        @else
                            <a href="{{ route('medical-records.create', $visit) }}" class="block mt-1 text-xs text-indigo-600 hover:underline">
                                + Add Record
                            </a>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        @if($visit->invoice)
                            <a href="{{ route('invoices.show', $visit->invoice) }}" class="text-green-600 hover:text-green-900 mr-3 font-bold">Invoice</a>
                        @else
                            <form action="{{ route('invoices.createFromVisit', $visit) }}" method="POST" class="inline-block mr-3">
                                @csrf
                                <button type="submit" class="text-orange-600 hover:text-orange-900 font-semibold" onclick="return confirm('Generate invoice for this visit?')">Generate Invoice</button>
                            </form>
                        @endif
                        <a href="{{ route('visits.show', $visit) }}" class="text-blue-600 hover:text-blue-900 mr-3">Lihat</a>
                        <a href="{{ route('visits.edit', $visit) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                        <form action="{{ route('visits.destroy', $visit) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="px-6 py-4">
            {{ $visits->links() }}
        </div>
    </div>
</x-app-layout>

<x-app-layout>
    <x-breadcrumb :items="[
        ['label' => 'Home', 'route' => route('dashboard')],
        ['label' => 'Pasien', 'route' => route('patients.index')],
        ['label' => $patient->name]
    ]" />

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="mb-6 flex justify-between items-center">
            <h1 class="text-3xl font-bold text-gray-800">Detail Pasien</h1>
        </div>

    <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">Informasi Hewan</h2>
        </div>
        <div class="p-6">
            <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-6">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Nama Hewan</dt>
                    <dd class="mt-1 text-lg text-gray-900">{{ $patient->name }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Pemilik</dt>
                    <dd class="mt-1 text-lg text-gray-900">
                        @if($patient->client)
                        <a href="{{ route('clients.show', $patient->client) }}" class="text-blue-600 hover:text-blue-800">{{ $patient->client->name }}</a>
                        @else
                        -
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Jenis (Spesies)</dt>
                    <dd class="mt-1 text-lg text-gray-900">{{ $patient->species }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Ras</dt>
                    <dd class="mt-1 text-lg text-gray-900">{{ $patient->breed ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Jenis Kelamin</dt>
                    <dd class="mt-1 text-lg text-gray-900">{{ ucfirst($patient->gender) }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Tanggal Lahir</dt>
                    <dd class="mt-1 text-lg text-gray-900">{{ $patient->dob ? $patient->dob : '-' }}</dd>
                </div>
            </dl>
        </div>
        <div class="bg-gray-50 px-6 py-4 flex justify-end">
            <a href="{{ route('patients.edit', $patient) }}" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded mr-2">
                Edit
            </a>
            <form action="{{ route('patients.destroy', $patient) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">Hapus</button>
            </form>
        </div>
    </div>
    
    <!-- Visit History Placeholder -->
    <div class="mb-6 flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-800">Riwayat Kunjungan</h2>
        <a href="{{ route('visits.create') }}?patient_id={{ $patient->id }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
            + Tambah Kunjungan
        </a>
    </div>
    
    <div class="bg-white rounded-lg shadow overflow-hidden">
        @if($patient->visits && $patient->visits->count() > 0)
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keluhan</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Diagnosa</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach ($patient->visits as $visit)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $visit->visit_date }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-500">{{ Str::limit($visit->complaint, 50) }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-500">{{ Str::limit($visit->diagnosis, 50) }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('visits.show', $visit) }}" class="text-blue-600 hover:text-blue-900 mr-3">Lihat</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="p-6 text-center text-gray-500">
            Belum ada riwayat kunjungan.
        </div>
        @endif
    </div>
    </div>
</x-app-layout>

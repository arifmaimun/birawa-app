<x-app-layout>
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-800">Detail Kunjungan</h1>
        <a href="{{ route('visits.index') }}" class="text-blue-500 hover:text-blue-700">Kembali ke Daftar</a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">Informasi Kunjungan</h2>
        </div>
        <div class="p-6">
            <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-6">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Tanggal & Waktu</dt>
                    <dd class="mt-1 text-lg text-gray-900">{{ \Carbon\Carbon::parse($visit->scheduled_at)->format('d F Y, H:i') }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                    <dd class="mt-1">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            {{ $visit->status === 'completed' ? 'bg-green-100 text-green-800' : 
                               ($visit->status === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                            {{ ucfirst($visit->status) }}
                        </span>
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Pasien</dt>
                    <dd class="mt-1 text-lg text-gray-900">
                        <a href="{{ route('patients.show', $visit->patient) }}" class="text-blue-600 hover:text-blue-800">{{ $visit->patient->name }}</a>
                        <span class="text-gray-500 text-sm">({{ $visit->patient->species }})</span>
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Pemilik</dt>
                    <dd class="mt-1 text-lg text-gray-900">
                        <a href="{{ route('owners.show', $visit->patient->owner) }}" class="text-blue-600 hover:text-blue-800">{{ $visit->patient->owner->name }}</a>
                    </dd>
                </div>
                <div class="col-span-1 md:col-span-2">
                    <dt class="text-sm font-medium text-gray-500">Keluhan / Alasan Kunjungan</dt>
                    <dd class="mt-1 text-lg text-gray-900">{{ $visit->complaint ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Biaya Transport</dt>
                    <dd class="mt-1 text-lg text-gray-900">Rp {{ number_format($visit->transport_fee, 0, ',', '.') }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Dokter / Petugas</dt>
                    <dd class="mt-1 text-lg text-gray-900">{{ $visit->user->name ?? 'N/A' }}</dd>
                </div>
            </dl>
        </div>
        <div class="bg-gray-50 px-6 py-4 flex justify-end">
            <a href="{{ route('visits.edit', $visit) }}" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded mr-2">
                Edit
            </a>
            <form action="{{ route('visits.destroy', $visit) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">Hapus</button>
            </form>
        </div>
    </div>
</x-app-layout>

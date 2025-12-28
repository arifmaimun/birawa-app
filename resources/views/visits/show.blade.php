<x-app-layout>
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-800">Detail Kunjungan</h1>
        <a href="{{ route('visits.index') }}" class="text-blue-500 hover:text-blue-700">Kembali ke Daftar</a>
    </div>

    @php
        $doctorProfile = Auth::user()->doctorProfile;
        $hasEmergencyContact = $doctorProfile && $doctorProfile->emergency_contact_number;
        $sosMessage = "SOS! Saya dalam bahaya saat visit (ID: " . $visit->id . "). Hubungi saya segera!";
        $sosLink = $hasEmergencyContact ? "https://wa.me/{$doctorProfile->emergency_contact_number}?text=" . urlencode($sosMessage) : '#';
    @endphp

    @if($hasEmergencyContact && in_array($visit->status, ['otw', 'arrived']))
        <div class="mb-6 bg-red-100 border-l-4 border-red-500 p-4 flex justify-between items-center">
            <div class="flex items-center">
                <svg class="h-6 w-6 text-red-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <div>
                    <h3 class="text-red-800 font-bold">Emergency / Darurat?</h3>
                    <p class="text-sm text-red-700">Tekan tombol SOS untuk mengirim pesan darurat ke kontak keluarga.</p>
                </div>
            </div>
            <a href="{{ $sosLink }}" target="_blank" id="sos-btn" class="bg-red-600 hover:bg-red-800 text-white font-bold py-3 px-6 rounded-full shadow-lg flex items-center">
                <span class="mr-2">🆘</span> SOS CHECK-IN
            </a>
        </div>
        
        <script>
            // Update SOS link with location
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    var lat = position.coords.latitude;
                    var long = position.coords.longitude;
                    var mapLink = " Lokasi: https://maps.google.com/?q=" + lat + "," + long;
                    var btn = document.getElementById('sos-btn');
                    if(btn) {
                        btn.href = btn.href + encodeURIComponent(mapLink);
                    }
                });
            }
        </script>
    @endif

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
                        @if($visit->patient->client)
                        <a href="{{ route('clients.show', $visit->patient->client) }}" class="text-blue-600 hover:text-blue-800">{{ $visit->patient->client->name }}</a>
                        @else
                        -
                        @endif
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

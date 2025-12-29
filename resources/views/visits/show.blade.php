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
        $statusSlug = $visit->visitStatus ? $visit->visitStatus->slug : 'scheduled';
    @endphp

    <!-- Doctor Actions Section -->
    @if(in_array($statusSlug, ['scheduled', 'otw', 'on-the-way']))
    <div class="mb-6 bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-birawa-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            Aksi Dokter
        </h3>
        
        @if($statusSlug == 'scheduled')
            <div class="flex flex-col sm:flex-row items-end gap-4">
                <div class="w-full sm:flex-1">
                    <label for="estimated_hours" class="block text-sm font-medium text-slate-700 mb-1">Estimasi Waktu Perjalanan (Jam)</label>
                    <input type="number" id="estimated_hours" class="w-full rounded-xl border-slate-300 shadow-sm focus:border-birawa-500 focus:ring-birawa-500" placeholder="Contoh: 0.5 atau 1" step="0.1">
                </div>
                <button onclick="startTrip()" class="w-full sm:w-auto bg-birawa-600 hover:bg-birawa-700 text-white font-bold py-2.5 px-6 rounded-xl shadow-lg shadow-birawa-500/30 transition-all active:scale-95 flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path></svg>
                    Mulai Berangkat
                </button>
            </div>
        @elseif(in_array($statusSlug, ['otw', 'on-the-way']))
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <button onclick="endTrip()" class="bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-4 px-6 rounded-xl shadow-lg shadow-emerald-500/30 transition-all active:scale-95 flex items-center justify-center gap-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Sampai di Lokasi
                </button>
                
                @if($hasEmergencyContact)
                    <a href="{{ $sosLink }}" target="_blank" class="bg-rose-500 hover:bg-rose-600 text-white font-bold py-4 px-6 rounded-xl shadow-lg shadow-rose-500/30 transition-all active:scale-95 flex items-center justify-center gap-2 animate-pulse">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        SOS / Darurat
                    </a>
                @endif
            </div>
        @endif
    </div>
    @endif

    @if($hasEmergencyContact && in_array($statusSlug, ['otw', 'on-the-way', 'arrived']))
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

    <!-- Booking / Travel Actions -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 mb-6">
        <h2 class="text-xl font-bold text-slate-800 mb-4 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-birawa-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            Status Perjalanan
        </h2>

        <div id="travel-status-container">
            @if(!$visit->departure_time)
                <!-- Not Started -->
                <div class="bg-blue-50 rounded-lg p-4 border border-blue-100">
                    <p class="text-blue-800 font-medium flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Perjalanan belum dimulai. Silakan gunakan panel "Aksi Dokter" di atas untuk memulai.
                    </p>
                </div>
            @elseif(!$visit->arrival_time)
                <!-- On The Way -->
                <div class="bg-yellow-50 rounded-lg p-4 border border-yellow-100">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="animate-pulse w-3 h-3 bg-yellow-500 rounded-full"></div>
                        <p class="text-yellow-800 font-bold text-lg">Sedang Dalam Perjalanan</p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <span class="text-sm text-yellow-600 block">Waktu Berangkat</span>
                            <span class="font-medium text-yellow-900">{{ \Carbon\Carbon::parse($visit->departure_time)->format('H:i') }}</span>
                        </div>
                        <div>
                            <span class="text-sm text-yellow-600 block">Estimasi Durasi</span>
                            <span class="font-medium text-yellow-900">{{ $visit->estimated_travel_minutes ? $visit->estimated_travel_minutes . ' menit' : '-' }}</span>
                        </div>
                    </div>
                    <button onclick="endTrip()" class="w-full sm:w-auto bg-green-600 text-white px-6 py-3 rounded-lg font-bold hover:bg-green-700 transition-colors shadow-sm flex items-center justify-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Sampai di Lokasi
                    </button>
                </div>
            @else
                <!-- Arrived / Completed -->
                <div class="bg-green-50 rounded-lg p-4 border border-green-100">
                    <div class="flex items-center gap-2 mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-green-800 font-bold text-lg">Sudah Sampai</p>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <span class="text-sm text-green-600 block">Berangkat</span>
                            <span class="font-medium text-green-900">{{ \Carbon\Carbon::parse($visit->departure_time)->format('H:i') }}</span>
                        </div>
                        <div>
                            <span class="text-sm text-green-600 block">Sampai</span>
                            <span class="font-medium text-green-900">{{ \Carbon\Carbon::parse($visit->arrival_time)->format('H:i') }}</span>
                        </div>
                        <div class="col-span-2">
                            <span class="text-sm text-green-600 block">Total Durasi</span>
                            <span class="font-bold text-green-900 text-lg">{{ $visit->actual_travel_minutes }} menit</span>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script>
        function startTrip() {
            const hours = document.getElementById('estimated_hours').value;
            
            if(!confirm('Mulai perjalanan sekarang?')) return;

            fetch('{{ route("visits.start-trip", $visit) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ estimated_hours: hours })
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    if(data.whatsapp_url) {
                        window.open(data.whatsapp_url, '_blank');
                    }
                    location.reload();
                } else {
                    alert('Terjadi kesalahan.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan koneksi.');
            });
        }

        function endTrip() {
            if(!confirm('Konfirmasi sudah sampai di lokasi?')) return;

            fetch('{{ route("visits.end-trip", $visit) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({})
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    if(data.whatsapp_url) {
                        window.open(data.whatsapp_url, '_blank');
                    }
                    alert(data.duration_report);
                    location.reload();
                } else {
                    alert('Terjadi kesalahan.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan koneksi.');
            });
        }
    </script>

    <!-- Location & Route Section -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 mb-6">
        <h2 class="text-xl font-bold text-slate-800 mb-4 flex items-center gap-2">
            <svg class="w-6 h-6 text-birawa-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            Lokasi & Rute
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-slate-500 mb-1">Alamat Pasien</label>
                <p class="text-lg font-medium text-slate-900 mb-4">
                    {{ $visit->patient->client->address ?? 'Alamat tidak tersedia' }}
                </p>
                
                @if($visit->latitude && $visit->longitude)
                    <div class="flex gap-4">
                        <a href="https://www.google.com/maps/dir/?api=1&destination={{ $visit->latitude }},{{ $visit->longitude }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0121 18.382V7.618a1 1 0 01-1.447-.894L15 7m0 13V7"></path></svg>
                            Navigasi Rute
                        </a>
                    </div>
                @else
                    <p class="text-sm text-yellow-600 bg-yellow-50 p-3 rounded-lg border border-yellow-200">
                        Koordinat belum diatur. Navigasi akan menggunakan alamat text.
                    </p>
                    @if($visit->patient->client && $visit->patient->client->address)
                        <a href="https://www.google.com/maps/dir/?api=1&destination={{ urlencode($visit->patient->client->address) }}" target="_blank" class="mt-2 inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0121 18.382V7.618a1 1 0 01-1.447-.894L15 7m0 13V7"></path></svg>
                            Navigasi via Alamat
                        </a>
                    @endif
                @endif
            </div>
            
            <div class="h-64 bg-slate-100 rounded-xl border border-slate-200 flex items-center justify-center overflow-hidden relative">
                @if($visit->latitude && $visit->longitude)
                    <!-- Simple static map fallback or iframe if key existed -->
                    <iframe 
                        width="100%" 
                        height="100%" 
                        style="border:0" 
                        loading="lazy" 
                        allowfullscreen 
                        referrerpolicy="no-referrer-when-downgrade"
                        src="https://maps.google.com/maps?q={{ $visit->latitude }},{{ $visit->longitude }}&z=15&output=embed">
                    </iframe>
                @else
                    <div class="text-center p-6 text-slate-400">
                        <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <p>Peta tidak tersedia</p>
                    </div>
                @endif
            </div>
        </div>
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
                        @php
                            $statusName = $visit->visitStatus ? $visit->visitStatus->name : ucfirst($visit->status ?? 'Unknown');
                            $statusSlug = $visit->visitStatus ? $visit->visitStatus->slug : ($visit->status ?? 'unknown');
                            
                            $badgeColor = match($statusSlug) {
                                'completed', 'done' => 'bg-green-100 text-green-800',
                                'cancelled', 'canceled' => 'bg-red-100 text-red-800',
                                'arrived' => 'bg-blue-100 text-blue-800',
                                'otw', 'on-the-way' => 'bg-yellow-100 text-yellow-800',
                                default => 'bg-gray-100 text-gray-800'
                            };
                        @endphp
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $badgeColor }}">
                            {{ $statusName }}
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

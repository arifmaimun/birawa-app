<x-app-layout>
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="mb-6">
            <a href="{{ route('message-templates.index') }}" class="text-slate-500 hover:text-birawa-600 flex items-center gap-2 mb-2 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Daftar Template
            </a>
            <h2 class="text-2xl font-bold text-slate-800">Buat Template Pesan</h2>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <form action="{{ route('message-templates.store') }}" method="POST">
                @csrf

                <div class="space-y-6">
                    <!-- Title -->
                    <div>
                        <x-input-label for="title" :value="__('Judul Template')" />
                        <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title')" required autofocus placeholder="Contoh: Notifikasi Keberangkatan" />
                        <x-input-error :messages="$errors->get('title')" class="mt-2" />
                    </div>

                    <!-- Type -->
                    <div>
                        <x-input-label for="type" :value="__('Tipe Pesan')" />
                        <select id="type" name="type" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-birawa-500 focus:ring-birawa-500">
                            <option value="whatsapp" {{ old('type') == 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                            <option value="email" {{ old('type') == 'email' ? 'selected' : '' }}>Email</option>
                            <option value="sms" {{ old('type') == 'sms' ? 'selected' : '' }}>SMS</option>
                        </select>
                        <x-input-error :messages="$errors->get('type')" class="mt-2" />
                    </div>

                    <!-- Trigger Event -->
                    <div>
                        <x-input-label for="trigger_event" :value="__('Pemicu Otomatis (Opsional)')" />
                        <select id="trigger_event" name="trigger_event" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-birawa-500 focus:ring-birawa-500">
                            <option value="">-- Tidak Ada Pemicu Otomatis --</option>
                            <option value="on_departure" {{ old('trigger_event') == 'on_departure' ? 'selected' : '' }}>Saat Dokter Berangkat (Tombol "Mulai Berangkat")</option>
                            <option value="on_arrival" {{ old('trigger_event') == 'on_arrival' ? 'selected' : '' }}>Saat Dokter Sampai (Tombol "Sampai")</option>
                        </select>
                        <p class="mt-1 text-sm text-gray-500">Jika dipilih, template ini akan otomatis digunakan saat Anda menekan tombol aksi terkait di halaman kunjungan.</p>
                        <x-input-error :messages="$errors->get('trigger_event')" class="mt-2" />
                    </div>

                    <!-- Content -->
                    <div>
                        <x-input-label for="content_pattern" :value="__('Isi Pesan')" />
                        <textarea id="content_pattern" name="content_pattern" rows="6" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-birawa-500 focus:ring-birawa-500" required placeholder="Halo {owner_name}, dokter hewan {doctor_name} sedang menuju ke lokasi Anda. Estimasi sampai {eta}.">{{ old('content_pattern') }}</textarea>
                        <p class="mt-2 text-sm text-gray-500">
                            Variabel yang tersedia: <br>
                            <code class="bg-gray-100 px-1 py-0.5 rounded text-birawa-600">{owner_name}</code> Nama pemilik hewan<br>
                            <code class="bg-gray-100 px-1 py-0.5 rounded text-birawa-600">{doctor_name}</code> Nama dokter<br>
                            <code class="bg-gray-100 px-1 py-0.5 rounded text-birawa-600">{patient_name}</code> Nama hewan<br>
                            <code class="bg-gray-100 px-1 py-0.5 rounded text-birawa-600">{eta}</code> Estimasi waktu sampai (hanya tersedia jika estimasi diisi)<br>
                            <code class="bg-gray-100 px-1 py-0.5 rounded text-birawa-600">{address}</code> Alamat kunjungan
                        </p>
                        <x-input-error :messages="$errors->get('content_pattern')" class="mt-2" />
                    </div>
                </div>

                <div class="mt-8 flex justify-end gap-4">
                    <a href="{{ route('message-templates.index') }}" class="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-birawa-500 transition-colors">
                        Batal
                    </a>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-birawa-600 border border-transparent rounded-lg hover:bg-birawa-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-birawa-500 transition-colors">
                        Simpan Template
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

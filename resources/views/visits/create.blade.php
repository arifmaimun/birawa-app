<x-app-layout>
    <!-- Top Navigation Bar -->
    <div class="sticky top-0 z-30 flex items-center justify-between px-4 py-3 bg-white border-b border-gray-200 shadow-sm">
        <a href="{{ route('visits.index') }}" class="p-2 -ml-2 text-gray-600 rounded-full hover:bg-gray-100">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        </a>
        <h1 class="text-lg font-bold text-gray-800">Booking Baru</h1>
        <div class="w-8"></div> <!-- Spacer for centering -->
    </div>

    <div class="max-w-xl px-4 py-6 mx-auto pb-28"> <!-- Added padding bottom for fixed button -->
        <form action="{{ route('visits.store') }}" method="POST">
            @csrf
            
            <!-- Default Status (Hidden) -->
            <input type="hidden" name="status" value="scheduled">

            <!-- Section: Patient Info (Searchable) -->
            <div class="mb-6" x-data="{
                search: '',
                open: false,
                selectedId: '{{ request('patient_id') ?? old('patient_id') ?? '' }}',
                patients: {{ \Illuminate\Support\Js::from($patients->map(fn($p) => [
                    'id' => $p->id, 
                    'name' => $p->name, 
                    'owner' => $p->client->name ?? 'No Owner',
                    'address' => $p->client->address ?? '',
                    'display' => $p->name . ' (' . ($p->client->name ?? 'No Owner') . ')'
                ])) }},
                
                init() {
                    if (this.selectedId) {
                        let selected = this.patients.find(p => p.id == this.selectedId);
                        if (selected) {
                            this.search = selected.display;
                        }
                    }
                },

                get filteredPatients() {
                    if (this.search === '') return this.patients;
                    return this.patients.filter(p => {
                        return p.name.toLowerCase().includes(this.search.toLowerCase()) || 
                               p.owner.toLowerCase().includes(this.search.toLowerCase());
                    });
                },
                
                get selectedPatientAddress() {
                    if (!this.selectedId) return '';
                    let p = this.patients.find(p => p.id == this.selectedId);
                    return p ? p.address : '';
                },

                selectPatient(patient) {
                    this.selectedId = patient.id;
                    this.search = patient.display;
                    this.open = false;
                }
            }">
                <label class="block mb-2 text-sm font-semibold text-gray-700">Pilih Pasien</label>
                
                <!-- Hidden Input for Form Submission -->
                <input type="hidden" name="patient_id" :value="selectedId" required>

                <div class="relative">
                    <!-- Search Input Wrapper -->
                    <div class="relative">
                         <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input 
                            type="text" 
                            x-model="search"
                            @focus="open = true" 
                            @click.away="open = false"
                            @input="open = true; selectedId = ''" 
                            class="block w-full py-3 pl-10 pr-10 text-gray-700 bg-white border border-gray-300 rounded-xl focus:ring-birawa-500 focus:border-birawa-500 shadow-sm transition-all placeholder-gray-400"
                            placeholder="Cari nama pasien atau pemilik..."
                            autocomplete="off"
                        >
                        <!-- Clear Button -->
                        <button type="button" x-show="search.length > 0" @click="search = ''; selectedId = ''; open = true; $refs.searchInput.focus()" class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-400 hover:text-gray-600 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <!-- Dropdown Results -->
                    <div x-show="open && filteredPatients.length > 0" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="absolute z-50 w-full mt-2 bg-white border border-gray-100 rounded-xl shadow-xl max-h-60 overflow-y-auto overflow-x-hidden">
                        <template x-for="patient in filteredPatients" :key="patient.id">
                            <div @click="selectPatient(patient)" class="px-4 py-3 cursor-pointer hover:bg-blue-50 border-b border-gray-50 last:border-none transition-colors group">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <div class="font-medium text-gray-800 group-hover:text-blue-700" x-text="patient.name"></div>
                                        <div class="text-xs text-gray-500 group-hover:text-blue-600" x-text="'Owner: ' + patient.owner"></div>
                                    </div>
                                    <!-- Checkmark icon if selected -->
                                    <svg x-show="selectedId == patient.id" class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Empty State -->
                    <div x-show="open && filteredPatients.length === 0" class="absolute z-50 w-full mt-2 bg-white border border-gray-100 rounded-xl shadow-xl p-4 text-center">
                        <p class="text-sm text-gray-500 mb-2">Pasien tidak ditemukan.</p>
                        <a href="{{ route('clients.create') }}" class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-birawa-600 bg-birawa-50 rounded-lg hover:bg-birawa-100 transition-colors">
                            + Tambah Client Baru
                        </a>
                    </div>
                </div>
                
                <!-- Helper text / Validation feedback -->
                <p x-show="search.length > 0 && !selectedId" class="mt-2 text-xs text-amber-600 flex items-center animate-pulse">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    Silakan klik nama pasien dari daftar untuk memilih.
                </p>
                <p x-show="!search && !selectedId" class="mt-2 text-xs text-gray-500">
                    Ketik nama hewan atau nama pemilik untuk mencari.
                </p>
            </div>

            <!-- Section: Schedule -->
            <div class="p-5 mb-6 bg-white border border-gray-100 shadow-sm rounded-2xl">
                <div class="flex items-center gap-3 mb-4">
                    <div class="flex items-center justify-center w-8 h-8 text-blue-600 bg-blue-100 rounded-full">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <h2 class="text-sm font-bold text-gray-800">Waktu Kunjungan</h2>
                </div>
                
                <div class="relative">
                    <input type="datetime-local" name="scheduled_at" id="scheduled_at" 
                        class="block w-full px-4 py-3 text-gray-700 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-birawa-500 focus:border-birawa-500 transition-colors"
                        value="{{ now()->format('Y-m-d\TH:i') }}" required>
                </div>
            </div>

            <!-- Section: Details -->
            <div class="p-5 mb-6 bg-white border border-gray-100 shadow-sm rounded-2xl">
                <div class="flex items-center gap-3 mb-4">
                    <div class="flex items-center justify-center w-8 h-8 text-orange-600 bg-orange-100 rounded-full">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <h2 class="text-sm font-bold text-gray-800">Detail Keluhan</h2>
                </div>

                <div class="mb-4">
                    <textarea name="complaint" id="complaint" rows="4" 
                        class="block w-full px-4 py-3 text-gray-700 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-birawa-500 focus:border-birawa-500 transition-colors"
                        placeholder="Jelaskan kondisi hewan, gejala, atau alasan kunjungan..."></textarea>
                </div>
            </div>

            <!-- Fixed Bottom Action Bar -->
            <div class="fixed bottom-20 left-0 right-0 p-4 bg-white/80 backdrop-blur-md border-t border-gray-200 z-20"> <!-- bottom-20 to sit above nav -->
                <div class="max-w-xl mx-auto">
                    <button type="submit" class="w-full px-4 py-3.5 text-center font-bold text-white bg-gradient-to-r from-birawa-600 to-birawa-500 rounded-xl shadow-lg shadow-birawa-500/30 hover:shadow-birawa-500/50 transform active:scale-[0.98] transition-all">
                        Buat Jadwal
                    </button>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>
<x-app-layout>
    <div class="max-w-xl px-4 py-6 mx-auto"> <!-- Added padding bottom for fixed button removed -->
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
                                    <svg x-show="selectedId == patient.id" class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                            </div>
                        </template>
                    </div>
                    
                    <!-- No Results -->
                    <div x-show="open && filteredPatients.length === 0" class="absolute z-50 w-full mt-2 bg-white border border-gray-100 rounded-xl shadow-xl p-4 text-center text-gray-500">
                        <p class="mb-2">Tidak ada pasien ditemukan.</p>
                        <a href="{{ route('clients.create', ['return_to' => 'visits.create']) }}" class="inline-block px-4 py-2 bg-birawa-600 text-white text-sm font-bold rounded-lg hover:bg-birawa-700 transition-colors">
                            + Tambah Klien Baru
                        </a>
                    </div>

                </div>

                <!-- Selected Patient Address Preview -->
                <div x-show="selectedId" class="mt-3 p-3 bg-blue-50 text-blue-800 rounded-lg text-sm border border-blue-100 flex items-start gap-2">
                    <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <div>
                        <span class="font-bold">Alamat:</span> <span x-text="selectedPatientAddress || 'Alamat tidak tersedia'"></span>
                    </div>
                </div>
            </div>

            <!-- Section: Date & Time -->
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block mb-2 text-sm font-semibold text-gray-700">Tanggal</label>
                    <input type="date" name="scheduled_date" value="{{ date('Y-m-d') }}" class="block w-full px-4 py-3 text-gray-700 bg-white border border-gray-300 rounded-xl focus:ring-birawa-500 focus:border-birawa-500 shadow-sm" required>
                </div>
                <div>
                    <label class="block mb-2 text-sm font-semibold text-gray-700">Jam</label>
                    <input type="time" name="scheduled_time" value="{{ date('H:i') }}" class="block w-full px-4 py-3 text-gray-700 bg-white border border-gray-300 rounded-xl focus:ring-birawa-500 focus:border-birawa-500 shadow-sm" required>
                </div>
            </div>

            <!-- Section: Notes -->
            <div class="mb-6">
                <label class="block mb-2 text-sm font-semibold text-gray-700">Catatan (Keluhan/Tujuan)</label>
                <textarea name="notes" rows="4" class="block w-full px-4 py-3 text-gray-700 bg-white border border-gray-300 rounded-xl focus:ring-birawa-500 focus:border-birawa-500 shadow-sm" placeholder="Contoh: Vaksinasi tahunan, atau Kucing muntah-muntah..."></textarea>
            </div>

            <!-- Submit Button (Static) -->
            <div class="mt-8 flex flex-col md:flex-row justify-end items-center gap-4">
                <button type="submit" class="w-full md:w-auto md:min-w-[200px] px-6 py-3 font-bold text-white bg-birawa-600 rounded-xl hover:bg-birawa-700 transition-colors flex justify-center items-center gap-2 min-h-[44px]">
                    <span>Buat Jadwal Kunjungan</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
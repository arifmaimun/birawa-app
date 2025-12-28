<x-app-layout>
    <!-- Top Navigation Bar (Sticky) -->
    <div class="sticky top-0 z-30 flex items-center justify-between px-4 py-3 bg-white border-b border-gray-200 shadow-sm">
        <a href="{{ route('clients.index') }}" class="p-2 -ml-2 text-gray-600 rounded-full hover:bg-gray-100">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        </a>
        <h1 class="text-lg font-bold text-gray-800">Registrasi Klien & Pasien</h1>
        <div class="w-8"></div> 
    </div>

    <div class="max-w-3xl px-4 py-6 mx-auto pb-28">
        <form action="{{ route('clients.store') }}" method="POST">
            @csrf

            <!-- Section 1: Info Umum Klien -->
            <div class="mb-6 overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
                <!-- Header Section -->
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                    <h2 class="text-sm font-bold tracking-wide text-gray-800 uppercase">1. Informasi Klien</h2>
                    <span class="text-xs font-semibold text-blue-600 bg-blue-50 px-2 py-1 rounded">Wajib</span>
                </div>
                
                <div class="p-6">
                    <!-- Grid Layout -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nama Lengkap (Full Width) -->
                        <div class="md:col-span-2">
                            <label for="name" class="block mb-1 text-xs font-bold text-gray-500 uppercase">Nama Lengkap *</label>
                            <input type="text" name="name" id="name" 
                                class="block w-full px-3 py-2.5 text-gray-900 bg-white border border-gray-300 rounded-lg focus:ring-birawa-500 focus:border-birawa-500 sm:text-sm" 
                                placeholder="Nama Depan & Belakang" required>
                        </div>

                        <!-- Phone -->
                        <div>
                            <label for="phone" class="block mb-1 text-xs font-bold text-gray-500 uppercase">No. Telepon / HP *</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                </div>
                                <input type="tel" name="phone" id="phone" 
                                    class="block w-full pl-10 pr-3 py-2.5 text-gray-900 bg-white border border-gray-300 rounded-lg focus:ring-birawa-500 focus:border-birawa-500 sm:text-sm" 
                                    placeholder="0812xxxx" required>
                            </div>
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block mb-1 text-xs font-bold text-gray-500 uppercase">Email</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 00-2-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                </div>
                                <input type="email" name="email" id="email" 
                                    class="block w-full pl-10 pr-3 py-2.5 text-gray-900 bg-white border border-gray-300 rounded-lg focus:ring-birawa-500 focus:border-birawa-500 sm:text-sm" 
                                    placeholder="klien@email.com">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 2: Alamat Klien -->
            <div class="mb-6 overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
                <!-- Header Section -->
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                    <h2 class="text-sm font-bold tracking-wide text-gray-800 uppercase">2. Alamat Domisili</h2>
                    <span class="px-2 py-1 text-xs font-semibold text-green-700 bg-green-100 rounded">Utama</span>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label for="address" class="block mb-1 text-xs font-bold text-gray-500 uppercase">Alamat Jalan</label>
                            <textarea name="address" id="address" rows="2" 
                                class="block w-full px-3 py-2.5 text-gray-900 bg-white border border-gray-300 rounded-lg focus:ring-birawa-500 focus:border-birawa-500 sm:text-sm" 
                                placeholder="Nama Jalan, Nomor Rumah, RT/RW"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 3: Informasi Hewan Peliharaan (Baru) -->
            <div class="mb-6 overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
                <!-- Header Section -->
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                    <div class="flex items-center gap-2">
                        <h2 class="text-sm font-bold tracking-wide text-gray-800 uppercase">3. Hewan Peliharaan</h2>
                    </div>
                    <span class="px-2 py-1 text-xs font-semibold text-purple-700 bg-purple-100 rounded">Pasien Pertama</span>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 gap-6">
                        <!-- Nama Hewan -->
                        <div>
                            <label for="patient_name" class="block mb-1 text-xs font-bold text-gray-500 uppercase">Nama Hewan *</label>
                            <input type="text" name="patient_name" id="patient_name" 
                                class="block w-full px-3 py-2.5 text-gray-900 bg-white border border-gray-300 rounded-lg focus:ring-birawa-500 focus:border-birawa-500 sm:text-sm" 
                                placeholder="Contoh: Mochi" required>
                        </div>

                        <!-- Species (Smart Select) -->
                        <div x-data="{
                            inputType: 'select',
                            selectedSpecies: '',
                            customSpecies: '',
                            options: ['Kucing', 'Anjing', 'Kelinci', 'Burung', 'Hamster', 'Reptil', 'Ikan'],
                            
                            init() {
                                this.$watch('selectedSpecies', (value) => {
                                    if (value === 'other') {
                                        this.inputType = 'text';
                                        this.customSpecies = '';
                                        this.$nextTick(() => $refs.customInput.focus());
                                    } else {
                                        this.customSpecies = value;
                                    }
                                });
                            },
                            
                            resetToSelect() {
                                this.inputType = 'select';
                                this.selectedSpecies = '';
                                this.customSpecies = '';
                            }
                        }">
                            <label class="block mb-1 text-xs font-bold text-gray-500 uppercase">Jenis Hewan (Spesies) *</label>
                            
                            <!-- Hidden Input sent to backend -->
                            <input type="hidden" name="species" :value="inputType === 'text' ? customSpecies : selectedSpecies">

                            <!-- Select Input -->
                            <div x-show="inputType === 'select'">
                                <select x-model="selectedSpecies" class="block w-full px-3 py-2.5 text-gray-900 bg-white border border-gray-300 rounded-lg focus:ring-birawa-500 focus:border-birawa-500 sm:text-sm">
                                    <option value="">-- Pilih Spesies --</option>
                                    <template x-for="option in options">
                                        <option :value="option" x-text="option"></option>
                                    </template>
                                    <option value="other" class="font-bold text-blue-600 bg-blue-50">+ Tambah Lainnya...</option>
                                </select>
                            </div>

                            <!-- Text Input (Custom) -->
                            <div x-show="inputType === 'text'" class="flex gap-2" style="display: none;">
                                <input 
                                    x-ref="customInput"
                                    type="text" 
                                    x-model="customSpecies"
                                    class="block w-full px-3 py-2.5 text-gray-900 bg-white border border-blue-500 rounded-lg focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                                    placeholder="Ketik jenis hewan..."
                                >
                                <button type="button" @click="resetToSelect()" class="px-3 py-2 text-gray-500 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </div>
                        </div>

                        <!-- Breed -->
                        <div>
                            <label for="breed" class="block mb-1 text-xs font-bold text-gray-500 uppercase">Ras (Breed)</label>
                            <input type="text" name="breed" id="breed" 
                                class="block w-full px-3 py-2.5 text-gray-900 bg-white border border-gray-300 rounded-lg focus:ring-birawa-500 focus:border-birawa-500 sm:text-sm" 
                                placeholder="Contoh: Domestik, Persia, Golden Retriever">
                        </div>

                        <!-- Row: DOB & Steril -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="dob" class="block mb-1 text-xs font-bold text-gray-500 uppercase">Tgl Lahir (Est.)</label>
                                <input type="date" name="dob" id="dob" 
                                    class="block w-full px-3 py-2.5 text-gray-900 bg-white border border-gray-300 rounded-lg focus:ring-birawa-500 focus:border-birawa-500 sm:text-sm">
                            </div>

                            <div>
                                <label class="block mb-1 text-xs font-bold text-gray-500 uppercase">Status Steril</label>
                                <div class="flex gap-0 rounded-lg border border-gray-300 overflow-hidden h-[42px]">
                                    <label class="flex-1 cursor-pointer h-full border-r border-gray-300">
                                        <input type="radio" name="is_sterile" value="0" class="peer sr-only" checked>
                                        <div class="h-full flex items-center justify-center bg-gray-50 text-gray-600 text-sm peer-checked:bg-gray-200 peer-checked:text-gray-900 peer-checked:font-semibold transition-all hover:bg-gray-100">
                                            Belum
                                        </div>
                                    </label>
                                    <label class="flex-1 cursor-pointer h-full">
                                        <input type="radio" name="is_sterile" value="1" class="peer sr-only">
                                        <div class="h-full flex items-center justify-center bg-gray-50 text-gray-600 text-sm peer-checked:bg-purple-100 peer-checked:text-purple-700 peer-checked:font-semibold transition-all hover:bg-gray-100">
                                            Sudah
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Gender -->
                        <div>
                            <label class="block mb-2 text-xs font-bold text-gray-500 uppercase">Jenis Kelamin</label>
                            <div class="flex gap-3">
                                <label class="flex-1 cursor-pointer group">
                                    <input type="radio" name="gender" value="jantan" class="peer sr-only" required>
                                    <div class="flex items-center justify-center py-2.5 px-4 rounded-lg border border-gray-300 bg-white text-gray-500 peer-checked:border-blue-500 peer-checked:bg-blue-50 peer-checked:text-blue-700 transition-all">
                                        <span class="text-base mr-2">♂</span> 
                                        <span class="text-sm font-medium">Jantan</span>
                                    </div>
                                </label>
                                <label class="flex-1 cursor-pointer group">
                                    <input type="radio" name="gender" value="betina" class="peer sr-only" required>
                                    <div class="flex items-center justify-center py-2.5 px-4 rounded-lg border border-gray-300 bg-white text-gray-500 peer-checked:border-pink-500 peer-checked:bg-pink-50 peer-checked:text-pink-700 transition-all">
                                        <span class="text-base mr-2">♀</span>
                                        <span class="text-sm font-medium">Betina</span>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Fixed Bottom Action Bar -->
            <div class="fixed bottom-20 left-0 right-0 p-4 bg-white/90 backdrop-blur-md border-t border-gray-200 z-20"> 
                <div class="max-w-3xl mx-auto flex justify-end gap-3">
                    <a href="{{ route('clients.index') }}" class="px-6 py-2.5 text-sm font-bold text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors shadow-sm">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2.5 text-sm font-bold text-white bg-birawa-600 rounded-lg shadow-md hover:bg-birawa-700 focus:ring-2 focus:ring-offset-2 focus:ring-birawa-500 transition-all">
                        Simpan Semua
                    </button>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>

@props(['clients', 'patient' => null, 'prefix' => '', 'hideClientSelection' => false, 'withCard' => true])

<!-- Section: Client Selection (Searchable) -->
@if(!$hideClientSelection)
<div class="mb-6 bg-white border border-gray-100 shadow-sm rounded-2xl p-5" x-data="{
    search: '',
    open: false,
    selectedId: '{{ old('client_id', $patient?->client_id) ?? '' }}',
    clients: {{ \Illuminate\Support\Js::from($clients->map(fn($c) => [
        'id' => $c->id, 
        'name' => $c->name, 
        'phone' => $c->phone ?? 'No Phone',
        'display' => $c->name . ' (' . ($c->phone ?? '-') . ')'
    ])) }},
    
    init() {
        if (this.selectedId) {
            let selected = this.clients.find(c => c.id == this.selectedId);
            if (selected) {
                this.search = selected.display;
            }
        }
    },

    get filteredClients() {
        if (this.search === '') return this.clients;
        return this.clients.filter(c => {
            return c.name.toLowerCase().includes(this.search.toLowerCase()) || 
                   c.phone.toLowerCase().includes(this.search.toLowerCase());
        });
    },

    selectClient(client) {
        this.selectedId = client.id;
        this.search = client.display;
        this.open = false;
    }
}">
    <div class="flex items-center gap-3 mb-4">
        <div class="flex items-center justify-center w-8 h-8 text-birawa-600 bg-birawa-100 rounded-full">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
        </div>
        <h2 class="text-sm font-bold text-gray-800">Pilih Klien (Pemilik)</h2>
    </div>
    
    <!-- Hidden Input for Form Submission -->
    <input type="hidden" name="client_id" :value="selectedId" required>

    <div class="relative">
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
                placeholder="Cari nama klien atau no. telp..."
                autocomplete="off"
            >
            <!-- Clear Button -->
            <button type="button" x-show="search.length > 0" @click="search = ''; selectedId = ''; open = true; $refs.searchInput.focus()" class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <!-- Dropdown Results -->
        <div x-show="open && filteredClients.length > 0" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="absolute z-50 w-full mt-2 bg-white border border-gray-100 rounded-xl shadow-xl max-h-60 overflow-y-auto overflow-x-hidden">
            <template x-for="client in filteredClients" :key="client.id">
                <div @click="selectClient(client)" class="px-4 py-3 cursor-pointer hover:bg-birawa-50 border-b border-gray-50 last:border-none transition-colors group">
                    <div class="flex justify-between items-center">
                        <div>
                            <div class="font-medium text-gray-800 group-hover:text-birawa-700" x-text="client.name"></div>
                            <div class="text-xs text-gray-500 group-hover:text-birawa-600" x-text="client.phone"></div>
                        </div>
                        <!-- Checkmark icon if selected -->
                        <svg x-show="selectedId == client.id" class="w-5 h-5 text-birawa-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                </div>
            </template>
        </div>

        <!-- Empty State -->
        <div x-show="open && filteredClients.length === 0" class="absolute z-50 w-full mt-2 bg-white border border-gray-100 rounded-xl shadow-xl p-4 text-center">
            <p class="text-sm text-gray-500 mb-2">Klien tidak ditemukan.</p>
            <a href="{{ route('clients.create') }}" class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-birawa-600 bg-birawa-50 rounded-lg hover:bg-birawa-100 transition-colors">
                + Tambah Klien Baru
            </a>
        </div>
    </div>

    <p x-show="search.length > 0 && !selectedId" class="mt-2 text-xs text-amber-600 flex items-center animate-pulse">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
        Silakan klik nama klien dari daftar untuk memilih.
    </p>
</div>
@endif

<!-- Section: Patient Details -->
<div class="{{ $withCard ? 'p-5 mb-6 bg-white border border-gray-100 shadow-sm rounded-2xl' : '' }}" x-data="{
    dobMode: 'date',
    dobDate: '{{ old($prefix . 'dob', $patient?->dob) }}',
    ageYears: 0,
    ageMonths: 0,

    init() {
        if (this.dobDate) {
            this.dobMode = 'date';
        }
    },

    calculateDobFromAge() {
        const today = new Date();
        let birthDate = new Date(today);
        birthDate.setFullYear(today.getFullYear() - (parseInt(this.ageYears) || 0));
        birthDate.setMonth(today.getMonth() - (parseInt(this.ageMonths) || 0));
        
        const yyyy = birthDate.getFullYear();
        const mm = String(birthDate.getMonth() + 1).padStart(2, '0');
        const dd = String(birthDate.getDate()).padStart(2, '0');
        
        this.dobDate = `${yyyy}-${mm}-${dd}`;
    }
}">
    @if($withCard)
    <div class="flex items-center gap-3 mb-4">
        <div class="flex items-center justify-center w-8 h-8 text-blue-600 bg-blue-100 rounded-full">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <h2 class="text-sm font-bold text-gray-800">Informasi Pasien</h2>
    </div>
    @endif

    <div class="grid grid-cols-1 gap-6">
        <!-- Name -->
        <div>
            <label for="{{ $prefix }}name" class="block mb-1 text-xs font-bold text-gray-500 uppercase">Nama Hewan *</label>
            <input type="text" name="{{ $prefix }}name" id="{{ $prefix }}name" value="{{ old($prefix . 'name', $patient?->name) }}" class="block w-full px-3 py-2.5 text-gray-900 bg-white border border-gray-300 rounded-lg focus:ring-birawa-500 focus:border-birawa-500 sm:text-sm" required placeholder="Contoh: Mochi">
        </div>

        <!-- Species (Smart Select) -->
        <div x-data="dropdownInput(['Kucing', 'Anjing', 'Kelinci', 'Burung', 'Hamster', 'Reptil', 'Ikan'], '{{ old($prefix . 'species', $patient?->species) }}')">
            <label class="block mb-1 text-xs font-bold text-gray-500 uppercase">Jenis Hewan (Spesies) *</label>
            
            <!-- Hidden Input sent to backend -->
            <input type="hidden" name="{{ $prefix }}species" :value="finalValue" required>

            <!-- Select Input -->
            <div x-show="!isCustom">
                <select x-model="selectedValue" class="block w-full px-3 py-2.5 text-gray-900 bg-white border border-gray-300 rounded-lg focus:ring-birawa-500 focus:border-birawa-500 sm:text-sm">
                    <option value="">-- Pilih Spesies --</option>
                    <template x-for="option in options" :key="option">
                        <option :value="option" x-text="option" :selected="option === selectedValue"></option>
                    </template>
                    <option value="custom" class="font-bold text-blue-600 bg-blue-50">+ Tambah Baru / Lainnya...</option>
                </select>
            </div>

            <!-- Text Input (Custom) -->
            <div x-show="isCustom" class="flex gap-2">
                <input 
                    type="text" 
                    x-model="customValue"
                    class="block w-full px-3 py-2.5 text-gray-900 bg-white border border-blue-500 rounded-lg focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                    placeholder="Ketik jenis hewan baru..."
                >
                <button type="button" @click="reset()" class="px-3 py-2 text-gray-500 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors" title="Kembali ke pilihan">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        </div>

        <!-- Breed -->
        <div>
            <label for="{{ $prefix }}breed" class="block mb-1 text-xs font-bold text-gray-500 uppercase">Ras (Breed)</label>
            <input type="text" name="{{ $prefix }}breed" id="{{ $prefix }}breed" value="{{ old($prefix . 'breed', $patient?->breed) }}" class="block w-full px-3 py-2.5 text-gray-900 bg-white border border-gray-300 rounded-lg focus:ring-birawa-500 focus:border-birawa-500 sm:text-sm" placeholder="Contoh: Domestik, Persia, Golden Retriever">
        </div>

        <!-- DOB / Age -->
        <div class="md:col-span-2">
            <label class="block mb-1 text-xs font-bold text-gray-500 uppercase">Tanggal Lahir / Umur Pasien</label>
            <div class="flex gap-4 mb-2">
                <label class="inline-flex items-center">
                    <input type="radio" x-model="dobMode" value="date" class="text-birawa-600 focus:ring-birawa-500">
                    <span class="ml-2 text-sm text-gray-700">Input Tanggal</span>
                </label>
                <label class="inline-flex items-center">
                    <input type="radio" x-model="dobMode" value="age" class="text-birawa-600 focus:ring-birawa-500">
                    <span class="ml-2 text-sm text-gray-700">Input Umur</span>
                </label>
            </div>
            
            <div x-show="dobMode === 'date'">
                <input type="date" name="{{ $prefix }}dob" x-model="dobDate" class="block w-full px-3 py-2.5 text-gray-900 bg-white border border-gray-300 rounded-lg focus:ring-birawa-500 focus:border-birawa-500 sm:text-sm">
            </div>
            
            <div x-show="dobMode === 'age'" class="flex gap-4 items-center">
                <div class="w-1/2">
                    <div class="flex items-center">
                        <input type="number" x-model="ageYears" @input="calculateDobFromAge" min="0" class="block w-full px-3 py-2.5 text-gray-900 bg-white border border-gray-300 rounded-l-lg focus:ring-birawa-500 focus:border-birawa-500 sm:text-sm">
                        <span class="inline-flex items-center px-3 py-2.5 rounded-r-lg border border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">Tahun</span>
                    </div>
                </div>
                <div class="w-1/2">
                    <div class="flex items-center">
                        <input type="number" x-model="ageMonths" @input="calculateDobFromAge" min="0" max="11" class="block w-full px-3 py-2.5 text-gray-900 bg-white border border-gray-300 rounded-l-lg focus:ring-birawa-500 focus:border-birawa-500 sm:text-sm">
                        <span class="inline-flex items-center px-3 py-2.5 rounded-r-lg border border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">Bulan</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Row: Gender & Steril -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Gender -->
            <div>
                <label class="block mb-1 text-xs font-bold text-gray-500 uppercase">Jenis Kelamin *</label>
                <select name="{{ $prefix }}gender" class="block w-full px-3 py-2.5 text-gray-900 bg-white border border-gray-300 rounded-lg focus:ring-birawa-500 focus:border-birawa-500 sm:text-sm" required>
                    <option value="">-- Pilih --</option>
                    <option value="Jantan" {{ strtolower(old($prefix . 'gender', $patient?->gender)) === 'jantan' ? 'selected' : '' }}>Jantan</option>
                    <option value="Betina" {{ strtolower(old($prefix . 'gender', $patient?->gender)) === 'betina' ? 'selected' : '' }}>Betina</option>
                    <option value="Tidak Diketahui" {{ strtolower(old($prefix . 'gender', $patient?->gender)) === 'tidak diketahui' ? 'selected' : '' }}>Tidak Diketahui</option>
                </select>
            </div>

            <!-- Steril -->
            <div>
                <label class="block mb-1 text-xs font-bold text-gray-500 uppercase">Status Steril *</label>
                <select name="{{ $prefix }}is_sterile" class="block w-full px-3 py-2.5 text-gray-900 bg-white border border-gray-300 rounded-lg focus:ring-birawa-500 focus:border-birawa-500 sm:text-sm" required>
                    <option value="">Tidak Diketahui</option>
                    <option value="0" {{ (string)old($prefix . 'is_sterile', $patient?->is_sterile) === '0' ? 'selected' : '' }}>Belum Steril</option>
                    <option value="1" {{ (string)old($prefix . 'is_sterile', $patient?->is_sterile) === '1' ? 'selected' : '' }}>Sudah Steril</option>
                </select>
            </div>
        </div>
    </div>
</div>


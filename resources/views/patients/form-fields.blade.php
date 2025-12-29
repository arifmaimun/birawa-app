@props(['clients', 'patient' => null])

<!-- Section: Client Selection (Searchable) -->
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

<!-- Section: Patient Details -->
<div class="p-5 mb-6 bg-white border border-gray-100 shadow-sm rounded-2xl">
    <div class="flex items-center gap-3 mb-4">
        <div class="flex items-center justify-center w-8 h-8 text-blue-600 bg-blue-100 rounded-full">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <h2 class="text-sm font-bold text-gray-800">Informasi Pasien</h2>
    </div>

    <div class="grid grid-cols-1 gap-4">
        <!-- Name -->
        <div>
            <label for="name" class="block mb-1 text-sm font-medium text-gray-700">Nama Hewan</label>
            <input type="text" name="name" id="name" value="{{ old('name', $patient?->name) }}" class="block w-full px-4 py-3 text-gray-700 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-birawa-500 focus:border-birawa-500" required>
        </div>

        <!-- Species (Smart Select) -->
        <div x-data="{
            inputType: 'select',
            selectedSpecies: '{{ old('species', $patient?->species) }}',
            customSpecies: '{{ old('species', $patient?->species) }}',
            options: ['Kucing', 'Anjing', 'Kelinci', 'Burung', 'Hamster', 'Reptil', 'Ikan'],
            
            init() {
                // If the initial species is not in options, set to text mode
                if (this.selectedSpecies && !this.options.includes(this.selectedSpecies)) {
                    this.inputType = 'text';
                    this.customSpecies = this.selectedSpecies;
                    this.selectedSpecies = 'other';
                }

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
            <label class="block mb-1 text-sm font-medium text-gray-700">Jenis Hewan (Spesies)</label>
            
            <!-- Hidden Input sent to backend -->
            <input type="hidden" name="species" :value="inputType === 'text' ? customSpecies : selectedSpecies" required>

            <!-- Select Input -->
            <div x-show="inputType === 'select'">
                <select x-model="selectedSpecies" class="block w-full px-4 py-3 text-gray-700 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-birawa-500 focus:border-birawa-500">
                    <option value="">-- Pilih Spesies --</option>
                    <template x-for="option in options">
                        <option :value="option" x-text="option" :selected="option === selectedSpecies"></option>
                    </template>
                    <option value="other" class="font-bold text-blue-600 bg-blue-50">+ Tambah Baru / Lainnya...</option>
                </select>
            </div>

            <!-- Text Input (Custom) -->
            <div x-show="inputType === 'text'" class="flex gap-2" style="display: none;">
                <input 
                    x-ref="customInput"
                    type="text" 
                    x-model="customSpecies"
                    class="block w-full px-4 py-3 text-gray-700 bg-white border border-blue-500 rounded-xl focus:ring-blue-500 focus:border-blue-500 shadow-sm" 
                    placeholder="Ketik jenis hewan baru..."
                >
                <button type="button" @click="resetToSelect()" class="px-4 py-2 text-gray-500 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors" title="Kembali ke pilihan">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <p x-show="inputType === 'text'" class="mt-1 text-xs text-blue-600">Data baru ini akan tersimpan untuk pasien ini.</p>
        </div>

        <!-- Breed -->
        <div>
            <label for="breed" class="block mb-1 text-sm font-medium text-gray-700">Ras (Breed)</label>
            <input type="text" name="breed" id="breed" value="{{ old('breed', $patient?->breed) }}" class="block w-full px-4 py-3 text-gray-700 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-birawa-500 focus:border-birawa-500">
        </div>

        <!-- Row: DOB & Gender -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- DOB -->
            <div x-data="{
                dateValue: '{{ old('dob', $patient?->dob) }}',
                displayValue: '',
                error: '',
                
                init() {
                    if(this.dateValue) {
                        this.formatDisplay();
                    }
                    this.$watch('dateValue', () => this.formatDisplay());
                },
                
                formatDisplay() {
                    if(!this.dateValue) return;
                    const date = new Date(this.dateValue);
                    if(isNaN(date.getTime())) return;
                    
                    const d = String(date.getDate()).padStart(2, '0');
                    const m = String(date.getMonth() + 1).padStart(2, '0');
                    const y = date.getFullYear();
                    this.displayValue = `${d}-${m}-${y}`;
                    this.error = '';
                },
                
                handleInput(e) {
                    let v = e.target.value.replace(/\D/g, '').slice(0, 8);
                    if (v.length >= 5) {
                        v = `${v.slice(0,2)}-${v.slice(2,4)}-${v.slice(4)}`;
                    } else if (v.length >= 3) {
                        v = `${v.slice(0,2)}-${v.slice(2)}`;
                    }
                    this.displayValue = v;
                    
                    if (v.length === 10) {
                        this.validateAndSet(v);
                    }
                },
                
                validateAndSet(v) {
                    const parts = v.split('-');
                    const d = parseInt(parts[0]);
                    const m = parseInt(parts[1]);
                    const y = parseInt(parts[2]);
                    
                    const date = new Date(y, m - 1, d);
                    const today = new Date();
                    today.setHours(0,0,0,0);
                    
                    if (date.getFullYear() === y && date.getMonth() === m - 1 && date.getDate() === d) {
                        if (date > today) {
                            this.error = 'Tanggal lahir tidak boleh melebihi hari ini';
                            this.dateValue = '';
                        } else {
                            this.error = '';
                            this.dateValue = `${y}-${String(m).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
                        }
                    } else {
                        this.error = 'Format tanggal tidak valid';
                        this.dateValue = '';
                    }
                }
            }">
                <label for="dob_display" class="block mb-1 text-sm font-medium text-gray-700">Tanggal Lahir</label>
                <div class="relative">
                    <input 
                        type="text" 
                        id="dob_display" 
                        x-model="displayValue"
                        @input="handleInput"
                        placeholder="DD-MM-YYYY"
                        class="block w-full px-4 py-3 text-gray-700 bg-white border border-gray-200 rounded-xl focus:bg-white focus:ring-birawa-500 focus:border-birawa-500 placeholder-gray-400"
                        maxlength="10"
                    >
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                        <input 
                            type="date" 
                            x-model="dateValue"
                            max="{{ date('Y-m-d') }}"
                            class="absolute inset-0 opacity-0 w-full h-full cursor-pointer"
                            tabindex="-1"
                        >
                        <svg class="w-5 h-5 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                </div>
                <input type="hidden" name="dob" :value="dateValue">
                <p x-show="error" x-text="error" class="mt-1 text-sm text-red-600"></p>
                <p x-show="!error" class="mt-1 text-xs text-gray-500">Format: DD-MM-YYYY</p>
            </div>

            <!-- Gender (Full Width) -->
            <div>
                <label class="block mb-2 text-sm font-medium text-gray-700">Jenis Kelamin</label>
                <div class="flex gap-3">
                    <label class="flex-1 cursor-pointer group">
                        <input type="radio" name="gender" value="jantan" class="peer sr-only" {{ old('gender', $patient?->gender) === 'jantan' ? 'checked' : '' }} required>
                        <div class="flex items-center justify-center py-3 px-4 rounded-xl border-2 border-gray-100 bg-white text-gray-500 peer-checked:border-blue-500 peer-checked:bg-blue-50 peer-checked:text-blue-700 transition-all group-hover:border-blue-200">
                            <span class="text-lg mr-2">♂</span> 
                            <span class="font-medium">Jantan</span>
                        </div>
                    </label>
                    <label class="flex-1 cursor-pointer group">
                        <input type="radio" name="gender" value="betina" class="peer sr-only" {{ old('gender', $patient?->gender) === 'betina' ? 'checked' : '' }} required>
                        <div class="flex items-center justify-center py-3 px-4 rounded-xl border-2 border-gray-100 bg-white text-gray-500 peer-checked:border-pink-500 peer-checked:bg-pink-50 peer-checked:text-pink-700 transition-all group-hover:border-pink-200">
                            <span class="text-lg mr-2">♀</span>
                            <span class="font-medium">Betina</span>
                        </div>
                    </label>
                </div>
            </div>
            
            <!-- Sterile Status -->
            <div class="md:col-span-2">
                <label class="flex items-center space-x-3 p-3 border border-gray-200 rounded-xl bg-gray-50 cursor-pointer hover:bg-white transition-colors">
                    <input type="checkbox" name="is_sterile" value="1" class="w-5 h-5 text-birawa-600 rounded border-gray-300 focus:ring-birawa-500" {{ old('is_sterile', $patient?->is_sterile) ? 'checked' : '' }}>
                    <span class="text-gray-700 font-medium select-none">Sudah Disteril (Sterile/Neutered)</span>
                </label>
            </div>
        </div>
    </div>
</div>

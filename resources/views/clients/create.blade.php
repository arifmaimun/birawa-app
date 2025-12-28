<x-app-layout>
    <div class="max-w-3xl px-4 py-6 mx-auto pb-28" x-data="clientForm()">
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
                    <!-- Mode Client: Personal vs Business -->
                    <div class="mb-6">
                        <label class="block mb-2 text-xs font-bold text-gray-500 uppercase">Tipe Klien</label>
                        <div class="flex p-1 bg-gray-100 rounded-lg w-fit">
                            <button type="button" @click="isBusiness = false" 
                                :class="{'bg-white text-blue-600 shadow-sm': !isBusiness, 'text-gray-500 hover:text-gray-700': isBusiness}"
                                class="px-4 py-2 text-sm font-medium rounded-md transition-all">
                                Personal
                            </button>
                            <button type="button" @click="isBusiness = true"
                                :class="{'bg-white text-blue-600 shadow-sm': isBusiness, 'text-gray-500 hover:text-gray-700': !isBusiness}"
                                class="px-4 py-2 text-sm font-medium rounded-md transition-all">
                                Bisnis / Organisasi
                            </button>
                        </div>
                        <input type="hidden" name="is_business" :value="isBusiness ? 1 : 0">
                    </div>

                    <!-- Grid Layout -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        <!-- Business Fields -->
                        <template x-if="isBusiness">
                            <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="md:col-span-2">
                                    <label for="business_name" class="block mb-1 text-xs font-bold text-gray-500 uppercase">Nama Bisnis *</label>
                                    <input type="text" name="business_name" id="business_name" 
                                        class="block w-full px-3 py-2.5 text-gray-900 bg-white border border-gray-300 rounded-lg focus:ring-birawa-500 focus:border-birawa-500 sm:text-sm" 
                                        placeholder="PT. Birawa Sejahtera" :required="isBusiness">
                                </div>
                                <div class="md:col-span-2">
                                    <label for="contact_person" class="block mb-1 text-xs font-bold text-gray-500 uppercase">Penanggung Jawab (Contact Person) *</label>
                                    <input type="text" name="contact_person" id="contact_person" 
                                        class="block w-full px-3 py-2.5 text-gray-900 bg-white border border-gray-300 rounded-lg focus:ring-birawa-500 focus:border-birawa-500 sm:text-sm" 
                                        placeholder="Nama Penanggung Jawab" :required="isBusiness">
                                </div>
                            </div>
                        </template>

                        <!-- Personal Name Fields -->
                        <div>
                            <label for="first_name" class="block mb-1 text-xs font-bold text-gray-500 uppercase">Nama Depan</label>
                            <input type="text" name="first_name" id="first_name" 
                                class="block w-full px-3 py-2.5 text-gray-900 bg-white border border-gray-300 rounded-lg focus:ring-birawa-500 focus:border-birawa-500 sm:text-sm" 
                                placeholder="Opsional">
                        </div>
                        <div>
                            <label for="last_name" class="block mb-1 text-xs font-bold text-gray-500 uppercase">Nama Belakang *</label>
                            <input type="text" name="last_name" id="last_name" 
                                class="block w-full px-3 py-2.5 text-gray-900 bg-white border border-gray-300 rounded-lg focus:ring-birawa-500 focus:border-birawa-500 sm:text-sm" 
                                placeholder="Wajib diisi" required>
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

                        <!-- Latar Belakang Client -->
                        <div class="md:col-span-2 pt-4 border-t border-gray-100">
                            <h3 class="text-sm font-bold text-gray-800 mb-4">Latar Belakang & Identitas</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                
                                <!-- ID Type (Dropdown + New) -->
                                <div x-data="dropdownInput('{{ $idTypes->join(',') }}')">
                                    <label class="block mb-1 text-xs font-bold text-gray-500 uppercase">Jenis ID</label>
                                    <input type="hidden" name="id_type" :value="finalValue">
                                    <div x-show="!isCustom">
                                        <select x-model="selectedValue" class="block w-full px-3 py-2.5 text-gray-900 bg-white border border-gray-300 rounded-lg focus:ring-birawa-500 focus:border-birawa-500 sm:text-sm">
                                            <option value="">-- Pilih Jenis ID --</option>
                                            <template x-for="opt in options">
                                                <option :value="opt" x-text="opt"></option>
                                            </template>
                                            <option value="custom">+ Tambah Baru</option>
                                        </select>
                                    </div>
                                    <div x-show="isCustom" class="flex gap-2">
                                        <input type="text" x-model="customValue" class="block w-full px-3 py-2.5 text-gray-900 bg-white border border-blue-500 rounded-lg focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Masukkan Jenis ID Baru">
                                        <button type="button" @click="reset()" class="px-3 py-2 text-gray-500 bg-gray-100 rounded-lg hover:bg-gray-200">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </button>
                                    </div>
                                </div>

                                <!-- ID Number -->
                                <div>
                                    <label for="id_number" class="block mb-1 text-xs font-bold text-gray-500 uppercase">Nomor ID</label>
                                    <input type="text" name="id_number" id="id_number" class="block w-full px-3 py-2.5 text-gray-900 bg-white border border-gray-300 rounded-lg focus:ring-birawa-500 focus:border-birawa-500 sm:text-sm" placeholder="NIK / No. Passport">
                                </div>

                                <!-- Gender -->
                                <div>
                                    <label for="gender" class="block mb-1 text-xs font-bold text-gray-500 uppercase">Jenis Kelamin</label>
                                    <select name="gender" id="gender" class="block w-full px-3 py-2.5 text-gray-900 bg-white border border-gray-300 rounded-lg focus:ring-birawa-500 focus:border-birawa-500 sm:text-sm">
                                        <option value="">-- Pilih --</option>
                                        <option value="Laki-laki">Laki-laki</option>
                                        <option value="Perempuan">Perempuan</option>
                                    </select>
                                </div>

                                <!-- Occupation -->
                                <div>
                                    <label for="occupation" class="block mb-1 text-xs font-bold text-gray-500 uppercase">Pekerjaan</label>
                                    <input type="text" name="occupation" id="occupation" class="block w-full px-3 py-2.5 text-gray-900 bg-white border border-gray-300 rounded-lg focus:ring-birawa-500 focus:border-birawa-500 sm:text-sm">
                                </div>

                                <!-- DOB / Age -->
                                <div class="md:col-span-2">
                                    <label class="block mb-1 text-xs font-bold text-gray-500 uppercase">Tanggal Lahir / Umur</label>
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
                                        <input type="date" name="dob" x-model="dobDate" class="block w-full px-3 py-2.5 text-gray-900 bg-white border border-gray-300 rounded-lg focus:ring-birawa-500 focus:border-birawa-500 sm:text-sm">
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
                                    <!-- Hidden Input for Actual DOB Value to be submitted if calculated from age -->
                                    <!-- If mode is date, name="dob" input above submits. If mode is age, we need to ensure the correct value is sent. 
                                         Actually, if I give the date input `name="dob"`, and update its value via x-model, it will be submitted correctly. -->
                                </div>

                                <!-- Ethnicity (Dropdown + New) -->
                                <div x-data="dropdownInput('{{ $ethnicities->join(',') }}')">
                                    <label class="block mb-1 text-xs font-bold text-gray-500 uppercase">Suku / Etnis</label>
                                    <input type="hidden" name="ethnicity" :value="finalValue">
                                    <div x-show="!isCustom">
                                        <select x-model="selectedValue" class="block w-full px-3 py-2.5 text-gray-900 bg-white border border-gray-300 rounded-lg focus:ring-birawa-500 focus:border-birawa-500 sm:text-sm">
                                            <option value="">-- Pilih Suku --</option>
                                            <template x-for="opt in options">
                                                <option :value="opt" x-text="opt"></option>
                                            </template>
                                            <option value="custom">+ Tambah Baru</option>
                                        </select>
                                    </div>
                                    <div x-show="isCustom" class="flex gap-2">
                                        <input type="text" x-model="customValue" class="block w-full px-3 py-2.5 text-gray-900 bg-white border border-blue-500 rounded-lg focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Masukkan Suku Baru">
                                        <button type="button" @click="reset()" class="px-3 py-2 text-gray-500 bg-gray-100 rounded-lg hover:bg-gray-200">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </button>
                                    </div>
                                </div>

                                <!-- Religion -->
                                <div>
                                    <label for="religion" class="block mb-1 text-xs font-bold text-gray-500 uppercase">Agama</label>
                                    <select name="religion" id="religion" class="block w-full px-3 py-2.5 text-gray-900 bg-white border border-gray-300 rounded-lg focus:ring-birawa-500 focus:border-birawa-500 sm:text-sm">
                                        <option value="">-- Pilih --</option>
                                        <option value="Islam">Islam</option>
                                        <option value="Kristen">Kristen</option>
                                        <option value="Katolik">Katolik</option>
                                        <option value="Hindu">Hindu</option>
                                        <option value="Buddha">Buddha</option>
                                        <option value="Konghucu">Konghucu</option>
                                        <option value="Lainnya">Lainnya</option>
                                    </select>
                                </div>

                                <!-- Marital Status -->
                                <div>
                                    <label for="marital_status" class="block mb-1 text-xs font-bold text-gray-500 uppercase">Status Perkawinan</label>
                                    <select name="marital_status" id="marital_status" class="block w-full px-3 py-2.5 text-gray-900 bg-white border border-gray-300 rounded-lg focus:ring-birawa-500 focus:border-birawa-500 sm:text-sm">
                                        <option value="">-- Pilih --</option>
                                        <option value="Belum Menikah">Belum Menikah</option>
                                        <option value="Menikah">Menikah</option>
                                        <option value="Cerai">Cerai</option>
                                    </select>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Section 2: Alamat Klien (Multiple) -->
            <div class="mb-6 overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
                <!-- Header Section -->
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                    <h2 class="text-sm font-bold tracking-wide text-gray-800 uppercase">2. Daftar Alamat</h2>
                    <button type="button" @click="addAddress" class="text-xs font-semibold text-birawa-600 bg-birawa-50 px-2 py-1 rounded hover:bg-birawa-100 transition-colors">
                        + Tambah Alamat
                    </button>
                </div>

                <div class="p-6 space-y-8">
                    <div class="text-sm text-blue-600 bg-blue-50 p-3 rounded-lg mb-4 flex items-start gap-2">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>Mohon isi alamat selengkap mungkin untuk memudahkan pencarian di Google Maps. Alamat yang lengkap akan ditampilkan sebagai link aktif pada form booking.</span>
                    </div>

                    <template x-for="(address, index) in addresses" :key="index">
                        <div class="relative p-4 border border-gray-200 rounded-xl bg-gray-50/30">
                            <!-- Remove Button -->
                            <button type="button" @click="removeAddress(index)" x-show="addresses.length > 1" class="absolute top-2 right-2 text-red-500 hover:text-red-700">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>

                            <h4 class="text-xs font-bold text-gray-500 uppercase mb-4" x-text="'Alamat #' + (index + 1)"></h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Street -->
                                <div class="md:col-span-2">
                                    <label class="block mb-1 text-xs font-bold text-gray-500 uppercase">Alamat Jalan (Lengkap)</label>
                                    <textarea :name="'addresses['+index+'][street]'" rows="2" 
                                        class="block w-full px-3 py-2.5 text-gray-900 bg-white border border-gray-300 rounded-lg focus:ring-birawa-500 focus:border-birawa-500 sm:text-sm" 
                                        placeholder="Nama Jalan, No. Rumah, RT/RW, Patokan" required></textarea>
                                </div>
                                
                                <!-- Additional Info -->
                                <div class="md:col-span-2">
                                    <label class="block mb-1 text-xs font-bold text-gray-500 uppercase">Info Tambahan (Opsional)</label>
                                    <input type="text" :name="'addresses['+index+'][additional_info]'" 
                                        class="block w-full px-3 py-2.5 text-gray-900 bg-white border border-gray-300 rounded-lg focus:ring-birawa-500 focus:border-birawa-500 sm:text-sm" 
                                        placeholder="Lantai, Unit, Kode Pagar, dll">
                                </div>

                                <!-- City & Province -->
                                <div>
                                    <label class="block mb-1 text-xs font-bold text-gray-500 uppercase">Kota / Kabupaten</label>
                                    <input type="text" :name="'addresses['+index+'][city]'" 
                                        class="block w-full px-3 py-2.5 text-gray-900 bg-white border border-gray-300 rounded-lg focus:ring-birawa-500 focus:border-birawa-500 sm:text-sm">
                                </div>
                                <div>
                                    <label class="block mb-1 text-xs font-bold text-gray-500 uppercase">Provinsi</label>
                                    <input type="text" :name="'addresses['+index+'][province]'" 
                                        class="block w-full px-3 py-2.5 text-gray-900 bg-white border border-gray-300 rounded-lg focus:ring-birawa-500 focus:border-birawa-500 sm:text-sm">
                                </div>

                                <!-- Postal Code & Country -->
                                <div>
                                    <label class="block mb-1 text-xs font-bold text-gray-500 uppercase">Kode Pos</label>
                                    <input type="text" :name="'addresses['+index+'][postal_code]'" 
                                        class="block w-full px-3 py-2.5 text-gray-900 bg-white border border-gray-300 rounded-lg focus:ring-birawa-500 focus:border-birawa-500 sm:text-sm">
                                </div>
                                <div>
                                    <label class="block mb-1 text-xs font-bold text-gray-500 uppercase">Negara</label>
                                    <input type="text" :name="'addresses['+index+'][country]'" value="Indonesia"
                                        class="block w-full px-3 py-2.5 text-gray-900 bg-white border border-gray-300 rounded-lg focus:ring-birawa-500 focus:border-birawa-500 sm:text-sm">
                                </div>

                                <!-- Parking Type (Dropdown + New) -->
                                <div x-data="dropdownInput('{{ $parkingTypes->join(',') }}')">
                                    <label class="block mb-1 text-xs font-bold text-gray-500 uppercase">Jenis Parkir</label>
                                    <input type="hidden" :name="'addresses['+index+'][parking_type]'" :value="finalValue">
                                    <div x-show="!isCustom">
                                        <select x-model="selectedValue" class="block w-full px-3 py-2.5 text-gray-900 bg-white border border-gray-300 rounded-lg focus:ring-birawa-500 focus:border-birawa-500 sm:text-sm">
                                            <option value="">-- Pilih --</option>
                                            <template x-for="opt in options">
                                                <option :value="opt" x-text="opt"></option>
                                            </template>
                                            <option value="custom">+ Tambah Baru</option>
                                        </select>
                                    </div>
                                    <div x-show="isCustom" class="flex gap-2">
                                        <input type="text" x-model="customValue" class="block w-full px-3 py-2.5 text-gray-900 bg-white border border-blue-500 rounded-lg focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Jenis Parkir Baru">
                                        <button type="button" @click="reset()" class="px-3 py-2 text-gray-500 bg-gray-100 rounded-lg hover:bg-gray-200">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </button>
                                    </div>
                                </div>

                                <!-- Address Type (Dropdown + New) -->
                                <div x-data="dropdownInput('{{ $addressTypes->join(',') }}')">
                                    <label class="block mb-1 text-xs font-bold text-gray-500 uppercase">Jenis Alamat</label>
                                    <input type="hidden" :name="'addresses['+index+'][address_type]'" :value="finalValue">
                                    <div x-show="!isCustom">
                                        <select x-model="selectedValue" class="block w-full px-3 py-2.5 text-gray-900 bg-white border border-gray-300 rounded-lg focus:ring-birawa-500 focus:border-birawa-500 sm:text-sm">
                                            <option value="">-- Pilih --</option>
                                            <template x-for="opt in options">
                                                <option :value="opt" x-text="opt"></option>
                                            </template>
                                            <option value="custom">+ Tambah Baru</option>
                                        </select>
                                    </div>
                                    <div x-show="isCustom" class="flex gap-2">
                                        <input type="text" x-model="customValue" class="block w-full px-3 py-2.5 text-gray-900 bg-white border border-blue-500 rounded-lg focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Jenis Alamat Baru">
                                        <button type="button" @click="reset()" class="px-3 py-2 text-gray-500 bg-gray-100 rounded-lg hover:bg-gray-200">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
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

                        <!-- DOB / Age -->
                        <div class="md:col-span-2">
                            <label class="block mb-1 text-xs font-bold text-gray-500 uppercase">Tanggal Lahir / Umur Pasien</label>
                            <div class="flex gap-4 mb-2">
                                <label class="inline-flex items-center">
                                    <input type="radio" x-model="patientDobMode" value="date" class="text-birawa-600 focus:ring-birawa-500">
                                    <span class="ml-2 text-sm text-gray-700">Input Tanggal</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" x-model="patientDobMode" value="age" class="text-birawa-600 focus:ring-birawa-500">
                                    <span class="ml-2 text-sm text-gray-700">Input Umur</span>
                                </label>
                            </div>
                            
                            <div x-show="patientDobMode === 'date'">
                                <input type="date" name="patient_dob" x-model="patientDobDate" class="block w-full px-3 py-2.5 text-gray-900 bg-white border border-gray-300 rounded-lg focus:ring-birawa-500 focus:border-birawa-500 sm:text-sm">
                            </div>
                            
                            <div x-show="patientDobMode === 'age'" class="flex gap-4 items-center">
                                <div class="w-1/2">
                                    <div class="flex items-center">
                                        <input type="number" x-model="patientAgeYears" @input="calculatePatientDobFromAge" min="0" class="block w-full px-3 py-2.5 text-gray-900 bg-white border border-gray-300 rounded-l-lg focus:ring-birawa-500 focus:border-birawa-500 sm:text-sm">
                                        <span class="inline-flex items-center px-3 py-2.5 rounded-r-lg border border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">Tahun</span>
                                    </div>
                                </div>
                                <div class="w-1/2">
                                    <div class="flex items-center">
                                        <input type="number" x-model="patientAgeMonths" @input="calculatePatientDobFromAge" min="0" max="11" class="block w-full px-3 py-2.5 text-gray-900 bg-white border border-gray-300 rounded-l-lg focus:ring-birawa-500 focus:border-birawa-500 sm:text-sm">
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
                                <select name="patient_gender" class="block w-full px-3 py-2.5 text-gray-900 bg-white border border-gray-300 rounded-lg focus:ring-birawa-500 focus:border-birawa-500 sm:text-sm" required>
                                    <option value="">-- Pilih --</option>
                                    <option value="Jantan">Jantan</option>
                                    <option value="Betina">Betina</option>
                                    <option value="Tidak Diketahui">Tidak Diketahui</option>
                                </select>
                            </div>

                            <!-- Steril -->
                            <div>
                                <label class="block mb-1 text-xs font-bold text-gray-500 uppercase">Status Steril *</label>
                                <select name="is_sterile" class="block w-full px-3 py-2.5 text-gray-900 bg-white border border-gray-300 rounded-lg focus:ring-birawa-500 focus:border-birawa-500 sm:text-sm" required>
                                    <option value="">Tidak Diketahui</option>
                                    <option value="0">Belum Steril</option>
                                    <option value="1">Sudah Steril</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="fixed bottom-0 left-0 right-0 p-4 bg-white border-t border-gray-200 z-20">
                <div class="max-w-3xl mx-auto">
                    <button type="submit" class="w-full px-4 py-3 text-center font-bold text-white bg-birawa-600 rounded-xl shadow-lg shadow-birawa-500/30 hover:bg-birawa-700 transform active:scale-[0.98] transition-all">
                        Selesai
                    </button>
                </div>
            </div>

        </form>
    </div>

    <!-- Alpine Data Logic -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('clientForm', () => ({
                isBusiness: false,
                dobMode: 'date',
                dobDate: '',
                ageYears: 0,
                ageMonths: 0,
                addresses: [
                    { street: '', additional_info: '', city: '', province: '', postal_code: '', country: 'Indonesia', parking_type: '', address_type: '' }
                ],

                addAddress() {
                    this.addresses.push({ street: '', additional_info: '', city: '', province: '', postal_code: '', country: 'Indonesia', parking_type: '', address_type: '' });
                },

                removeAddress(index) {
                    this.addresses.splice(index, 1);
                },

                calculateDobFromAge() {
                    // Calculate date from age years and months
                    const today = new Date();
                    let birthDate = new Date(today);
                    birthDate.setFullYear(today.getFullYear() - (parseInt(this.ageYears) || 0));
                    birthDate.setMonth(today.getMonth() - (parseInt(this.ageMonths) || 0));
                    
                    // Format to YYYY-MM-DD
                    const yyyy = birthDate.getFullYear();
                    const mm = String(birthDate.getMonth() + 1).padStart(2, '0');
                    const dd = String(birthDate.getDate()).padStart(2, '0');
                    
                    this.dobDate = `${yyyy}-${mm}-${dd}`;
                },

                calculatePatientDobFromAge() {
                    const today = new Date();
                    let birthDate = new Date(today);
                    birthDate.setFullYear(today.getFullYear() - (parseInt(this.patientAgeYears) || 0));
                    birthDate.setMonth(today.getMonth() - (parseInt(this.patientAgeMonths) || 0));
                    
                    const yyyy = birthDate.getFullYear();
                    const mm = String(birthDate.getMonth() + 1).padStart(2, '0');
                    const dd = String(birthDate.getDate()).padStart(2, '0');
                    
                    this.patientDobDate = `${yyyy}-${mm}-${dd}`;
                }
            }));

            Alpine.data('dropdownInput', (optionsStr) => ({
                options: optionsStr ? optionsStr.split(',').filter(Boolean) : [],
                selectedValue: '',
                customValue: '',
                isCustom: false,

                init() {
                    this.$watch('selectedValue', (value) => {
                        if (value === 'custom') {
                            this.isCustom = true;
                            this.customValue = '';
                        } else {
                            this.isCustom = false;
                        }
                    });
                },

                get finalValue() {
                    return this.isCustom ? this.customValue : this.selectedValue;
                },

                reset() {
                    this.isCustom = false;
                    this.selectedValue = '';
                    this.customValue = '';
                }
            }));
        });
    </script>
</x-app-layout>
<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-slate-800">Create Medical Record</h1>
            <p class="text-sm text-slate-500">Document the visit and treatment plan</p>
        </div>
            
        <!-- Medical History Section -->
        @if($medicalHistory->count() > 0)
        <div class="mb-8 bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-6">
                <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-birawa-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Medical History (Last 5 Visits)
                </h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Date</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Doctor</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Diagnosis/Assessment</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Treatment</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-50">
                            @foreach($medicalHistory as $history)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-slate-700">
                                        {{ $history->created_at->format('d M Y') }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-slate-600">
                                        {{ $history->doctor->name ?? 'Unknown' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-slate-600">
                                        @if($history->diagnoses->count() > 0)
                                            <div class="flex flex-wrap gap-1">
                                                @foreach($history->diagnoses as $diag)
                                                    <span class="px-2 py-0.5 rounded-md text-xs font-bold bg-birawa-50 text-birawa-700 border border-birawa-100">
                                                        {{ $diag->name }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @else
                                            {{ Str::limit($history->assessment, 50) }}
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm text-slate-600">
                                        {{ Str::limit($history->plan_treatment, 50) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-6 md:p-8">
                <div class="mb-8 pb-6 border-b border-slate-100">
                    <h3 class="text-xl font-bold text-slate-800">Patient: {{ $visit->patient->name }}</h3>
                    <div class="mt-2 flex flex-col sm:flex-row gap-4 text-sm text-slate-500">
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                            Owner: {{ $visit->patient->client->name ?? 'N/A' }}
                        </span>
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" /></svg>
                            Complaint: {{ $visit->complaint }}
                        </span>
                    </div>
                </div>

                <form action="{{ route('medical-records.store', $visit) }}" method="POST">
                    @csrf
                    
                    <!-- Vital Signs Section -->
                    <div class="mb-8 p-6 bg-slate-50 rounded-2xl border border-slate-100">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                                <svg class="w-5 h-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>
                                Vital Signs
                            </h3>
                            <a href="{{ route('vital-sign-settings.index') }}" target="_blank" class="text-xs font-bold text-birawa-600 hover:text-birawa-700 flex items-center gap-1 bg-white px-3 py-1.5 rounded-lg border border-birawa-100 shadow-sm hover:shadow transition-all">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                Configure Fields
                            </a>
                        </div>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <!-- Default Fields -->
                            <div>
                                <label for="temperature" class="block text-sm font-bold text-slate-700 mb-1">Temperature (°C)</label>
                                <input type="number" step="0.1" name="temperature" id="temperature" 
                                    class="w-full rounded-xl border-slate-200 focus:border-birawa-500 focus:ring-birawa-500 text-sm shadow-sm transition-colors"
                                    placeholder="e.g. 38.5" value="{{ old('temperature') }}">
                            </div>
                            <div>
                                <label for="weight" class="block text-sm font-bold text-slate-700 mb-1">Weight (kg)</label>
                                <input type="number" step="0.001" name="weight" id="weight" 
                                    class="w-full rounded-xl border-slate-200 focus:border-birawa-500 focus:ring-birawa-500 text-sm shadow-sm transition-colors"
                                    placeholder="e.g. 5.2" value="{{ old('weight') }}">
                            </div>
                            <div>
                                <label for="heart_rate" class="block text-sm font-bold text-slate-700 mb-1">Heart Rate (bpm)</label>
                                <input type="number" name="heart_rate" id="heart_rate" 
                                    class="w-full rounded-xl border-slate-200 focus:border-birawa-500 focus:ring-birawa-500 text-sm shadow-sm transition-colors"
                                    placeholder="e.g. 120" value="{{ old('heart_rate') }}">
                            </div>
                            
                            <!-- Custom Fields -->
                            @if(isset($vitalSignSettings))
                                @foreach($vitalSignSettings as $setting)
                                <div>
                                    <label for="custom_{{ $setting->id }}" class="block text-sm font-bold text-slate-700 mb-1">
                                        {{ $setting->name }} 
                                        @if($setting->unit) <span class="text-xs text-slate-500 font-normal">({{ $setting->unit }})</span> @endif
                                    </label>
                                    <input type="{{ $setting->type == 'number' ? 'number' : 'text' }}" 
                                        @if($setting->type == 'number') step="any" @endif
                                        name="custom_vital_signs[{{ $setting->name }}]" 
                                        id="custom_{{ $setting->id }}" 
                                        class="w-full rounded-xl border-slate-200 focus:border-birawa-500 focus:ring-birawa-500 text-sm shadow-sm transition-colors"
                                        placeholder="{{ $setting->type == 'number' ? '0' : 'Value...' }}"
                                        value="{{ old('custom_vital_signs')[$setting->name] ?? '' }}">
                                </div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <!-- SOAP Section -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div>
                            <label for="subjective" class="block text-sm font-bold text-slate-700 mb-2">Subjective (Keluhan Pemilik)</label>
                            <textarea name="subjective" id="subjective" rows="4" 
                                class="w-full rounded-xl border-slate-200 focus:border-birawa-500 focus:ring-birawa-500 placeholder-slate-400 text-sm shadow-sm transition-colors" 
                                required>{{ old('subjective', $visit->complaint) }}</textarea>
                        </div>
                        <div>
                            <label for="objective" class="block text-sm font-bold text-slate-700 mb-2">Objective (Temuan Klinis)</label>
                            <textarea name="objective" id="objective" rows="4" 
                                class="w-full rounded-xl border-slate-200 focus:border-birawa-500 focus:ring-birawa-500 placeholder-slate-400 text-sm shadow-sm transition-colors" 
                                required>{{ old('objective') }}</textarea>
                        </div>
                        
                        <div class="col-span-2" x-data="diagnosisManager()">
                            <div class="flex justify-between items-center mb-2">
                                <label for="diagnoses" class="block text-sm font-bold text-slate-700">Smart Diagnosis (Pilih satu atau lebih)</label>
                                <button type="button" @click="showModal = true" class="text-xs font-bold text-birawa-600 hover:text-birawa-700 flex items-center gap-1 bg-birawa-50 px-2 py-1 rounded-lg hover:bg-birawa-100 transition-colors">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                    Add New Diagnosis
                                </button>
                            </div>
                            
                            <select name="diagnoses[]" id="diagnoses" multiple 
                                class="w-full rounded-xl border-slate-200 focus:border-birawa-500 focus:ring-birawa-500 text-sm shadow-sm transition-colors h-48">
                                @foreach($diagnoses->groupBy('category') as $category => $items)
                                    <optgroup label="{{ $category ?? 'Other' }}" class="font-bold text-slate-800 bg-slate-50">
                                        @foreach($items as $diagnosis)
                                            <option value="{{ $diagnosis->id }}" {{ in_array($diagnosis->id, old('diagnoses', [])) ? 'selected' : '' }} class="py-1 px-2 text-slate-600 bg-white">
                                                {{ $diagnosis->code }} - {{ $diagnosis->name }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                            <p class="text-xs text-slate-400 mt-2 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                Hold Ctrl (Windows) or Cmd (Mac) to select multiple
                            </p>

                            <!-- Modal -->
                            <div x-cloak x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                    <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-slate-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="showModal = false"></div>

                                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                                    <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                            <h3 class="text-lg leading-6 font-medium text-slate-900 mb-4" id="modal-title">Add New Diagnosis</h3>
                                            <div class="space-y-4">
                                                <div>
                                                    <label class="block text-sm font-medium text-slate-700">Code</label>
                                                    <input type="text" x-model="form.code" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-birawa-500 focus:ring-birawa-500 sm:text-sm">
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-slate-700">Name</label>
                                                    <input type="text" x-model="form.name" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-birawa-500 focus:ring-birawa-500 sm:text-sm">
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-slate-700">Category</label>
                                                    <input type="text" x-model="form.category" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-birawa-500 focus:ring-birawa-500 sm:text-sm" placeholder="e.g. Dental, Skin, General">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="bg-slate-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                            <button type="button" @click="saveDiagnosis()" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-birawa-600 text-base font-medium text-white hover:bg-birawa-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-birawa-500 sm:ml-3 sm:w-auto sm:text-sm">
                                                Save
                                            </button>
                                            <button type="button" @click="showModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-slate-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-birawa-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                                Cancel
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <script>
                        function diagnosisManager() {
                            return {
                                showModal: false,
                                form: {
                                    code: '',
                                    name: '',
                                    category: ''
                                },
                                saveDiagnosis() {
                                    if(!this.form.code || !this.form.name || !this.form.category) {
                                        alert('Please fill all fields');
                                        return;
                                    }
                                    
                                    fetch('{{ route("diagnoses.store") }}', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                        },
                                        body: JSON.stringify(this.form)
                                    })
                                    .then(response => {
                                        if(!response.ok) return response.json().then(json => { throw new Error(json.message || 'Failed to save') });
                                        return response.json();
                                    })
                                    .then(data => {
                                        // Add to select
                                        const select = document.getElementById('diagnoses');
                                        const option = new Option(data.code + ' - ' + data.name, data.id, true, true);
                                        option.className = "py-1 px-2 text-slate-600 bg-white";
                                        
                                        // Find or create optgroup
                                        let optgroup = Array.from(select.getElementsByTagName('optgroup'))
                                            .find(g => g.label === data.category);
                                        
                                        if (!optgroup) {
                                            optgroup = document.createElement('optgroup');
                                            optgroup.label = data.category;
                                            optgroup.className = "font-bold text-slate-800 bg-slate-50";
                                            select.appendChild(optgroup);
                                        }
                                        
                                        optgroup.appendChild(option);
                                        
                                        this.showModal = false;
                                        this.form = { code: '', name: '', category: '' };
                                    })
                                    .catch(error => {
                                        alert('Error saving diagnosis: ' + error.message);
                                    });
                                }
                            }
                        }
                        </script>

                        <div class="col-span-2">
                            <label for="assessment" class="block text-sm font-bold text-slate-700 mb-2">Additional Assessment Notes (Optional)</label>
                            <textarea name="assessment" id="assessment" rows="2" 
                                class="w-full rounded-xl border-slate-200 focus:border-birawa-500 focus:ring-birawa-500 placeholder-slate-400 text-sm shadow-sm transition-colors" 
                                placeholder="Tambahkan catatan diagnosa manual jika tidak ada di list...">{{ old('assessment') }}</textarea>
                        </div>

                        <div class="col-span-2 md:col-span-1">
                            <label for="plan_treatment" class="block text-sm font-bold text-slate-700 mb-2">Plan (Treatment & Advice - Visible to Client)</label>
                            <textarea name="plan_treatment" id="plan_treatment" rows="4" 
                                class="w-full rounded-xl border-slate-200 focus:border-birawa-500 focus:ring-birawa-500 placeholder-slate-400 text-sm shadow-sm transition-colors" 
                                required>{{ old('plan_treatment') }}</textarea>
                        </div>
                        <div class="col-span-2 md:col-span-1">
                            <label for="plan_recipe" class="block text-sm font-bold text-amber-700 mb-2 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                                Plan Recipe (Internal Notes / Hidden)
                            </label>
                            <textarea name="plan_recipe" id="plan_recipe" rows="4" 
                                class="w-full rounded-xl border-amber-200 focus:border-amber-500 focus:ring-amber-500 placeholder-amber-400 text-sm shadow-sm transition-colors bg-amber-50" 
                                placeholder="Internal notes not visible to client...">{{ old('plan_recipe') }}</textarea>
                        </div>
                    </div>

                    <!-- Inventory Usage Section -->
                    <div class="mb-8 pt-6 border-t border-slate-100">
                        <h3 class="text-lg font-bold text-slate-800 mb-4">Medication & Consumables Used</h3>
                        <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100">
                            <div class="space-y-3" id="inventory-list">
                                @if($inventories->count() > 0)
                                    <div class="grid grid-cols-12 gap-4 items-center px-2 mb-2 text-xs font-bold text-slate-500 uppercase tracking-wider">
                                        <div class="col-span-8">Item Name (Stock)</div>
                                        <div class="col-span-4">Quantity Used</div>
                                    </div>
                                    @foreach($inventories as $index => $item)
                                        <div class="grid grid-cols-12 gap-4 items-center bg-white p-3 rounded-xl border border-slate-100 shadow-sm">
                                            <div class="col-span-8">
                                                <label class="flex items-center gap-3 cursor-pointer">
                                                    <div class="flex-1">
                                                        <div class="flex justify-between items-start">
                                                            <p class="font-bold text-slate-700 text-sm">{{ $item->item_name }}</p>
                                                            <span class="text-[10px] uppercase font-bold text-slate-400 bg-slate-50 px-1.5 py-0.5 rounded border border-slate-100">{{ $item->storageLocation->name ?? 'Unknown' }}</span>
                                                        </div>
                                                        <p class="text-xs text-slate-400">Stock: {{ $item->stock_qty }} {{ $item->base_unit ?? $item->unit }}</p>
                                                    </div>
                                                </label>
                                                <input type="hidden" name="inventory_items[{{ $index }}][id]" value="{{ $item->id }}">
                                            </div>
                                            <div class="col-span-4">
                                                <input type="number" name="inventory_items[{{ $index }}][qty]" min="0" max="{{ $item->stock_qty }}" step="0.01" value="0" 
                                                    class="w-full rounded-lg border-slate-200 focus:border-birawa-500 focus:ring-birawa-500 text-sm font-bold text-slate-700">
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="text-center py-4 text-slate-400">
                                        <p class="text-sm">No inventory items found. Please add items to your personal inventory first.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Services Usage Section -->
                    <div class="mb-8 pt-6 border-t border-slate-100">
                        <h3 class="text-lg font-bold text-slate-800 mb-4">Services Performed</h3>
                        <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100">
                            <div class="space-y-3" id="service-list">
                                @if(isset($services) && $services->count() > 0)
                                    <div class="grid grid-cols-12 gap-4 items-center px-2 mb-2 text-xs font-bold text-slate-500 uppercase tracking-wider">
                                        <div class="col-span-8">Service Name</div>
                                        <div class="col-span-4">Quantity</div>
                                    </div>
                                    @foreach($services as $index => $service)
                                        <div class="grid grid-cols-12 gap-4 items-center bg-white p-3 rounded-xl border border-slate-100 shadow-sm">
                                            <div class="col-span-8">
                                                <label class="flex items-center gap-3 cursor-pointer">
                                                    <div class="flex-1">
                                                        <div class="flex justify-between items-start">
                                                            <p class="font-bold text-slate-700 text-sm">{{ $service->service_name }}</p>
                                                        </div>
                                                        <p class="text-xs text-slate-400">Price: Rp {{ number_format($service->price, 0, ',', '.') }}</p>
                                                    </div>
                                                </label>
                                                <input type="hidden" name="service_items[{{ $index }}][id]" value="{{ $service->id }}">
                                            </div>
                                            <div class="col-span-4">
                                                <input type="number" name="service_items[{{ $index }}][qty]" min="0" step="1" value="0" 
                                                    class="w-full rounded-lg border-slate-200 focus:border-birawa-500 focus:ring-birawa-500 text-sm font-bold text-slate-700">
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="text-center py-4 text-slate-400">
                                        <p class="text-sm">No services found. Please add services to your catalog first.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-4 pt-4 border-t border-slate-100">
                        <a href="{{ route('visits.index') }}" class="px-6 py-2.5 text-sm font-bold text-slate-500 hover:text-slate-800 transition-colors">
                            Cancel
                        </a>
                        <button type="submit" class="px-6 py-2.5 bg-birawa-600 text-white text-sm font-bold rounded-xl shadow-lg hover:bg-birawa-700 focus:ring-4 focus:ring-birawa-100 transition-all active:scale-95 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            Save Medical Record & Complete Visit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

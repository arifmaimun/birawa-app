<x-app-layout>
    @push('styles')
        <link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@2.0.8/dist/trix.css">
        <style>
            .trix-button-group--file-tools { display: none !important; }
        </style>
    @endpush

    @push('scripts')
        <script type="text/javascript" src="https://unpkg.com/trix@2.0.8/dist/trix.umd.min.js"></script>
    @endpush

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-slate-800">Edit Service</h1>
            <p class="text-sm text-slate-500">Update service details</p>
        </div>

        <div class="bg-white rounded-3xl shadow-lg border border-slate-100 overflow-hidden" x-data="serviceForm()">
            <div class="p-8">
                <form action="{{ route('services.update', $service) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 gap-6 mb-8">
                        <!-- Service Name -->
                        <div>
                            <label for="service_name" class="block text-sm font-bold text-slate-700 mb-2">Service Name</label>
                            <input type="text" id="service_name" name="service_name" 
                                   class="w-full rounded-xl border-slate-200 focus:border-birawa-500 focus:ring-birawa-500 placeholder-slate-400 transition-colors shadow-sm"
                                   value="{{ old('service_name', $service->service_name) }}" required autofocus>
                            <x-input-error :messages="$errors->get('service_name')" class="mt-2" />
                        </div>

                        <!-- Price & Calculator -->
                        <div>
                            <label for="price" class="block text-sm font-bold text-slate-700 mb-2">Price</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-slate-500 font-bold sm:text-sm">Rp</span>
                                </div>
                                <input type="number" id="price" name="price" x-model="price"
                                       class="w-full pl-10 rounded-xl border-slate-200 focus:border-birawa-500 focus:ring-birawa-500 placeholder-slate-400 transition-colors shadow-sm"
                                       required min="0" step="1000">
                            </div>
                            <x-input-error :messages="$errors->get('price')" class="mt-2" />

                            <!-- Calculator Toggle -->
                            <div class="mt-2">
                                <button type="button" @click="showCalculator = !showCalculator" class="text-xs text-birawa-600 hover:text-birawa-700 font-medium flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                    Calculate Price based on Duration
                                </button>
                            </div>

                            <!-- Calculator -->
                            <div x-show="showCalculator" class="mt-2 p-4 bg-slate-5 rounded-xl border border-slate-100" x-transition style="display: none;">
                                <div class="grid grid-cols-2 gap-4 mb-2">
                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 mb-1">Rate (Rp)</label>
                                        <input type="number" x-model="rate" class="w-full text-sm rounded-lg border-slate-200" placeholder="e.g. 100000">
                                    </div>
                                    <div>
                                         <label class="block text-xs font-bold text-slate-500 mb-1">Per</label>
                                         <select x-model="rateUnit" class="w-full text-sm rounded-lg border-slate-200">
                                             <option value="minute">Minute</option>
                                             <option value="hour">Hour</option>
                                         </select>
                                    </div>
                                </div>
                                <button type="button" @click="calculatePrice()" class="w-full py-1.5 text-xs font-bold text-white bg-slate-600 hover:bg-slate-700 rounded-lg">
                                    Apply Calculation
                                </button>
                                <p class="text-[10px] text-slate-400 mt-2 text-center">Will calculate based on current duration below.</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <!-- Duration -->
                            <div>
                                <label for="duration_minutes" class="block text-sm font-bold text-slate-700 mb-2">Duration (Minutes)</label>
                                <input type="number" id="duration_minutes" name="duration_minutes" x-model="duration"
                                       class="w-full rounded-xl border-slate-200 focus:border-birawa-500 focus:ring-birawa-500 placeholder-slate-400 transition-colors shadow-sm"
                                       required min="1">
                                <x-input-error :messages="$errors->get('duration_minutes')" class="mt-2" />
                            </div>

                            <!-- Unit -->
                            <div>
                                <label for="unit" class="block text-sm font-bold text-slate-700 mb-2">Unit</label>
                                <select id="unit" name="unit" class="w-full rounded-xl border-slate-200 focus:border-birawa-500 focus:ring-birawa-500 shadow-sm transition-colors">
                                    <option value="session" {{ old('unit', $service->unit) == 'session' ? 'selected' : '' }}>Minutes / Session</option>
                                    <option value="hour" {{ old('unit', $service->unit) == 'hour' ? 'selected' : '' }}>Hours</option>
                                    <option value="pcs" {{ old('unit', $service->unit) == 'pcs' ? 'selected' : '' }}>Pieces</option>
                                </select>
                                <x-input-error :messages="$errors->get('unit')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-sm font-bold text-slate-700 mb-2">Description</label>
                            <input id="description" type="hidden" name="description" value="{{ old('description', $service->description) }}">
                            <trix-editor input="description" class="trix-content rounded-xl border-slate-200 min-h-[150px] bg-white"></trix-editor>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-6 border-t border-slate-100">
                        <button type="button" onclick="document.getElementById('delete-form').submit()" class="text-rose-600 font-bold text-sm hover:text-rose-800 transition-colors">
                            Delete Service
                        </button>
                        
                        <div class="flex items-center gap-4">
                            <a href="{{ route('services.index') }}" class="px-5 py-2.5 text-sm font-bold text-slate-600 hover:text-slate-800 hover:bg-slate-100 rounded-xl transition-colors">Cancel</a>
                            <button type="submit" class="px-5 py-2.5 text-sm font-bold text-white bg-birawa-600 hover:bg-birawa-700 rounded-xl shadow-lg shadow-birawa-500/30 transition-all transform active:scale-95">
                                Update Service
                            </button>
                        </div>
                    </div>
                </form>

                <form id="delete-form" action="{{ route('services.destroy', $service) }}" method="POST" class="hidden">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>
    </div>

    <script>
        function serviceForm() {
            return {
                price: "{{ old('price', $service->price) }}",
                duration: "{{ old('duration_minutes', $service->duration_minutes) }}",
                
                showCalculator: false,
                rate: 0,
                rateUnit: 'minute',
                
                calculatePrice() {
                    let d = parseInt(this.duration) || 0;
                    let p = 0;
                    
                    if (this.rateUnit === 'minute') {
                        p = this.rate * d;
                    } else {
                        p = this.rate * (d / 60);
                    }
                    
                    this.price = Math.round(p);
                    this.showCalculator = false;
                }
            }
        }
    </script>
</x-app-layout>
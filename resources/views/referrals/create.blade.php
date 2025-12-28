<x-app-layout>
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-slate-800">Create Referral Letter</h1>
            <p class="text-sm text-slate-500">Generate a referral letter for a patient</p>
        </div>

        <div class="bg-white rounded-3xl shadow-lg border border-slate-100 overflow-hidden">
            <div class="p-8">
                <form action="{{ route('referrals.store') }}" method="POST">
                    @csrf
                    
                    <div class="space-y-6">
                        <!-- Patient Selection -->
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Select Patient</label>
                            <div class="relative">
                                <select name="patient_id" class="w-full rounded-xl border-slate-200 focus:border-birawa-500 focus:ring-birawa-500 shadow-sm transition-colors cursor-pointer" required>
                                    <option value="">-- Select Patient --</option>
                                    @foreach($patients as $patient)
                                        <option value="{{ $patient->id }}">{{ $patient->name }} ({{ $patient->owners->first()->name ?? 'No Owner' }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Target Clinic -->
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Target Clinic / Doctor Name</label>
                            <input type="text" name="target_clinic_name" 
                                   class="w-full rounded-xl border-slate-200 focus:border-birawa-500 focus:ring-birawa-500 placeholder-slate-400 transition-colors shadow-sm"
                                   required 
                                   placeholder="e.g. Klinik Hewan Sehat / Dr. Budi">
                        </div>

                        <!-- Notes -->
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Clinical Notes & Reason for Referral</label>
                            <textarea name="notes" rows="6" 
                                      class="w-full rounded-xl border-slate-200 focus:border-birawa-500 focus:ring-birawa-500 placeholder-slate-400 transition-colors shadow-sm"
                                      placeholder="Describe the condition, history, and reason for referral..."></textarea>
                            <p class="mt-2 text-xs text-slate-500">These notes will be included in the public referral letter.</p>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end gap-4 mt-8 pt-6 border-t border-slate-100">
                        <a href="{{ route('referrals.index') }}" class="text-sm font-bold text-slate-500 hover:text-slate-800 transition-colors">
                            Cancel
                        </a>
                        <button type="submit" class="px-6 py-2.5 bg-birawa-600 text-white text-sm font-bold rounded-xl shadow-lg hover:bg-birawa-700 focus:ring-4 focus:ring-birawa-100 transition-all active:scale-95">
                            Generate Referral Link
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

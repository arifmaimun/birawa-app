<x-app-layout>
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-slate-800">Add Custom Vital Sign Field</h1>
            <p class="text-sm text-slate-500">Define a new field to track in patient medical records</p>
        </div>

        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <form action="{{ route('vital-sign-settings.store') }}" method="POST" class="p-6 md:p-8">
                @csrf
                
                <div class="space-y-6">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-bold text-slate-700 mb-2">Field Name</label>
                        <input type="text" name="name" id="name" 
                            class="w-full rounded-xl border-slate-200 focus:border-birawa-500 focus:ring-birawa-500 placeholder-slate-400 text-sm shadow-sm transition-colors"
                            placeholder="e.g. Blood Pressure (Systolic)"
                            value="{{ old('name') }}" required>
                        @error('name')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Unit -->
                    <div>
                        <label for="unit" class="block text-sm font-bold text-slate-700 mb-2">Unit (Optional)</label>
                        <input type="text" name="unit" id="unit" 
                            class="w-full rounded-xl border-slate-200 focus:border-birawa-500 focus:ring-birawa-500 placeholder-slate-400 text-sm shadow-sm transition-colors"
                            placeholder="e.g. mmHg, %, cm"
                            value="{{ old('unit') }}">
                        @error('unit')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Type -->
                    <div>
                        <label for="type" class="block text-sm font-bold text-slate-700 mb-2">Input Type</label>
                        <select name="type" id="type" 
                            class="w-full rounded-xl border-slate-200 focus:border-birawa-500 focus:ring-birawa-500 text-sm shadow-sm transition-colors">
                            <option value="number" {{ old('type') == 'number' ? 'selected' : '' }}>Number (Numeric input)</option>
                            <option value="text" {{ old('type') == 'text' ? 'selected' : '' }}>Text (String input)</option>
                        </select>
                        <p class="mt-1 text-xs text-slate-500">Choose 'Number' for measurable data like pressure or counts.</p>
                        @error('type')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-8 pt-6 border-t border-slate-100 flex items-center justify-end gap-3">
                    <a href="{{ route('vital-sign-settings.index') }}" class="px-4 py-2 text-sm font-medium text-slate-700 hover:text-slate-900 bg-white border border-slate-300 rounded-xl shadow-sm hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-birawa-500 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-birawa-600 hover:bg-birawa-700 border border-transparent rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-birawa-500 transition-colors">
                        Save Field
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

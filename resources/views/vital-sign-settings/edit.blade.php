<x-app-layout>
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-slate-800">Edit Vital Sign Field</h1>
            <p class="text-sm text-slate-500">Update custom field settings</p>
        </div>

        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <form action="{{ route('vital-sign-settings.update', $vitalSignSetting) }}" method="POST" class="p-6 md:p-8">
                @csrf
                @method('PUT')
                
                <div class="space-y-6">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-bold text-slate-700 mb-2">Field Name</label>
                        <input type="text" name="name" id="name" 
                            class="w-full rounded-xl border-slate-200 focus:border-birawa-500 focus:ring-birawa-500 placeholder-slate-400 text-sm shadow-sm transition-colors"
                            value="{{ old('name', $vitalSignSetting->name) }}" required>
                        @error('name')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Unit -->
                    <div>
                        <label for="unit" class="block text-sm font-bold text-slate-700 mb-2">Unit (Optional)</label>
                        <input type="text" name="unit" id="unit" 
                            class="w-full rounded-xl border-slate-200 focus:border-birawa-500 focus:ring-birawa-500 placeholder-slate-400 text-sm shadow-sm transition-colors"
                            value="{{ old('unit', $vitalSignSetting->unit) }}">
                        @error('unit')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Type -->
                    <div>
                        <label for="type" class="block text-sm font-bold text-slate-700 mb-2">Input Type</label>
                        <select name="type" id="type" 
                            class="w-full rounded-xl border-slate-200 focus:border-birawa-500 focus:ring-birawa-500 text-sm shadow-sm transition-colors">
                            <option value="number" {{ old('type', $vitalSignSetting->type) == 'number' ? 'selected' : '' }}>Number (Numeric input)</option>
                            <option value="text" {{ old('type', $vitalSignSetting->type) == 'text' ? 'selected' : '' }}>Text (String input)</option>
                        </select>
                        @error('type')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div class="flex items-center">
                        <input type="checkbox" name="is_active" id="is_active" value="1" 
                            {{ old('is_active', $vitalSignSetting->is_active) ? 'checked' : '' }}
                            class="h-4 w-4 text-birawa-600 focus:ring-birawa-500 border-gray-300 rounded">
                        <label for="is_active" class="ml-2 block text-sm text-slate-700">
                            Active (Show in new medical records)
                        </label>
                    </div>
                </div>

                <div class="mt-8 pt-6 border-t border-slate-100 flex items-center justify-end gap-3">
                    <a href="{{ route('vital-sign-settings.index') }}" class="px-4 py-2 text-sm font-medium text-slate-700 hover:text-slate-900 bg-white border border-slate-300 rounded-xl shadow-sm hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-birawa-500 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-birawa-600 hover:bg-birawa-700 border border-transparent rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-birawa-500 transition-colors">
                        Update Field
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

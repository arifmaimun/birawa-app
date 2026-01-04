<x-manual.layouts.app>
    <x-slot name="header">
        Edit Consent Template: {{ $consentTemplate->name }}
    </x-slot>

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <form method="POST" action="{{ route('manual.consent-templates.update', $consentTemplate) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 gap-6">
                        <!-- Name -->
                        <div>
                            <x-manual.input-label for="name" value="Name" />
                            <x-manual.text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $consentTemplate->name)" required />
                            <x-manual.input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Body -->
                        <div>
                            <x-manual.input-label for="body" value="Body (HTML supported)" />
                            <textarea id="body" name="body" rows="10" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>{{ old('body', $consentTemplate->body) }}</textarea>
                            <x-manual.input-error :messages="$errors->get('body')" class="mt-2" />
                        </div>

                        <!-- Active -->
                        <div class="flex items-center">
                            <input type="hidden" name="is_active" value="0">
                            <input id="is_active" type="checkbox" name="is_active" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" {{ old('is_active', $consentTemplate->is_active) ? 'checked' : '' }}>
                            <label for="is_active" class="ml-2 block text-sm text-gray-900">Active</label>
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-6">
                        <a href="{{ route('manual.consent-templates.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">Cancel</a>
                        <x-manual.primary-button class="ml-4">
                            {{ __('Update Template') }}
                        </x-manual.primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-manual.layouts.app>

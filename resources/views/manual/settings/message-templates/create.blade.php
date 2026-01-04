<x-manual.layouts.app>
    <x-slot name="header">
        Create Message Template
    </x-slot>

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <form method="POST" action="{{ route('manual.message-templates.store') }}">
                    @csrf
                    
                    <div class="grid grid-cols-1 gap-6">
                        <!-- Title -->
                        <div>
                            <x-manual.input-label for="title" value="Title" />
                            <x-manual.text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title')" required autofocus />
                            <x-manual.input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        <!-- Type -->
                        <div>
                            <x-manual.input-label for="type" value="Type" />
                            <select id="type" name="type" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                <option value="">Select Type</option>
                                <option value="whatsapp" {{ old('type') == 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                                <option value="email" {{ old('type') == 'email' ? 'selected' : '' }}>Email</option>
                                <option value="sms" {{ old('type') == 'sms' ? 'selected' : '' }}>SMS</option>
                            </select>
                            <x-manual.input-error :messages="$errors->get('type')" class="mt-2" />
                        </div>

                        <!-- Content Pattern -->
                        <div>
                            <x-manual.input-label for="content_pattern" value="Content Pattern" />
                            <p class="text-sm text-gray-500 mb-1">Available placeholders: {nama_klien}, {nama_pasien}, {jam_visit}</p>
                            <textarea id="content_pattern" name="content_pattern" rows="5" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>{{ old('content_pattern') }}</textarea>
                            <x-manual.input-error :messages="$errors->get('content_pattern')" class="mt-2" />
                        </div>

                        <!-- Trigger Event -->
                        <div>
                            <x-manual.input-label for="trigger_event" value="Trigger Event (Optional)" />
                            <select id="trigger_event" name="trigger_event" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="">None</option>
                                <option value="on_departure" {{ old('trigger_event') == 'on_departure' ? 'selected' : '' }}>On Departure</option>
                                <option value="on_arrival" {{ old('trigger_event') == 'on_arrival' ? 'selected' : '' }}>On Arrival</option>
                            </select>
                            <x-manual.input-error :messages="$errors->get('trigger_event')" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-6">
                        <a href="{{ route('manual.message-templates.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">Cancel</a>
                        <x-manual.primary-button class="ml-4">
                            {{ __('Create Template') }}
                        </x-manual.primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-manual.layouts.app>

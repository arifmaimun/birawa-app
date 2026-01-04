<x-manual.layouts.app>
    <x-slot name="header">
        Register New Patient
    </x-slot>

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <form method="POST" action="{{ route('manual.patients.store') }}">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Owner -->
                        <div class="md:col-span-2">
                            <div class="flex justify-between items-center">
                                <x-manual.input-label for="client_id" value="Owner (Client)" />
                                <a href="{{ route('manual.clients.create') }}" class="text-sm text-indigo-600 hover:text-indigo-900">+ New Client</a>
                            </div>
                            <select id="client_id" name="client_id" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                <option value="">-- Select Owner --</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                        {{ $client->name }} ({{ $client->phone }})
                                    </option>
                                @endforeach
                            </select>
                            <x-manual.input-error :messages="$errors->get('client_id')" class="mt-2" />
                        </div>

                        <!-- Name -->
                        <div>
                            <x-manual.input-label for="name" value="Patient Name" />
                            <x-manual.text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required />
                            <x-manual.input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Species -->
                        <div>
                            <x-manual.input-label for="species" value="Species" />
                            <select id="species" name="species" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                <option value="Cat" {{ old('species') == 'Cat' ? 'selected' : '' }}>Cat</option>
                                <option value="Dog" {{ old('species') == 'Dog' ? 'selected' : '' }}>Dog</option>
                                <option value="Bird" {{ old('species') == 'Bird' ? 'selected' : '' }}>Bird</option>
                                <option value="Other" {{ old('species') == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                            <x-manual.input-error :messages="$errors->get('species')" class="mt-2" />
                        </div>

                        <!-- Breed -->
                        <div>
                            <x-manual.input-label for="breed" value="Breed" />
                            <x-manual.text-input id="breed" class="block mt-1 w-full" type="text" name="breed" :value="old('breed')" />
                            <x-manual.input-error :messages="$errors->get('breed')" class="mt-2" />
                        </div>

                        <!-- Gender -->
                        <div>
                            <x-manual.input-label for="gender" value="Gender" />
                            <select id="gender" name="gender" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                            </select>
                            <x-manual.input-error :messages="$errors->get('gender')" class="mt-2" />
                        </div>

                        <!-- DOB -->
                        <div>
                            <x-manual.input-label for="dob" value="Date of Birth" />
                            <x-manual.text-input id="dob" class="block mt-1 w-full" type="date" name="dob" :value="old('dob')" />
                            <x-manual.input-error :messages="$errors->get('dob')" class="mt-2" />
                        </div>

                        <!-- Sterile -->
                        <div class="flex items-center mt-6">
                            <input id="is_sterile" type="checkbox" name="is_sterile" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" {{ old('is_sterile') ? 'checked' : '' }}>
                            <label for="is_sterile" class="ml-2 block text-sm text-gray-900">Is Sterile/Neutered?</label>
                        </div>

                        <!-- Allergies -->
                        <div class="md:col-span-2">
                            <x-manual.input-label for="allergies" value="Allergies" />
                            <textarea id="allergies" name="allergies" rows="2" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('allergies') }}</textarea>
                            <x-manual.input-error :messages="$errors->get('allergies')" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-6">
                        <a href="{{ route('manual.patients.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">Cancel</a>
                        <x-manual.primary-button class="ml-4">
                            {{ __('Register Patient') }}
                        </x-manual.primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-manual.layouts.app>

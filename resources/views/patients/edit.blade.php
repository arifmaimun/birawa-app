<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Patient') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('patients.update', $patient) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Owner Selection -->
                            <div class="col-span-1 md:col-span-2">
                                <label for="owner_id" class="block text-sm font-medium text-gray-700">Owner</label>
                                <select name="owner_id" id="owner_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-birawa-500 focus:ring focus:ring-birawa-500 focus:ring-opacity-50" required>
                                    <option value="">Select Owner</option>
                                    @foreach($owners as $owner)
                                        <option value="{{ $owner->id }}" {{ (old('owner_id', $patient->owners->first()->id ?? '') == $owner->id) ? 'selected' : '' }}>
                                            {{ $owner->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('owner_id')
                                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Name -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                                <input type="text" name="name" id="name" value="{{ old('name', $patient->name) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-birawa-500 focus:ring focus:ring-birawa-500 focus:ring-opacity-50" required>
                                @error('name')
                                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Species -->
                            <div>
                                <label for="species" class="block text-sm font-medium text-gray-700">Species</label>
                                <select name="species" id="species" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-birawa-500 focus:ring focus:ring-birawa-500 focus:ring-opacity-50" required>
                                    <option value="">Select Species</option>
                                    @foreach(['Kucing', 'Anjing', 'Kelinci', 'Burung', 'Lainnya'] as $spec)
                                        <option value="{{ $spec }}" {{ (old('species', $patient->species) == $spec) ? 'selected' : '' }}>{{ $spec }}</option>
                                    @endforeach
                                </select>
                                @error('species')
                                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Breed -->
                            <div>
                                <label for="breed" class="block text-sm font-medium text-gray-700">Breed</label>
                                <input type="text" name="breed" id="breed" value="{{ old('breed', $patient->breed) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-birawa-500 focus:ring focus:ring-birawa-500 focus:ring-opacity-50">
                                @error('breed')
                                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Date of Birth -->
                            <div>
                                <label for="dob" class="block text-sm font-medium text-gray-700">Date of Birth</label>
                                <input type="date" name="dob" id="dob" value="{{ old('dob', $patient->dob ? date('Y-m-d', strtotime($patient->dob)) : '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-birawa-500 focus:ring focus:ring-birawa-500 focus:ring-opacity-50">
                                @error('dob')
                                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Gender -->
                            <div class="col-span-1 md:col-span-2">
                                <span class="block text-sm font-medium text-gray-700 mb-2">Gender</span>
                                <div class="flex items-center space-x-6">
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="gender" value="jantan" class="form-radio text-birawa-600 focus:ring-birawa-500" {{ old('gender', $patient->gender) == 'jantan' ? 'checked' : '' }} required>
                                        <span class="ml-2 text-gray-700">Male (Jantan)</span>
                                    </label>
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="gender" value="betina" class="form-radio text-birawa-600 focus:ring-birawa-500" {{ old('gender', $patient->gender) == 'betina' ? 'checked' : '' }} required>
                                        <span class="ml-2 text-gray-700">Female (Betina)</span>
                                    </label>
                                </div>
                                @error('gender')
                                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('patients.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900 mr-4">
                                Cancel
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-birawa-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-birawa-500 active:bg-birawa-700 focus:outline-none focus:border-birawa-700 focus:ring focus:ring-birawa-300 disabled:opacity-25 transition">
                                Simpan Data
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if (session('success'))
                        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf
                        @method('PATCH')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Basic Info -->
                            <div class="col-span-1">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                                <div class="mb-4">
                                    <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Name</label>
                                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                                </div>
                                <div class="mb-4">
                                    <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                                </div>
                                <div class="mb-4">
                                    <label for="specialty" class="block text-gray-700 text-sm font-bold mb-2">Specialty</label>
                                    <input type="text" name="specialty" id="specialty" value="{{ old('specialty', $profile->specialty) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                </div>
                                <div class="mb-4">
                                    <label for="bio" class="block text-gray-700 text-sm font-bold mb-2">Bio</label>
                                    <textarea name="bio" id="bio" rows="3" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('bio', $profile->bio) }}</textarea>
                                </div>
                            </div>

                            <!-- Safety & Emergency -->
                            <div class="col-span-1">
                                <h3 class="text-lg font-medium text-red-900 mb-4 bg-red-50 p-2 rounded">Safety & Emergency Settings (SOS)</h3>
                                <p class="text-sm text-gray-600 mb-4">
                                    This information will be used for the SOS Panic Button during visits.
                                </p>
                                <div class="mb-4">
                                    <label for="emergency_contact_name" class="block text-gray-700 text-sm font-bold mb-2">Emergency Contact Name</label>
                                    <input type="text" name="emergency_contact_name" id="emergency_contact_name" value="{{ old('emergency_contact_name', $profile->emergency_contact_name) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="e.g. Spouse, Parent, Partner">
                                </div>
                                <div class="mb-4">
                                    <label for="emergency_contact_number" class="block text-gray-700 text-sm font-bold mb-2">Emergency Contact Number (WhatsApp)</label>
                                    <input type="text" name="emergency_contact_number" id="emergency_contact_number" value="{{ old('emergency_contact_number', $profile->emergency_contact_number) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="e.g. 628123456789">
                                    <p class="text-xs text-gray-500 mt-1">Format: 628xxx (No spaces or dashes). Must be WhatsApp active.</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Save Profile
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

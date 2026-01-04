<x-manual.layouts.app>
    <x-slot name="header">
        Profile Settings
    </x-slot>

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <form method="POST" action="{{ route('manual.profile.update') }}">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- User Account Information -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Account Information</h3>

                        <div class="space-y-4">
                            <!-- Name -->
                            <div>
                                <x-manual.input-label for="name" value="Full Name" />
                                <x-manual.text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $user->name)" required autofocus />
                                <x-manual.input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <!-- Email -->
                            <div>
                                <x-manual.input-label for="email" value="Email Address" />
                                <x-manual.text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $user->email)" required />
                                <x-manual.input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>

                            <!-- Phone -->
                            <div>
                                <x-manual.input-label for="phone" value="Phone Number" />
                                <x-manual.text-input id="phone" class="block mt-1 w-full" type="text" name="phone" :value="old('phone', $user->phone)" />
                                <x-manual.input-error :messages="$errors->get('phone')" class="mt-2" />
                            </div>

                            <!-- Address -->
                            <div>
                                <x-manual.input-label for="address" value="Address" />
                                <textarea id="address" name="address" rows="3" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('address', $user->address) }}</textarea>
                                <x-manual.input-error :messages="$errors->get('address')" class="mt-2" />
                            </div>

                            <hr class="my-6 border-gray-200">

                            <!-- Password -->
                            <div>
                                <x-manual.input-label for="password" value="New Password (Optional)" />
                                <x-manual.text-input id="password" class="block mt-1 w-full" type="password" name="password" autocomplete="new-password" />
                                <p class="text-sm text-gray-500 mt-1">Leave blank to keep current password.</p>
                                <x-manual.input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>

                            <!-- Confirm Password -->
                            <div>
                                <x-manual.input-label for="password_confirmation" value="Confirm New Password" />
                                <x-manual.text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Doctor Profile Settings -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Professional Settings</h3>

                        <div class="space-y-4">
                            <!-- Specialty -->
                            <div>
                                <x-manual.input-label for="specialty" value="Specialty" />
                                <x-manual.text-input id="specialty" class="block mt-1 w-full" type="text" name="specialty" :value="old('specialty', $doctorProfile->specialty)" placeholder="e.g. General Practitioner" />
                                <x-manual.input-error :messages="$errors->get('specialty')" class="mt-2" />
                            </div>

                            <!-- Timezone -->
                            <div>
                                <x-manual.input-label for="timezone" value="Timezone" />
                                <select id="timezone" name="timezone" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                    @foreach($timezones as $value => $label)
                                        <option value="{{ $value }}" {{ old('timezone', $doctorProfile->timezone) == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-manual.input-error :messages="$errors->get('timezone')" class="mt-2" />
                            </div>

                            <!-- Service Radius -->
                            <div>
                                <x-manual.input-label for="service_radius_km" value="Service Radius (km)" />
                                <x-manual.text-input id="service_radius_km" class="block mt-1 w-full" type="number" step="0.1" name="service_radius_km" :value="old('service_radius_km', $doctorProfile->service_radius_km)" required />
                                <x-manual.input-error :messages="$errors->get('service_radius_km')" class="mt-2" />
                            </div>

                            <!-- Base Transport Fee -->
                            <div>
                                <x-manual.input-label for="base_transport_fee" value="Base Transport Fee (Rp)" />
                                <x-manual.text-input id="base_transport_fee" class="block mt-1 w-full" type="number" name="base_transport_fee" :value="old('base_transport_fee', $doctorProfile->base_transport_fee)" required />
                                <x-manual.input-error :messages="$errors->get('base_transport_fee')" class="mt-2" />
                            </div>

                            <!-- Transport Fee Per KM -->
                            <div>
                                <x-manual.input-label for="transport_fee_per_km" value="Fee Per KM (Rp)" />
                                <x-manual.text-input id="transport_fee_per_km" class="block mt-1 w-full" type="number" name="transport_fee_per_km" :value="old('transport_fee_per_km', $doctorProfile->transport_fee_per_km)" required />
                                <x-manual.input-error :messages="$errors->get('transport_fee_per_km')" class="mt-2" />
                            </div>

                            <hr class="my-6 border-gray-200">

                            <!-- Emergency Contact -->
                            <div>
                                <x-manual.input-label for="emergency_contact_name" value="Emergency Contact Name" />
                                <x-manual.text-input id="emergency_contact_name" class="block mt-1 w-full" type="text" name="emergency_contact_name" :value="old('emergency_contact_name', $doctorProfile->emergency_contact_name)" />
                            </div>

                            <div>
                                <x-manual.input-label for="emergency_contact_number" value="Emergency Contact Number" />
                                <x-manual.text-input id="emergency_contact_number" class="block mt-1 w-full" type="text" name="emergency_contact_number" :value="old('emergency_contact_number', $doctorProfile->emergency_contact_number)" />
                            </div>

                            <!-- Bank Details -->
                            <div>
                                <x-manual.input-label for="bank_account_details" value="Bank Account Details" />
                                <textarea id="bank_account_details" name="bank_account_details" rows="2" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('bank_account_details', $doctorProfile->bank_account_details) }}</textarea>
                            </div>

                            <!-- Bio -->
                            <div>
                                <x-manual.input-label for="bio" value="Bio" />
                                <textarea id="bio" name="bio" rows="3" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('bio', $doctorProfile->bio) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end mt-6">
                <x-manual.primary-button>
                    {{ __('Save Changes') }}
                </x-manual.primary-button>
            </div>
        </form>
    </div>
</x-manual.layouts.app>

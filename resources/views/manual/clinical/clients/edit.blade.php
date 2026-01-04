<x-manual.layouts.app>
    <x-slot name="header">
        Edit Client: {{ $client->name }}
    </x-slot>

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <form method="POST" action="{{ route('manual.clients.update', $client) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Name -->
                        <div>
                            <x-manual.input-label for="name" value="Full Name" />
                            <x-manual.text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $client->name)" required />
                            <x-manual.input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Phone -->
                        <div>
                            <x-manual.input-label for="phone" value="Phone Number" />
                            <x-manual.text-input id="phone" class="block mt-1 w-full" type="text" name="phone" :value="old('phone', $client->phone)" required />
                            <x-manual.input-error :messages="$errors->get('phone')" class="mt-2" />
                        </div>

                        <!-- Email -->
                        <div>
                            <x-manual.input-label for="email" value="Email Address" />
                            <x-manual.text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $client->email)" />
                            <x-manual.input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <!-- Address -->
                        <div class="md:col-span-2">
                            <x-manual.input-label for="address" value="Address" />
                            <x-manual.text-input id="address" class="block mt-1 w-full" type="text" name="address" :value="old('address', $client->address)" />
                            <x-manual.input-error :messages="$errors->get('address')" class="mt-2" />
                        </div>

                        <!-- Business Checkbox -->
                        <div class="md:col-span-2 flex items-center">
                            <input id="is_business" type="checkbox" name="is_business" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" {{ old('is_business', $client->is_business) ? 'checked' : '' }} x-data="{}" @change="$dispatch('toggle-business')">
                            <label for="is_business" class="ml-2 block text-sm text-gray-900">Is this a Business Entity?</label>
                        </div>

                        <!-- Business Name -->
                        <div class="md:col-span-2" x-data="{ show: {{ old('is_business', $client->is_business) ? 'true' : 'false' }} }" @toggle-business.window="show = !show" x-show="show" x-transition>
                            <x-manual.input-label for="business_name" value="Business Name" />
                            <x-manual.text-input id="business_name" class="block mt-1 w-full" type="text" name="business_name" :value="old('business_name', $client->business_name)" />
                            <x-manual.input-error :messages="$errors->get('business_name')" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex items-center justify-between mt-6">
                        <button type="button" onclick="if(confirm('Are you sure you want to delete this client?')) document.getElementById('delete-form').submit();" class="text-red-600 hover:text-red-900">
                            Delete Client
                        </button>

                        <div class="flex items-center">
                            <a href="{{ route('manual.clients.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">Cancel</a>
                            <x-manual.primary-button class="ml-4">
                                {{ __('Update Client') }}
                            </x-manual.primary-button>
                        </div>
                    </div>
                </form>

                <form id="delete-form" action="{{ route('manual.clients.destroy', $client) }}" method="POST" class="hidden">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>
    </div>
</x-manual.layouts.app>

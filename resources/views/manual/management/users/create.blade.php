<x-manual.layouts.app>
    <x-slot name="header">
        Add New User
    </x-slot>

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <form method="POST" action="{{ route('manual.users.store') }}">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Name -->
                        <div>
                            <x-manual.input-label for="name" value="Name" />
                            <x-manual.text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                            <x-manual.input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Email -->
                        <div>
                            <x-manual.input-label for="email" value="Email" />
                            <x-manual.text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
                            <x-manual.input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <!-- Role (Column) -->
                        <div>
                            <x-manual.input-label for="role" value="Primary Role" />
                            <select id="role" name="role" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                <option value="veterinarian">Veterinarian</option>
                                <option value="staff">Staff</option>
                                <option value="superadmin">Superadmin</option>
                            </select>
                            <x-manual.input-error :messages="$errors->get('role')" class="mt-2" />
                        </div>

                        <!-- Spatie Roles -->
                        <div>
                            <x-manual.input-label for="roles" value="Additional Roles (Permissions)" />
                            <select id="roles" name="roles[]" multiple class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Hold Ctrl/Cmd to select multiple.</p>
                        </div>

                        <!-- Password -->
                        <div>
                            <x-manual.input-label for="password" value="Password" />
                            <x-manual.text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
                            <x-manual.input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <x-manual.input-label for="password_confirmation" value="Confirm Password" />
                            <x-manual.text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required />
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-6">
                        <a href="{{ route('manual.users.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">Cancel</a>
                        <x-manual.primary-button class="ml-4">
                            {{ __('Create User') }}
                        </x-manual.primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-manual.layouts.app>

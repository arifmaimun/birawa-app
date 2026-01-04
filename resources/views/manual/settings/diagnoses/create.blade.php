<x-manual.layouts.app>
    <x-slot name="header">
        Create Diagnosis
    </x-slot>

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <form method="POST" action="{{ route('manual.diagnoses.store') }}">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Code -->
                        <div>
                            <x-manual.input-label for="code" value="Code" />
                            <x-manual.text-input id="code" class="block mt-1 w-full" type="text" name="code" :value="old('code')" required autofocus />
                            <x-manual.input-error :messages="$errors->get('code')" class="mt-2" />
                        </div>

                        <!-- Name -->
                        <div>
                            <x-manual.input-label for="name" value="Name" />
                            <x-manual.text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required />
                            <x-manual.input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Description -->
                        <div class="col-span-2">
                            <x-manual.input-label for="description" value="Description" />
                            <textarea id="description" name="description" rows="3" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('description') }}</textarea>
                            <x-manual.input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-6">
                        <a href="{{ route('manual.diagnoses.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">Cancel</a>
                        <x-manual.primary-button class="ml-4">
                            {{ __('Create Diagnosis') }}
                        </x-manual.primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-manual.layouts.app>

<x-manual.layouts.app>
    <x-slot name="header">
        Edit Storage Location: {{ $storageLocation->name }}
    </x-slot>

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <form method="POST" action="{{ route('manual.storage-locations.update', $storageLocation) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Name -->
                        <div>
                            <x-manual.input-label for="name" value="Location Name" />
                            <x-manual.text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $storageLocation->name)" required />
                            <x-manual.input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Type -->
                        <div>
                            <x-manual.input-label for="type" value="Type" />
                            <select id="type" name="type" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="General" {{ old('type', $storageLocation->type) == 'General' ? 'selected' : '' }}>General</option>
                                <option value="Shelf" {{ old('type', $storageLocation->type) == 'Shelf' ? 'selected' : '' }}>Shelf</option>
                                <option value="Cabinet" {{ old('type', $storageLocation->type) == 'Cabinet' ? 'selected' : '' }}>Cabinet</option>
                                <option value="Refrigerator" {{ old('type', $storageLocation->type) == 'Refrigerator' ? 'selected' : '' }}>Refrigerator</option>
                                <option value="Warehouse" {{ old('type', $storageLocation->type) == 'Warehouse' ? 'selected' : '' }}>Warehouse</option>
                            </select>
                            <x-manual.input-error :messages="$errors->get('type')" class="mt-2" />
                        </div>

                        <!-- Description -->
                        <div class="md:col-span-2">
                            <x-manual.input-label for="description" value="Description" />
                            <textarea id="description" name="description" rows="3" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('description', $storageLocation->description) }}</textarea>
                            <x-manual.input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <!-- Default Checkbox -->
                        <div class="md:col-span-2 flex items-center">
                            <input id="is_default" type="checkbox" name="is_default" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" {{ old('is_default', $storageLocation->is_default) ? 'checked' : '' }}>
                            <label for="is_default" class="ml-2 block text-sm text-gray-900">Set as Default Location</label>
                        </div>
                    </div>

                    <div class="flex items-center justify-between mt-6">
                        <button type="button" onclick="if(confirm('Are you sure you want to delete this location?')) document.getElementById('delete-form').submit();" class="text-red-600 hover:text-red-900">
                            Delete Location
                        </button>

                        <div class="flex items-center">
                            <a href="{{ route('manual.storage-locations.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">Cancel</a>
                            <x-manual.primary-button class="ml-4">
                                {{ __('Update Location') }}
                            </x-manual.primary-button>
                        </div>
                    </div>
                </form>

                <form id="delete-form" action="{{ route('manual.storage-locations.destroy', $storageLocation) }}" method="POST" class="hidden">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>
    </div>
</x-manual.layouts.app>

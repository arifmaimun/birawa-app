<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Add New Owner') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
                <form action="{{ route('owners.store') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-slate-700">Full Name</label>
                        <input type="text" name="name" id="name" 
                               class="mt-1 block w-full rounded-xl border-slate-200 shadow-sm focus:border-birawa-500 focus:ring focus:ring-birawa-500 focus:ring-opacity-50" 
                               value="{{ old('name') }}" required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phone -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-slate-700">Phone Number</label>
                        <input type="text" name="phone" id="phone" 
                               class="mt-1 block w-full rounded-xl border-slate-200 shadow-sm focus:border-birawa-500 focus:ring focus:ring-birawa-500 focus:ring-opacity-50" 
                               value="{{ old('phone') }}" required>
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-700">Email Address (Optional)</label>
                        <input type="email" name="email" id="email" 
                               class="mt-1 block w-full rounded-xl border-slate-200 shadow-sm focus:border-birawa-500 focus:ring focus:ring-birawa-500 focus:ring-opacity-50" 
                               value="{{ old('email') }}">
                        <p class="mt-1 text-xs text-slate-500">If left blank, a placeholder email will be generated.</p>
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Address -->
                    <div>
                        <label for="address" class="block text-sm font-medium text-slate-700">Address</label>
                        <textarea name="address" id="address" rows="3" 
                                  class="mt-1 block w-full rounded-xl border-slate-200 shadow-sm focus:border-birawa-500 focus:ring focus:ring-birawa-500 focus:ring-opacity-50" 
                                  >{{ old('address') }}</textarea>
                        @error('address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-end gap-4 pt-4 border-t border-slate-100">
                        <a href="{{ route('owners.index') }}" class="text-sm font-medium text-slate-600 hover:text-slate-900">
                            Cancel
                        </a>
                        <button type="submit" class="inline-flex justify-center items-center px-4 py-2 bg-birawa-600 border border-transparent rounded-xl font-semibold text-sm text-white hover:bg-birawa-700 focus:outline-none focus:ring-2 focus:ring-birawa-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm">
                            Save Owner
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
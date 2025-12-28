<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-slate-800">Edit Profile</h1>
            <p class="text-sm text-slate-500">Manage your personal information and safety settings</p>
        </div>

        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-6 md:p-8">
                @if (session('success'))
                    <div class="mb-6 bg-emerald-50 border border-emerald-100 text-emerald-600 px-4 py-3 rounded-xl flex items-center gap-2" role="alert">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span class="font-bold text-sm">{{ session('success') }}</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PATCH')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Basic Info -->
                        <div class="space-y-6">
                            <div class="flex items-center gap-3 pb-2 border-b border-slate-100">
                                <div class="w-8 h-8 rounded-full bg-birawa-100 flex items-center justify-center text-birawa-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <h3 class="text-lg font-bold text-slate-800">Basic Information</h3>
                            </div>
                            
                            <div>
                                <label for="name" class="block text-slate-700 text-sm font-bold mb-2">Name</label>
                                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" class="w-full rounded-xl border-slate-200 shadow-sm focus:border-birawa-500 focus:ring-birawa-500 transition-colors" required>
                            </div>
                            
                            <div>
                                <label for="email" class="block text-slate-700 text-sm font-bold mb-2">Email</label>
                                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" class="w-full rounded-xl border-slate-200 shadow-sm focus:border-birawa-500 focus:ring-birawa-500 transition-colors" required>
                            </div>
                            
                            <div>
                                <label for="specialty" class="block text-slate-700 text-sm font-bold mb-2">Specialty</label>
                                <input type="text" name="specialty" id="specialty" value="{{ old('specialty', $profile->specialty) }}" class="w-full rounded-xl border-slate-200 shadow-sm focus:border-birawa-500 focus:ring-birawa-500 transition-colors">
                            </div>
                            
                            <div>
                                <label for="bio" class="block text-slate-700 text-sm font-bold mb-2">Bio</label>
                                <textarea name="bio" id="bio" rows="3" class="w-full rounded-xl border-slate-200 shadow-sm focus:border-birawa-500 focus:ring-birawa-500 transition-colors">{{ old('bio', $profile->bio) }}</textarea>
                            </div>
                        </div>

                        <!-- Safety & Emergency -->
                        <div class="space-y-6">
                            <div class="flex items-center gap-3 pb-2 border-b border-red-100">
                                <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center text-red-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>
                                <h3 class="text-lg font-bold text-red-900">Safety & Emergency (SOS)</h3>
                            </div>

                            <div class="bg-red-50 p-4 rounded-2xl border border-red-100">
                                <p class="text-sm text-red-700 font-medium">
                                    This information will be used for the SOS Panic Button during visits.
                                </p>
                            </div>

                            <div>
                                <label for="emergency_contact_name" class="block text-slate-700 text-sm font-bold mb-2">Emergency Contact Name</label>
                                <input type="text" name="emergency_contact_name" id="emergency_contact_name" value="{{ old('emergency_contact_name', $profile->emergency_contact_name) }}" class="w-full rounded-xl border-slate-200 shadow-sm focus:border-red-500 focus:ring-red-500 transition-colors" placeholder="e.g. Spouse, Parent, Partner">
                            </div>
                            
                            <div>
                                <label for="emergency_contact_number" class="block text-slate-700 text-sm font-bold mb-2">Emergency Contact Number (WhatsApp)</label>
                                <input type="text" name="emergency_contact_number" id="emergency_contact_number" value="{{ old('emergency_contact_number', $profile->emergency_contact_number) }}" class="w-full rounded-xl border-slate-200 shadow-sm focus:border-red-500 focus:ring-red-500 transition-colors" placeholder="e.g. 628123456789">
                                <p class="text-xs text-slate-500 mt-2 flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Format: 628xxx (No spaces or dashes). Must be WhatsApp active.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-8 pt-6 border-t border-slate-100">
                        <button type="submit" class="inline-flex justify-center items-center px-6 py-3 bg-birawa-600 border border-transparent rounded-xl font-bold text-sm text-white uppercase tracking-widest hover:bg-birawa-700 focus:bg-birawa-700 active:bg-birawa-900 focus:outline-none focus:ring-2 focus:ring-birawa-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-lg shadow-birawa-100">
                            Save Profile
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

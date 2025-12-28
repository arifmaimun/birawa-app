<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="max-w-3xl mx-auto">
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-birawa-100 text-birawa-600 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <h2 class="text-3xl font-bold text-slate-800">Clinic Setup</h2>
                <p class="mt-2 text-slate-500">Let's set up your clinic profile to get started.</p>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-6 border-b border-slate-100 bg-slate-50">
                    <h3 class="text-lg font-bold text-slate-800">Clinic Information</h3>
                </div>
                
                <div class="p-6">
                    <form action="{{ route('onboarding.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="space-y-6">
                            <!-- Clinic Name -->
                            <div>
                                <label class="block text-slate-700 text-sm font-bold mb-2">Clinic Name</label>
                                <input type="text" name="clinic_name" class="w-full rounded-xl border-slate-200 shadow-sm focus:border-birawa-500 focus:ring-birawa-500 transition-colors" placeholder="e.g. Birawa Vet Clinic" required>
                            </div>

                            <!-- Address -->
                            <div>
                                <label class="block text-slate-700 text-sm font-bold mb-2">Address</label>
                                <textarea name="address" rows="3" class="w-full rounded-xl border-slate-200 shadow-sm focus:border-birawa-500 focus:ring-birawa-500 transition-colors" placeholder="Full address of your clinic" required></textarea>
                            </div>

                            <!-- Contact Info -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-slate-700 text-sm font-bold mb-2">Phone Number</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                            </svg>
                                        </div>
                                        <input type="text" name="phone" class="w-full pl-10 rounded-xl border-slate-200 shadow-sm focus:border-birawa-500 focus:ring-birawa-500 transition-colors" placeholder="+62..." required>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-slate-700 text-sm font-bold mb-2">Email</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                        <input type="email" name="email" class="w-full pl-10 rounded-xl border-slate-200 shadow-sm focus:border-birawa-500 focus:ring-birawa-500 transition-colors" placeholder="clinic@example.com" required>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Logo Upload -->
                            <div>
                                <label class="block text-slate-700 text-sm font-bold mb-2">Clinic Logo (Optional)</label>
                                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-slate-200 border-dashed rounded-xl hover:border-birawa-400 transition-colors cursor-pointer bg-slate-50">
                                    <div class="space-y-1 text-center">
                                        <svg class="mx-auto h-12 w-12 text-slate-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <div class="flex text-sm text-slate-600 justify-center">
                                            <label for="logo-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-birawa-600 hover:text-birawa-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-birawa-500 px-2">
                                                <span>Upload a file</span>
                                                <input id="logo-upload" name="logo" type="file" class="sr-only">
                                            </label>
                                            <p class="pl-1">or drag and drop</p>
                                        </div>
                                        <p class="text-xs text-slate-500">
                                            PNG, JPG, GIF up to 2MB
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="pt-4">
                                <button type="submit" class="w-full bg-birawa-500 text-white font-bold py-3 px-4 rounded-xl hover:bg-birawa-600 transition-colors shadow-sm flex items-center justify-center gap-2">
                                    <span>Complete Setup</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

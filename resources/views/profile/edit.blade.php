<x-app-layout>
    <!-- Success Message -->
    @if (session('success') || session('status') === 'profile-updated')
        <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 4000)" 
             class="fixed top-20 right-4 z-50 bg-green-50 text-green-700 px-6 py-4 rounded-xl border border-green-200 shadow-lg flex items-center gap-3">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            <span class="font-bold">{{ session('success') ?? 'Profile updated successfully.' }}</span>
            <button @click="show = false" class="ml-4 text-green-500 hover:text-green-700"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
        </div>
    @endif

    <div class="max-w-xl px-4 py-6 mx-auto pb-28" x-data="{ isSubmittingProfile: false, isSubmittingPassword: false }">
        
        <!-- Section 1: Profile Information -->
        <div class="mb-6 bg-white border border-gray-100 shadow-sm rounded-2xl overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-50 bg-gray-50/50 flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-birawa-100 flex items-center justify-center text-birawa-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                </div>
                <h2 class="font-bold text-gray-800">Profile Information</h2>
            </div>
            
            <div class="p-5" x-data="avatarUploader('{{ $user->avatar ? Storage::url($user->avatar) : '' }}')">
                <form id="profile-update-form" method="post" action="{{ route('profile.update') }}" class="space-y-4" @submit="isSubmittingProfile = true">
                    @csrf
                    @method('patch')

                    <!-- Avatar Upload Section -->
                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">Profile Photo</label>
                        <div class="flex items-center gap-4">
                            <div class="relative w-16 h-16 rounded-full overflow-hidden bg-gray-100 border border-gray-200 group">
                                <template x-if="avatarUrl">
                                    <img :src="avatarUrl" alt="{{ $user->name }}" class="w-full h-full object-cover">
                                </template>
                                <template x-if="!avatarUrl">
                                    <svg class="w-full h-full text-gray-300" fill="currentColor" viewBox="0 0 24 24"><path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                                </template>
                                <!-- Overlay -->
                                <div @click="$refs.fileInput.click()" class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 flex items-center justify-center cursor-pointer transition-opacity">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                </div>
                            </div>
                            
                            <div>
                                <input type="file" x-ref="fileInput" @change="onFileSelect" class="hidden" accept="image/jpeg,image/png,image/webp">
                                <button type="button" @click="$refs.fileInput.click()" class="w-full sm:w-auto px-5 py-3 sm:px-4 sm:py-2 bg-white border border-gray-300 rounded-xl sm:rounded-lg text-base sm:text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-birawa-500 transition-colors shadow-sm">
                                    Change Photo
                                </button>
                                <p class="mt-2 sm:mt-1 text-xs text-gray-500">JPG, PNG, or WEBP. Max 2MB.</p>
                                <p x-show="errorMessage" x-text="errorMessage" class="text-xs text-red-600 mt-1 font-medium"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Cropper Modal -->
                    <div x-show="isCropping" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                        <!-- Mobile: Bottom Sheet / Desktop: Centered Modal -->
                        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-0 text-center md:block md:p-0">
                            
                            <!-- Backdrop -->
                            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="cancelCrop"></div>

                            <!-- Centering trick for desktop -->
                            <span class="hidden md:inline-block md:align-middle md:h-screen" aria-hidden="true">&#8203;</span>

                            <!-- Modal Panel -->
                            <div class="inline-block align-bottom bg-white rounded-t-2xl md:rounded-lg text-left overflow-hidden shadow-xl transform transition-all 
                                        w-full md:my-8 md:align-middle md:max-w-lg lg:max-w-xl
                                        absolute bottom-0 left-0 right-0 md:relative">
                                
                                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                    <div class="md:flex md:items-start">
                                        <div class="w-full">
                                            <h3 class="text-lg leading-6 font-bold text-gray-900 mb-4 flex items-center justify-between">
                                                <span>Crop Image</span>
                                                <!-- Desktop Hint -->
                                                <span class="hidden lg:inline-block text-xs font-normal text-gray-500 bg-gray-100 px-2 py-1 rounded">
                                                    Scroll to zoom • Drag to move
                                                </span>
                                            </h3>
                                            
                                            <!-- Crop Area: Mobile (h-72) vs Desktop (h-96) -->
                                            <div class="relative w-full h-72 lg:h-96 bg-gray-100 rounded-lg overflow-hidden flex items-center justify-center touch-none">
                                                <img id="crop-image" src="" alt="Picture" class="max-w-full max-h-full block">
                                            </div>

                                            <!-- Controls -->
                                            <div class="mt-6 flex flex-col items-center gap-5">
                                                <!-- Zoom Slider: Optimized for Touch -->
                                                <div class="flex items-center gap-4 w-full max-w-sm px-2">
                                                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM13 10H7"></path></svg>
                                                    <!-- Larger touch target for range input -->
                                                    <input type="range" min="0.1" max="3.0" step="0.01" x-model="zoomLevel" @input="setZoom($event.target.value)" 
                                                           class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer focus:outline-none focus:ring-2 focus:ring-birawa-500">
                                                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path></svg>
                                                </div>

                                                <!-- Rotation Controls: Larger buttons on mobile -->
                                                <div class="flex justify-center gap-4 w-full md:w-auto">
                                                    <button type="button" @click="rotate(-90)" class="flex-1 md:flex-none p-3 md:p-2 text-gray-600 hover:text-birawa-600 bg-gray-100 hover:bg-birawa-50 rounded-xl transition-colors" title="Rotate Left">
                                                        <svg class="w-6 h-6 md:w-5 md:h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                                                        <span class="sr-only">Rotate Left</span>
                                                    </button>
                                                    <button type="button" @click="rotate(90)" class="flex-1 md:flex-none p-3 md:p-2 text-gray-600 hover:text-birawa-600 bg-gray-100 hover:bg-birawa-50 rounded-xl transition-colors" title="Rotate Right">
                                                        <svg class="w-6 h-6 md:w-5 md:h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 10H11a8 8 0 00-8 8v2M21 10l-6 6m6-6l-6-6"></path></svg>
                                                        <span class="sr-only">Rotate Right</span>
                                                    </button>
                                                    <button type="button" @click="resetCrop()" class="flex-1 md:flex-none p-3 md:p-2 text-gray-600 hover:text-birawa-600 bg-gray-100 hover:bg-birawa-50 rounded-xl transition-colors" title="Reset">
                                                        <svg class="w-6 h-6 md:w-5 md:h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                                        <span class="sr-only">Reset</span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Footer Actions -->
                                <div class="bg-gray-50 px-4 py-4 sm:px-6 md:flex md:flex-row-reverse gap-2">
                                    <button type="button" @click="saveCrop()" class="w-full inline-flex justify-center items-center rounded-xl border border-transparent shadow-sm px-4 py-3 md:py-2 bg-birawa-600 text-base font-medium text-white hover:bg-birawa-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-birawa-500 md:w-auto md:text-sm transition-colors mb-3 md:mb-0">
                                        <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        Save & Upload
                                    </button>
                                    <button type="button" @click="cancelCrop()" class="w-full inline-flex justify-center items-center rounded-xl border border-gray-300 shadow-sm px-4 py-3 md:py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-birawa-500 md:w-auto md:text-sm transition-colors">
                                        Cancel
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Uploading Overlay -->
                    <div x-show="isUploading" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
                        <div class="bg-white rounded-xl p-6 max-w-sm w-full mx-4 shadow-xl">
                            <div class="flex flex-col items-center">
                                <div class="w-full bg-gray-200 rounded-full h-2.5 mb-4 dark:bg-gray-700">
                                    <div class="bg-birawa-600 h-2.5 rounded-full transition-all duration-300" :style="'width: ' + progress + '%'"></div>
                                </div>
                                <p class="text-sm font-medium text-gray-700 mb-1" x-text="uploadStatus"></p>
                                <p class="text-xs text-gray-500" x-text="progress + '%'"></p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="name" class="block mb-1 text-sm font-medium text-gray-700">Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" class="block w-full px-4 py-3 text-gray-700 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-birawa-500 focus:border-birawa-500" required autofocus autocomplete="name">
                        <x-input-error class="mt-2" :messages="$errors->get('name')" />
                    </div>

                    <div>
                        <label for="email" class="block mb-1 text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" class="block w-full px-4 py-3 text-gray-700 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-birawa-500 focus:border-birawa-500" required autocomplete="username">
                        <x-input-error class="mt-2" :messages="$errors->get('email')" />
                    </div>
                </form>
            </div>
        </div>

        <!-- Section 2: Update Password -->
        <div class="mb-6 bg-white border border-gray-100 shadow-sm rounded-2xl overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-50 bg-gray-50/50 flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-orange-100 flex items-center justify-center text-orange-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                </div>
                <h2 class="font-bold text-gray-800">Update Password</h2>
            </div>

            <div class="p-5">
                <form method="post" action="{{ route('password.update') }}" class="space-y-4" @submit="isSubmittingPassword = true">
                    @csrf
                    @method('put')

                    <div>
                        <label for="current_password" class="block mb-1 text-sm font-medium text-gray-700">Current Password</label>
                        <input type="password" name="current_password" id="current_password" class="block w-full px-4 py-3 text-gray-700 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-birawa-500 focus:border-birawa-500" autocomplete="current-password">
                        <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
                    </div>

                    <div>
                        <label for="password" class="block mb-1 text-sm font-medium text-gray-700">New Password</label>
                        <input type="password" name="password" id="password" class="block w-full px-4 py-3 text-gray-700 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-birawa-500 focus:border-birawa-500" autocomplete="new-password">
                        <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
                    </div>

                    <div>
                        <label for="password_confirmation" class="block mb-1 text-sm font-medium text-gray-700">Confirm Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="block w-full px-4 py-3 text-gray-700 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-birawa-500 focus:border-birawa-500" autocomplete="new-password">
                        <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
                    </div>
                    
                    <div class="flex items-center gap-4">
                         <!-- Separate Save Button for Password -->
                        <button type="submit" 
                            :disabled="isSubmittingPassword"
                            :class="{'opacity-75 cursor-not-allowed': isSubmittingPassword}"
                            class="px-4 py-2 bg-gray-800 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 transition ease-in-out duration-150 flex items-center gap-2">
                            <span x-show="isSubmittingPassword" class="animate-spin rounded-full h-3 w-3 border-b-2 border-white"></span>
                            <span x-text="isSubmittingPassword ? 'Saving...' : 'Save Password'"></span>
                        </button>

                        @if (session('status') === 'password-updated')
                            <p
                                x-data="{ show: true }"
                                x-show="show"
                                x-transition
                                x-init="setTimeout(() => show = false, 2000)"
                                class="text-sm text-gray-600"
                            >{{ __('Saved.') }}</p>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <!-- Section 3: Delete Account -->
        <div class="mb-6 bg-white border border-red-100 shadow-sm rounded-2xl overflow-hidden">
            <div class="px-5 py-4 border-b border-red-50 bg-red-50/50 flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center text-red-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </div>
                <h2 class="font-bold text-red-800">Delete Account</h2>
            </div>

            <div class="p-5">
                <p class="text-sm text-gray-600 mb-4">
                    {{ __('Once your account is deleted, all of its resources and data will be permanently deleted.') }}
                </p>

                <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')" class="px-4 py-2 bg-red-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    {{ __('Delete Account') }}
                </button>

                <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
                    <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
                        @csrf
                        @method('delete')

                        <h2 class="text-lg font-medium text-gray-900">
                            {{ __('Are you sure you want to delete your account?') }}
                        </h2>

                        <p class="mt-1 text-sm text-gray-600">
                            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
                        </p>

                        <div class="mt-6">
                            <x-input-label for="password" value="{{ __('Password') }}" class="sr-only" />

                            <x-text-input
                                id="password"
                                name="password"
                                type="password"
                                class="mt-1 block w-3/4"
                                placeholder="{{ __('Password') }}"
                            />

                            <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
                        </div>

                        <div class="mt-6 flex justify-end">
                            <x-secondary-button x-on:click="$dispatch('close')">
                                {{ __('Cancel') }}
                            </x-secondary-button>

                            <x-danger-button class="ms-3">
                                {{ __('Delete Account') }}
                            </x-danger-button>
                        </div>
                    </form>
                </x-modal>
            </div>
        </div>

        <!-- Fixed Bottom Action Bar (For Main Profile Update) -->
        <div class="fixed bottom-20 left-0 right-0 p-4 bg-white/80 backdrop-blur-md border-t border-gray-200 z-20"> 
            <div class="max-w-xl mx-auto flex gap-3">
                 <a href="{{ route('dashboard') }}" class="flex-1 px-4 py-3.5 text-center font-bold text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors">
                    Back
                </a>
                <button 
                    @click="document.getElementById('profile-update-form').dispatchEvent(new Event('submit', { cancelable: true })); document.getElementById('profile-update-form').submit()"
                    :disabled="isSubmittingProfile"
                    :class="{'opacity-75 cursor-not-allowed': isSubmittingProfile}"
                    class="flex-[2] px-4 py-3.5 text-center font-bold text-white bg-birawa-600 rounded-xl shadow-lg shadow-birawa-500/30 hover:bg-birawa-700 transition-all flex justify-center items-center gap-2">
                    <span x-show="isSubmittingProfile" class="animate-spin rounded-full h-5 w-5 border-b-2 border-white"></span>
                    <span x-text="isSubmittingProfile ? 'Saving...' : 'Save Changes'"></span>
                </button>
            </div>
        </div>
    </div>
</x-app-layout>

<div x-data="{ 
    sending: false,
    sendSOS() {
        if (this.sending) return;
        this.sending = true;
        
        if (!navigator.geolocation) {
            alert('Geolocation is not supported by your browser');
            this.sending = false;
            return;
        }

        navigator.geolocation.getCurrentPosition((position) => {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;
            // Get emergency contact from auth user's profile
            const phone = '{{ Auth::user()->doctorProfile->emergency_contact_number ?? '' }}';
            
            if (!phone) {
                alert('Please set your Emergency Contact Number in Profile settings first!');
                window.location.href = '{{ route('profile.edit') }}';
                return;
            }

            const text = `SOS! Saya butuh bantuan segera. Lokasi saya: https://maps.google.com/?q=${lat},${lng}`;
            const url = `https://wa.me/${phone}?text=${encodeURIComponent(text)}`;
            
            window.open(url, '_blank');
            this.sending = false;
        }, (error) => {
            alert('Unable to retrieve your location: ' + error.message);
            this.sending = false;
        });
    }
}" class="fixed bottom-6 right-6 z-50">
    <button @click="sendSOS()" class="w-16 h-16 bg-red-600 rounded-full shadow-lg shadow-red-600/30 flex items-center justify-center text-white hover:bg-red-700 active:scale-95 transition-all animate-pulse" title="Emergency SOS">
        <span x-show="!sending" class="font-bold text-lg">SOS</span>
        <svg x-show="sending" class="animate-spin h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" style="display: none;">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    </button>
</div>
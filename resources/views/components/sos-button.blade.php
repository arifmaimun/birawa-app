<div class="fixed bottom-6 right-6 z-50">
    <button 
        id="sosBtn"
        class="bg-red-600 hover:bg-red-700 text-white rounded-full p-4 shadow-xl flex items-center justify-center animate-pulse transition-transform active:scale-95"
        style="width: 64px; height: 64px;"
        title="Emergency SOS"
    >
        <span class="font-bold text-lg">SOS</span>
    </button>
</div>

<script>
    document.getElementById('sosBtn').addEventListener('click', function() {
        if (!confirm('Are you sure you want to send an SOS alert?')) return;

        if ("geolocation" in navigator) {
            navigator.geolocation.getCurrentPosition(function(position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                // Replace with actual emergency number
                const phoneNumber = "628123456789"; 
                const message = `SOS! Help needed at: https://maps.google.com/?q=${lat},${lng}`;
                const url = `https://wa.me/${phoneNumber}?text=${encodeURIComponent(message)}`;
                
                window.open(url, '_blank');
            }, function(error) {
                alert("Error getting location: " + error.message);
            });
        } else {
            alert("Geolocation is not supported by this browser.");
        }
    });
</script>
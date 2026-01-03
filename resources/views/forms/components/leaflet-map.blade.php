<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <div
        x-data="{
            state: $wire.$entangle('{{ $getStatePath() }}'),
            map: null,
            marker: null,
            init() {
                // Wait for Leaflet to load if not present
                if (!window.L) {
                    const link = document.createElement('link');
                    link.rel = 'stylesheet';
                    link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
                    document.head.appendChild(link);

                    const script = document.createElement('script');
                    script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
                    script.onload = () => this.initMap();
                    document.head.appendChild(script);
                } else {
                    this.initMap();
                }
            },
            initMap() {
                // Default to Jakarta if no state
                let lat = -6.200000;
                let lng = 106.816666;

                if (this.state) {
                    const parts = this.state.split(',');
                    if (parts.length === 2) {
                        lat = parseFloat(parts[0]);
                        lng = parseFloat(parts[1]);
                    }
                }

                this.map = L.map(this.$refs.map).setView([lat, lng], 13);

                L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; <a href=\'http://www.openstreetmap.org/copyright\'>OpenStreetMap</a>'
                }).addTo(this.map);

                this.marker = L.marker([lat, lng], { draggable: true }).addTo(this.map);

                this.marker.on('dragend', (e) => {
                    const position = e.target.getLatLng();
                    this.state = `${position.lat.toFixed(6)},${position.lng.toFixed(6)}`;
                    // Trigger Livewire update if needed
                    $wire.$refresh(); 
                });

                this.map.on('click', (e) => {
                    this.marker.setLatLng(e.latlng);
                    this.state = `${e.latlng.lat.toFixed(6)},${e.latlng.lng.toFixed(6)}`;
                    $wire.$refresh();
                });
                
                // Watch for external state changes (e.g. from address selection)
                this.$watch('state', value => {
                    if (value) {
                        const parts = value.split(',');
                        if (parts.length === 2) {
                            const newLat = parseFloat(parts[0]);
                            const newLng = parseFloat(parts[1]);
                            const newLatLng = new L.LatLng(newLat, newLng);
                            
                            this.marker.setLatLng(newLatLng);
                            this.map.panTo(newLatLng);
                        }
                    }
                });
            }
        }"
        wire:ignore
    >
        <div x-ref="map" style="height: 400px; width: 100%; border-radius: 0.5rem; z-index: 0;"></div>
    </div>
</x-dynamic-component>

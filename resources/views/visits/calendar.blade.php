<x-app-layout>
    <div class="py-6" x-data="calendarApp({{ \Illuminate\Support\Js::from($statuses->pluck('slug')) }})">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row gap-6">
                
                <!-- Sidebar Filters -->
                <div class="w-full lg:w-1/4 space-y-6">
                    <!-- Actions -->
                    <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-100 space-y-3">
                        <a href="{{ route('visits.create') }}" class="flex items-center justify-center gap-2 w-full py-2.5 bg-birawa-600 hover:bg-birawa-700 text-white rounded-xl font-bold shadow-lg shadow-birawa-500/30 transition-all active:scale-95">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Buat Jadwal
                        </a>

                        <button @click="openRouteModal()" class="flex items-center justify-center gap-2 w-full py-2.5 bg-white border-2 border-birawa-100 hover:border-birawa-600 text-birawa-600 hover:text-birawa-700 rounded-xl font-bold transition-all active:scale-95">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                            </svg>
                            Rekomendasi Rute
                        </button>
                    </div>

                    <!-- View Mode Toggle -->
                    <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-100">
                         <label class="block text-xs font-bold text-slate-500 uppercase mb-3">Mode Tampilan</label>
                         <div class="flex bg-slate-100 rounded-lg p-1">
                             <button @click="switchView('calendar')" :class="viewMode === 'calendar' ? 'bg-white text-birawa-600 shadow-sm' : 'text-slate-500 hover:text-slate-700'" class="flex-1 py-2 rounded-md text-sm font-bold transition-all flex items-center justify-center gap-2">
                                 <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                 </svg>
                                 Kalender
                             </button>
                             <button @click="switchView('map')" :class="viewMode === 'map' ? 'bg-white text-birawa-600 shadow-sm' : 'text-slate-500 hover:text-slate-700'" class="flex-1 py-2 rounded-md text-sm font-bold transition-all flex items-center justify-center gap-2">
                                 <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                                 </svg>
                                 Peta
                             </button>
                         </div>
                    </div>

                    <!-- Filters -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
                        <h3 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                            </svg>
                            Filter Jadwal
                        </h3>
                        
                        <!-- Search -->
                        <div class="mb-6">
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Pencarian</label>
                            <div class="relative">
                                <input type="text" x-model.debounce.500ms="search" placeholder="Cari pasien/klien..." class="w-full rounded-xl border-slate-200 focus:border-birawa-500 focus:ring focus:ring-birawa-200 transition-all pl-10 text-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-400 absolute left-3 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                        </div>

                        <!-- Statuses -->
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-3">Status Kunjungan</label>
                            <div class="space-y-2.5">
                                @foreach($statuses as $status)
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <div class="relative flex items-center">
                                        <input type="checkbox" value="{{ $status->slug }}" x-model="selectedStatuses" class="peer h-5 w-5 rounded-md border-slate-300 text-birawa-600 focus:ring-birawa-200 transition-all cursor-pointer">
                                    </div>
                                    <span class="flex items-center gap-2 text-sm text-slate-600 group-hover:text-slate-900 transition-colors">
                                        <span class="w-2.5 h-2.5 rounded-full" style="background-color: {{ $status->color }}"></span>
                                        {{ $status->name }}
                                    </span>
                                </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Calendar Area -->
                <div class="flex-1 bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden relative min-h-[600px]">
                    <!-- Loading Overlay -->
                    <div id="calendar-loading" class="absolute inset-0 z-20 bg-white/80 backdrop-blur-sm flex items-center justify-center transition-opacity duration-300" style="display: none;">
                        <div class="flex flex-col items-center">
                            <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-birawa-600 mb-3"></div>
                            <span class="text-birawa-600 font-bold text-sm">Memuat jadwal...</span>
                        </div>
                    </div>

                    <div x-show="viewMode === 'calendar'" id="calendar" class="p-6 h-full font-sans"></div>
                    
                    <div x-show="viewMode === 'map'" class="h-full w-full relative">
                        <div id="main-map" class="w-full h-full z-0"></div>
                        <div class="absolute top-4 right-4 z-10 bg-white p-3 rounded-xl shadow-lg border border-slate-100 max-w-xs">
                            <h4 class="font-bold text-slate-800 text-sm mb-2">Legenda</h4>
                            <div class="space-y-2 text-xs">
                                @foreach($statuses as $status)
                                <div class="flex items-center gap-2">
                                    <span class="w-3 h-3 rounded-full" style="background-color: {{ $status->color }}"></span>
                                    <span>{{ $status->name }}</span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    <!-- Route Recommendation Modal -->
    <div x-show="showRouteModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showRouteModal" 
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" 
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" 
                 class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="closeRouteModal()"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="showRouteModal" 
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 flex justify-between items-center" id="modal-title">
                                <span>Rekomendasi Rute Harian</span>
                                <button @click="closeRouteModal()" class="text-gray-400 hover:text-gray-500">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </h3>
                            
                            <div class="mt-4 flex gap-4 items-end">
                                <div class="w-1/3">
                                    <label class="block text-sm font-bold text-gray-700 mb-1">Pilih Tanggal</label>
                                    <input type="date" x-model="routeDate" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-birawa-500 focus:ring-birawa-500">
                                </div>
                                <button @click="fetchRoute()" class="bg-birawa-600 hover:bg-birawa-700 text-white font-bold py-2 px-4 rounded-lg shadow transition-colors flex items-center gap-2" :disabled="isLoadingRoute">
                                    <span x-show="isLoadingRoute" class="animate-spin h-4 w-4 border-2 border-white border-t-transparent rounded-full"></span>
                                    Cari Rute Terbaik
                                </button>
                            </div>

                            <div class="mt-6 flex flex-col lg:flex-row gap-6 h-[500px]">
                                <!-- List -->
                                <div class="w-full lg:w-1/3 overflow-y-auto pr-2">
                                    <template x-if="routeVisits.length === 0 && !isLoadingRoute && !routeError">
                                        <div class="text-center text-gray-500 py-10">
                                            <p>Pilih tanggal untuk melihat rute.</p>
                                        </div>
                                    </template>
                                    
                                    <template x-if="routeError">
                                        <div class="bg-red-50 text-red-700 p-4 rounded-lg text-sm">
                                            <p x-text="routeError"></p>
                                        </div>
                                    </template>

                                    <div class="space-y-3">
                                        <template x-for="(visit, index) in routeVisits" :key="visit.id">
                                            <div class="flex gap-3 p-3 bg-gray-50 rounded-xl border border-gray-100 hover:shadow-md transition-all cursor-pointer" @click="focusOnMap(visit)">
                                                <div class="flex-shrink-0 w-8 h-8 bg-birawa-100 text-birawa-700 rounded-full flex items-center justify-center font-bold text-sm" x-text="index + 1"></div>
                                                <div class="flex-1">
                                                    <div class="font-bold text-gray-900 text-sm" x-text="visit.patient.name"></div>
                                                    <div class="text-xs text-gray-500" x-text="visit.patient.client.name"></div>
                                                    <div class="text-xs text-gray-400 mt-1 truncate w-48" x-text="visit.patient.client.address || 'No Address'"></div>
                                                    
                                                    <!-- Estimation Info -->
                                                    <template x-if="visit.est_travel_minutes">
                                                        <div class="mt-2 pt-2 border-t border-gray-200 flex items-center gap-2 text-xs text-slate-600">
                                                            <span class="flex items-center gap-1" title="Estimasi Waktu">
                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                                <span x-text="visit.est_travel_minutes + ' mnt'"></span>
                                                            </span>
                                                            <span class="flex items-center gap-1" title="Jarak dari sebelumnya">
                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                                                <span x-text="visit.distance_from_prev + ' km'"></span>
                                                            </span>
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>
                                        </template>
                                    </div>

                                    <!-- Disclaimer -->
                                    <template x-if="routeVisits.length > 0">
                                        <div class="mt-4 text-xs text-gray-500 italic border-t border-gray-100 pt-3">
                                            <p>
                                                <span class="font-semibold">Sumber Data:</span> 
                                                <span x-text="routeVisits[0].route_source || 'Haversine (Estimasi)'"></span>
                                            </p>
                                            <p class="mt-1">
                                                * Waktu tempuh adalah estimasi. Akurasi ±15%. Kondisi lalu lintas aktual mungkin berbeda.
                                            </p>
                                        </div>
                                    </template>
                                </div>

                                <!-- Map -->
                                <div class="flex-1 bg-gray-100 rounded-xl overflow-hidden border border-gray-200 relative">
                                    <div id="route-map" class="w-full h-full z-0"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    @push('scripts')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js'></script>
    <script>
        function calendarApp(initialStatuses) {
            return {
                viewMode: 'calendar', // 'calendar' or 'map'
                search: '',
                selectedStatuses: initialStatuses, // Default select all
                calendar: null,
                mainMap: null,
                mapMarkers: [],
                
                // Route Recommendation State
                showRouteModal: false,
                routeDate: new Date().toISOString().slice(0, 10),
                routeVisits: [],
                isLoadingRoute: false,
                routeError: null,
                routeMap: null,
                routeMarkers: [],
                routePolyline: null,

                init() {
                    var calendarEl = document.getElementById('calendar');
                    
                    this.calendar = new FullCalendar.Calendar(calendarEl, {
                        initialView: 'dayGridMonth',
                        headerToolbar: {
                            left: 'prev,next today',
                            center: 'title',
                            right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
                        },
                        themeSystem: 'standard',
                        height: 'auto',
                        contentHeight: 800,
                        aspectRatio: 1.8,
                        stickyHeaderDates: true,
                        
                        // Events Source
                        events: (info, successCallback, failureCallback) => {
                            // Show loading
                            document.getElementById('calendar-loading').style.display = 'flex';

                            const params = new URLSearchParams({
                                start: info.startStr,
                                end: info.endStr,
                                search: this.search,
                            });
                            
                            // Append status array manually
                            this.selectedStatuses.forEach(status => params.append('status[]', status));

                            fetch(`{{ route('visits.calendar-events') }}?${params.toString()}`)
                                .then(response => response.json())
                                .then(data => {
                                    document.getElementById('calendar-loading').style.display = 'none';
                                    if (data.error) {
                                        failureCallback(data.error);
                                    } else {
                                        successCallback(data);
                                    }
                                })
                                .catch(error => {
                                    document.getElementById('calendar-loading').style.display = 'none';
                                    failureCallback(error);
                                });
                        },
                        
                        // Styling & Interactions
                        eventClassNames: function(arg) {
                            return ['shadow-sm', 'border-0', 'hover:shadow-md', 'transition-all', 'opacity-90', 'hover:opacity-100'];
                        },
                        eventContent: function(arg) {
                            let timeText = arg.timeText;
                            let title = arg.event.title;
                            let status = arg.event.extendedProps.status;
                            
                            // Custom HTML content for event
                            return {
                                html: `
                                    <div class="px-1.5 py-0.5 overflow-hidden">
                                        <div class="text-xs font-bold truncate">${title}</div>
                                        ${arg.view.type !== 'dayGridMonth' ? `<div class="text-[10px] opacity-90 truncate">${status}</div>` : ''}
                                    </div>
                                `
                            };
                        },
                        eventClick: function(info) {
                            if (info.event.url) {
                                info.jsEvent.preventDefault();
                                window.location.href = info.event.url;
                            }
                        },
                        
                        // UI Config
                        buttonText: {
                            today: 'Hari Ini',
                            month: 'Bulan',
                            week: 'Minggu',
                            day: 'Hari',
                            list: 'List'
                        },
                        dayMaxEvents: 3,
                        moreLinkClick: 'popover',
                        nowIndicator: true,
                        navLinks: true,
                    });

                    this.calendar.render();

                    // Watchers for filters
                    this.$watch('search', () => {
                        this.calendar.refetchEvents();
                        if (this.viewMode === 'map') this.fetchMapEvents();
                    });
                    this.$watch('selectedStatuses', () => {
                        this.calendar.refetchEvents();
                        if (this.viewMode === 'map') this.fetchMapEvents();
                    });
                    
                    // Watch modal to handle map size
                    this.$watch('showRouteModal', (value) => {
                        if (value) {
                            this.$nextTick(() => {
                                this.initRouteMap();
                            });
                        }
                    });
                },

                switchView(mode) {
                    this.viewMode = mode;
                    if (mode === 'map') {
                        this.$nextTick(() => {
                            this.initMainMap();
                            this.fetchMapEvents();
                        });
                    } else {
                        this.$nextTick(() => {
                            this.calendar.render(); // Re-render to fix layout issues
                        });
                    }
                },

                initMainMap() {
                    if (!this.mainMap) {
                        // Default to Jakarta
                        this.mainMap = L.map('main-map').setView([-6.200000, 106.816666], 12);
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '&copy; OpenStreetMap contributors'
                        }).addTo(this.mainMap);
                    } else {
                        this.mainMap.invalidateSize();
                    }
                },

                fetchMapEvents() {
                    if (!this.mainMap) return;

                    // Get current range from calendar if available, else default to this month
                    let start, end;
                    if (this.calendar) {
                        const view = this.calendar.view;
                        start = view.activeStart.toISOString();
                        end = view.activeEnd.toISOString();
                    } else {
                        const now = new Date();
                        start = new Date(now.getFullYear(), now.getMonth(), 1).toISOString();
                        end = new Date(now.getFullYear(), now.getMonth() + 1, 0).toISOString();
                    }

                    // Show loading
                    document.getElementById('calendar-loading').style.display = 'flex';

                    const params = new URLSearchParams({
                        start: start,
                        end: end,
                        search: this.search,
                    });
                    
                    this.selectedStatuses.forEach(status => params.append('status[]', status));

                    fetch(`{{ route('visits.calendar-events') }}?${params.toString()}`)
                        .then(response => response.json())
                        .then(data => {
                            document.getElementById('calendar-loading').style.display = 'none';
                            this.renderMapMarkers(data);
                        })
                        .catch(error => {
                            document.getElementById('calendar-loading').style.display = 'none';
                            console.error("Map fetch error", error);
                        });
                },

                renderMapMarkers(events) {
                    // Clear existing
                    this.mapMarkers.forEach(m => this.mainMap.removeLayer(m));
                    this.mapMarkers = [];

                    if (!Array.isArray(events)) return;

                    const bounds = [];

                    events.forEach(event => {
                        const props = event.extendedProps;
                        if (props.latitude && props.longitude) {
                            const latLng = [props.latitude, props.longitude];
                            bounds.push(latLng);

                            // Create custom icon based on status color
                            const color = event.backgroundColor || '#3b82f6';
                            const markerHtml = `<div style="background-color: ${color}; width: 12px; height: 12px; border-radius: 50%; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></div>`;
                            
                            const icon = L.divIcon({
                                className: 'custom-map-marker',
                                html: markerHtml,
                                iconSize: [16, 16],
                                iconAnchor: [8, 8]
                            });

                            const marker = L.marker(latLng, { icon: icon }).addTo(this.mainMap);
                            
                            const popupContent = `
                                <div class="p-1">
                                    <div class="font-bold text-sm">${event.title}</div>
                                    <div class="text-xs text-gray-500 mb-1">${props.status}</div>
                                    <div class="text-xs text-gray-400">${props.address}</div>
                                    <a href="${event.url}" class="block mt-2 text-center bg-birawa-600 text-white text-xs py-1 px-2 rounded hover:bg-birawa-700">Detail</a>
                                </div>
                            `;
                            
                            marker.bindPopup(popupContent);
                            this.mapMarkers.push(marker);
                        }
                    });

                    if (bounds.length > 0) {
                        this.mainMap.fitBounds(bounds, { padding: [50, 50] });
                    }
                },

                openRouteModal() {
                    this.showRouteModal = true;
                },

                closeRouteModal() {
                    this.showRouteModal = false;
                },

                initRouteMap() {
                    if (!this.routeMap) {
                        // Default center (Jakarta) if no location
                        this.routeMap = L.map('route-map').setView([-6.200000, 106.816666], 13);
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '&copy; OpenStreetMap contributors'
                        }).addTo(this.routeMap);
                    } else {
                        this.routeMap.invalidateSize();
                    }
                },

                fetchRoute() {
                    this.isLoadingRoute = true;
                    this.routeError = null;
                    this.routeVisits = [];
                    
                    // Clear map
                    if (this.routeMap) {
                        this.routeMarkers.forEach(m => this.routeMap.removeLayer(m));
                        this.routeMarkers = [];
                        if (this.routePolyline) {
                            this.routeMap.removeLayer(this.routePolyline);
                            this.routePolyline = null;
                        }
                    }

                    fetch(`{{ route('visits.recommend-route') }}?date=${this.routeDate}`)
                        .then(res => res.json())
                        .then(data => {
                            this.isLoadingRoute = false;
                            if (Array.isArray(data)) {
                                this.routeVisits = data;
                                if (data.length > 0) {
                                    this.renderRouteOnMap();
                                } else {
                                    this.routeError = "Tidak ada jadwal kunjungan pada tanggal ini.";
                                }
                            } else {
                                this.routeError = "Gagal memuat rute.";
                            }
                        })
                        .catch(err => {
                            this.isLoadingRoute = false;
                            this.routeError = "Terjadi kesalahan: " + err.message;
                        });
                },

                renderRouteOnMap() {
                    if (!this.routeMap) return;

                    const latLngs = [];
                    
                    this.routeVisits.forEach((visit, index) => {
                        if (visit.latitude && visit.longitude) {
                            const latLng = [visit.latitude, visit.longitude];
                            latLngs.push(latLng);
                            
                            const marker = L.marker(latLng).addTo(this.routeMap);
                            marker.bindPopup(`<b>${index + 1}. ${visit.patient.name}</b><br>${visit.patient.client.address || ''}`);
                            this.routeMarkers.push(marker);
                            
                            // Attach marker to visit object in array (reactive issue might occur but let's try)
                            visit._marker = marker;
                        }
                    });

                    if (latLngs.length > 0) {
                        this.routePolyline = L.polyline(latLngs, {color: '#2563eb', weight: 4, opacity: 0.7}).addTo(this.routeMap);
                        this.routeMap.fitBounds(L.latLngBounds(latLngs).pad(0.1));
                    }
                },

                focusOnMap(visit) {
                    if (visit._marker && this.routeMap) {
                        this.routeMap.flyTo(visit._marker.getLatLng(), 16);
                        visit._marker.openPopup();
                    } else if (visit.latitude && visit.longitude && this.routeMap) {
                         this.routeMap.flyTo([visit.latitude, visit.longitude], 16);
                    }
                }
            }
        }
    </script>
    <style>
        /* Custom Calendar Styling to match Birawa Theme */
        :root {
            --fc-border-color: #e2e8f0;
            --fc-button-bg-color: #fff;
            --fc-button-border-color: #e2e8f0;
            --fc-button-text-color: #475569;
            --fc-button-hover-bg-color: #f8fafc;
            --fc-button-hover-border-color: #cbd5e1;
            --fc-button-active-bg-color: #eff6ff;
            --fc-button-active-border-color: #2563eb;
            --fc-button-active-text-color: #2563eb;
            --fc-today-bg-color: #f0f9ff;
        }

        .fc .fc-toolbar-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1e293b;
        }

        .fc .fc-col-header-cell-cushion {
            padding: 12px 0;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            font-size: 0.75rem;
            text-decoration: none;
        }

        .fc .fc-daygrid-day-number {
            font-weight: 500;
            color: #475569;
            text-decoration: none;
            padding: 8px;
        }

        .fc-theme-standard .fc-scrollgrid {
            border: none;
        }

        .fc-theme-standard td, .fc-theme-standard th {
            border-color: #f1f5f9;
        }

        .fc .fc-button {
            border-radius: 0.75rem;
            font-weight: 600;
            font-size: 0.875rem;
            padding: 0.5rem 1rem;
            box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            transition: all 0.2s;
        }
        
        .fc .fc-button-primary:not(:disabled).fc-button-active, 
        .fc .fc-button-primary:not(:disabled):active {
            background-color: #0f766e;
            border-color: #0f766e;
            color: white;
        }
        
        .fc-day-today {
            background-color: #f0fdfa !important;
        }
    </style>
    @endpush
</x-app-layout>
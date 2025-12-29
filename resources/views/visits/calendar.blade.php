<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold">Jadwal Kunjungan</h2>
                        <div class="flex space-x-2">
                            <a href="{{ route('visits.create') }}" class="bg-birawa-600 hover:bg-birawa-700 text-white font-bold py-2 px-4 rounded">
                                + Buat Jadwal
                            </a>
                            <a href="{{ route('visit-statuses.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-4 rounded">
                                Setting Status
                            </a>
                        </div>
                    </div>

                    <div class="relative">
                        <!-- Loading Overlay -->
                        <div id="calendar-loading" class="absolute inset-0 z-10 bg-white/50 backdrop-blur-sm flex items-center justify-center" style="display: none;">
                            <div class="flex flex-col items-center">
                                <svg class="animate-spin h-10 w-10 text-birawa-600 mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span class="text-birawa-600 font-bold">Memuat jadwal...</span>
                            </div>
                        </div>

                        <!-- Error Container -->
                        <div id="calendar-error" class="absolute inset-0 z-10 bg-white flex items-center justify-center" style="display: none;">
                            <div class="text-center p-6 max-w-sm mx-auto">
                                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4 text-red-500">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                </div>
                                <h3 class="text-lg font-bold text-gray-900 mb-2">Gagal Memuat Jadwal</h3>
                                <p class="text-gray-500 mb-6">Terjadi kesalahan saat mengambil data jadwal. Silakan coba lagi.</p>
                                <button onclick="window.location.reload()" class="bg-birawa-600 hover:bg-birawa-700 text-white font-bold py-2 px-6 rounded-lg transition-colors">
                                    Muat Ulang Halaman
                                </button>
                            </div>
                        </div>

                        <div id="calendar"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js'></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                events: {
                    url: '{{ route("visits.calendar-events") }}',
                    failure: function() {
                        document.getElementById('calendar-error').style.display = 'flex';
                        document.getElementById('calendar-loading').style.display = 'none';
                    }
                },
                loading: function(isLoading) {
                    if (isLoading) {
                        // Show loading spinner
                        document.getElementById('calendar-loading').style.display = 'flex';
                    } else {
                        // Hide loading spinner
                        document.getElementById('calendar-loading').style.display = 'none';
                    }
                },
                eventClick: function(info) {
                    if (info.event.url) {
                        window.location.href = info.event.url;
                        info.jsEvent.preventDefault();
                    }
                },
                height: 800,
                navLinks: true, // can click day/week names to navigate views
                editable: false,
                dayMaxEvents: true, // allow "more" link when too many events
                noEventsContent: 'Tidak ada jadwal kunjungan.'
            });
            calendar.render();
        });
    </script>
    @endpush
    
    @push('styles')
    <style>
        /* Custom FullCalendar overrides if needed */
        .fc-event {
            cursor: pointer;
        }
    </style>
    @endpush
</x-app-layout>

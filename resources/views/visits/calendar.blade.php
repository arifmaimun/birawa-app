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

                    <div id="calendar"></div>
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
                events: '{{ route("visits.calendar-events") }}',
                eventClick: function(info) {
                    if (info.event.url) {
                        window.location.href = info.event.url;
                        info.jsEvent.preventDefault();
                    }
                },
                height: 800,
                navLinks: true, // can click day/week names to navigate views
                editable: false,
                dayMaxEvents: true // allow "more" link when too many events
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

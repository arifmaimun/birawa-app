<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Calendar Schedule') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- FullCalendar JS -->
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
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
                events: '{{ route("visits.calendar-events") }}', // API Endpoint
                eventClick: function(info) {
                    if (info.event.url) {
                        window.location.href = info.event.url;
                        info.jsEvent.preventDefault(); // Don't follow default link
                    }
                },
                height: 'auto',
                aspectRatio: 1.5,
                themeSystem: 'standard'
            });
            calendar.render();
        });
    </script>

    <!-- Custom CSS to match Medical Theme -->
    <style>
        :root {
            --fc-border-color: #e5e7eb;
            --fc-button-text-color: #fff;
            --fc-button-bg-color: #0d9488;
            --fc-button-border-color: #0d9488;
            --fc-button-hover-bg-color: #0f766e;
            --fc-button-hover-border-color: #0f766e;
            --fc-button-active-bg-color: #115e59;
            --fc-button-active-border-color: #115e59;
            --fc-today-bg-color: #f0fdfa;
        }
        .fc-event {
            cursor: pointer;
            border-radius: 4px;
            padding: 2px 4px;
            font-size: 0.875rem;
        }
    </style>
</x-app-layout>

<x-app-layout>
    <div x-data="{ view: localStorage.getItem('visitView') || 'list' }" x-init="$watch('view', value => localStorage.setItem('visitView', value))">
        <!-- Header & Search -->
        <div class="mb-4 space-y-3">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold text-slate-800">Visits</h1>
                <a href="{{ route('visits.create') }}" class="bg-birawa-600 hover:bg-birawa-700 text-white p-2 rounded-xl shadow-birawa-sm flex items-center gap-2 px-4 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    <span class="text-sm font-semibold">New</span>
                </a>
            </div>

            <!-- Search & Toggle -->
            <div class="flex gap-2">
                <form action="{{ route('visits.index') }}" method="GET" class="flex-1 relative">
                    <svg class="w-5 h-5 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Search patient or owner..." class="w-full pl-10 pr-4 py-2.5 rounded-xl border-slate-200 focus:border-birawa-500 focus:ring-birawa-500 text-sm shadow-sm bg-white">
                </form>
                
                <div class="bg-white rounded-xl border border-slate-200 p-1 flex shadow-sm shrink-0">
                    <button @click="view = 'list'" :class="{ 'bg-birawa-50 text-birawa-600': view === 'list', 'text-slate-400': view !== 'list' }" class="p-2 rounded-lg transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                    <button @click="view = 'calendar'; setTimeout(() => { window.calendar && window.calendar.render() }, 100)" :class="{ 'bg-birawa-50 text-birawa-600': view === 'calendar', 'text-slate-400': view !== 'calendar' }" class="p-2 rounded-lg transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- List View -->
        <div x-show="view === 'list'" class="space-y-4">
            @forelse ($visits as $visit)
                <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-100 flex flex-col gap-3">
                    <!-- Top Row: Time & Status -->
                    <div class="flex justify-between items-start">
                        <div class="flex items-center gap-3">
                             <div class="flex flex-col items-center bg-slate-50 rounded-xl p-2 border border-slate-100 min-w-[3.5rem]">
                                <span class="text-xs font-bold text-slate-500 uppercase">{{ \Carbon\Carbon::parse($visit->scheduled_at)->format('M') }}</span>
                                <span class="text-xl font-bold text-slate-800">{{ \Carbon\Carbon::parse($visit->scheduled_at)->format('d') }}</span>
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-800 text-lg leading-tight">{{ $visit->patient->name }}</h3>
                                <p class="text-sm text-slate-500">{{ $visit->patient->owners->first()->name ?? 'No Owner' }}</p>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wide 
                            {{ $visit->status === 'completed' ? 'bg-green-50 text-green-600 border border-green-100' : 
                               ($visit->status === 'cancelled' ? 'bg-red-50 text-red-600 border border-red-100' : 
                               ($visit->status === 'in_progress' ? 'bg-blue-50 text-blue-600 border border-blue-100' : 
                               'bg-slate-100 text-slate-600 border border-slate-200')) }}">
                            {{ str_replace('_', ' ', $visit->status) }}
                        </span>
                    </div>

                    <!-- Info Row -->
                    <div class="flex items-center gap-4 text-xs text-slate-500">
                         <div class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span>{{ \Carbon\Carbon::parse($visit->scheduled_at)->format('H:i') }}</span>
                        </div>
                        @if($visit->complaint)
                        <div class="flex items-center gap-1.5">
                             <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                             <span class="truncate max-w-[150px]">{{ $visit->complaint }}</span>
                        </div>
                        @endif
                    </div>

                    <!-- Actions -->
                    <div class="pt-3 border-t border-slate-50 flex items-center justify-between">
                        <div class="flex gap-2">
                             @if($visit->status !== 'completed' && $visit->status !== 'cancelled')
                                <form action="{{ route('visits.update-status', $visit) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status" value="otw">
                                    <button type="submit" class="text-xs font-semibold text-blue-600 hover:text-blue-700 disabled:opacity-50" {{ $visit->status === 'otw' ? 'disabled' : '' }}>OTW</button>
                                </form>
                                <span class="text-slate-200">|</span>
                                <form action="{{ route('visits.update-status', $visit) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status" value="arrived">
                                    <button type="submit" class="text-xs font-semibold text-purple-600 hover:text-purple-700 disabled:opacity-50" {{ $visit->status === 'arrived' ? 'disabled' : '' }}>Arrived</button>
                                </form>
                            @endif
                        </div>
                        <a href="{{ route('visits.show', $visit) }}" class="text-xs font-bold text-slate-600 flex items-center gap-1 hover:text-birawa-600">
                            Details
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </a>
                    </div>
                </div>
            @empty
                <div class="text-center py-10">
                    <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-3 text-slate-300">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <p class="text-slate-500 font-medium">No visits found.</p>
                </div>
            @endforelse

            <!-- Pagination -->
            <div class="mt-4">
                {{ $visits->links() }}
            </div>
        </div>

        <!-- Calendar View -->
        <div x-show="view === 'calendar'" style="display: none;">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4">
                <div id="calendar" class="min-h-[400px]"></div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            window.calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next',
                    center: 'title',
                    right: 'dayGridMonth,timeGridDay'
                },
                buttonText: {
                    today: 'Today',
                    month: 'Month',
                    week: 'Week',
                    day: 'Day',
                    list: 'List'
                },
                height: 'auto',
                events: '{{ route("visits.calendar-events") }}',
                eventClick: function(info) {
                    if (info.event.url) {
                        window.location.href = info.event.url;
                        return false;
                    }
                },
                eventContent: function(arg) {
                    return {
                        html: '<div class="fc-content p-1 truncate text-xs font-semibold">' + arg.event.title + '</div>'
                    };
                }
            });
            
            // Re-render calendar when switching tabs to ensure proper sizing
            // This is handled by the x-on:click in the button
        });
    </script>
    <style>
        .fc-toolbar-title { font-size: 1rem !important; font-weight: 700; }
        .fc-button { background-color: #0f766e !important; border-color: #0f766e !important; font-size: 0.75rem !important; padding: 0.25rem 0.5rem !important; }
        .fc-button:hover { background-color: #0d9488 !important; border-color: #0d9488 !important; }
        .fc-button-active { background-color: #115e59 !important; border-color: #115e59 !important; }
        .fc-day-today { background-color: #f0fdfa !important; }
    </style>
    @endpush
</x-app-layout>

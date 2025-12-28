<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-slate-800">Shift Management</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Add New Shift -->
            <div class="md:col-span-1">
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-full bg-birawa-100 flex items-center justify-center text-birawa-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-slate-800">Add New Shift</h3>
                    </div>
                    
                    <form action="{{ route('shifts.store') }}" method="POST">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label class="block text-slate-700 text-sm font-bold mb-2">Day of Week</label>
                                <div class="relative">
                                    <select name="day_of_week" class="w-full rounded-xl border-slate-200 shadow-sm focus:border-birawa-500 focus:ring-birawa-500 transition-colors appearance-none py-2.5 pl-4 pr-10" required>
                                        @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)
                                            <option value="{{ $day }}">{{ $day }}</option>
                                        @endforeach
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-slate-700 text-sm font-bold mb-2">Start Time</label>
                                <input type="time" name="start_time" class="w-full rounded-xl border-slate-200 shadow-sm focus:border-birawa-500 focus:ring-birawa-500 transition-colors" required>
                            </div>
                            
                            <div>
                                <label class="block text-slate-700 text-sm font-bold mb-2">End Time</label>
                                <input type="time" name="end_time" class="w-full rounded-xl border-slate-200 shadow-sm focus:border-birawa-500 focus:ring-birawa-500 transition-colors" required>
                            </div>
                            
                            <button type="submit" class="w-full bg-birawa-500 text-white font-bold py-3 px-4 rounded-xl hover:bg-birawa-600 transition-colors shadow-sm flex items-center justify-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                Add Shift
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Existing Shifts -->
            <div class="md:col-span-2">
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="p-6 border-b border-slate-100 flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-slate-800">Current Schedule</h3>
                        </div>
                    </div>
                    
                    <div class="p-6">
                        @if($shifts->isEmpty())
                            <div class="text-center py-12">
                                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-100 text-slate-400 mb-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <h3 class="text-lg font-medium text-slate-900">No shifts configured</h3>
                                <p class="mt-1 text-slate-500">Add a shift using the form to get started.</p>
                            </div>
                        @else
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                @foreach($shifts as $shift)
                                    <div class="bg-slate-50 rounded-xl p-4 border border-slate-200 flex justify-between items-center hover:shadow-md transition-shadow">
                                        <div>
                                            <div class="font-bold text-slate-800">{{ $shift->day_of_week }}</div>
                                            <div class="text-slate-600 flex items-center gap-1 text-sm mt-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                {{ Carbon\Carbon::parse($shift->start_time)->format('H:i') }} - {{ Carbon\Carbon::parse($shift->end_time)->format('H:i') }}
                                            </div>
                                        </div>
                                        <form action="{{ route('shifts.destroy', $shift) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this shift?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-colors" title="Delete Shift">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

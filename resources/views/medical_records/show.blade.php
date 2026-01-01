<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-6 md:p-8">
                <!-- Header -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 border-b border-slate-100 pb-6 gap-4">
                    <div>
                        <div class="flex items-center gap-3 mb-1">
                            <h1 class="text-2xl font-bold text-slate-800">Medical Record Details</h1>
                            <span class="px-3 py-1 bg-emerald-50 text-emerald-700 border border-emerald-100 rounded-full text-xs font-bold uppercase tracking-wide flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                Finalized
                            </span>
                        </div>
                        <p class="text-sm text-slate-500 flex items-center gap-2">
                            <span class="font-bold text-slate-700">{{ $medicalRecord->patient->name }}</span>
                            <span class="text-slate-300">•</span>
                            <span>Recorded by {{ $medicalRecord->doctor->name }}</span>
                            <span class="text-slate-300">•</span>
                            <span>{{ $medicalRecord->created_at->format('d M Y, H:i') }}</span>
                        </p>
                    </div>
                    <a href="{{ route('visits.index') }}" class="px-4 py-2 bg-slate-100 text-slate-600 text-sm font-bold rounded-xl hover:bg-slate-200 transition-colors">
                        &larr; Back to Visits
                    </a>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 md:gap-12">
                    <!-- Left Column: SOAP -->
                    <div class="space-y-8">
                        <div>
                            <h4 class="flex items-center gap-2 text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">
                                <span class="w-6 h-6 rounded-full bg-birawa-100 text-birawa-600 flex items-center justify-center text-[10px]">S</span>
                                Subjective
                            </h4>
                            <div class="bg-slate-50 p-5 rounded-2xl border border-slate-100 text-slate-700 leading-relaxed text-sm">
                                {{ $medicalRecord->subjective }}
                            </div>
                        </div>
                        <div>
                            <h4 class="flex items-center gap-2 text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">
                                <span class="w-6 h-6 rounded-full bg-birawa-100 text-birawa-600 flex items-center justify-center text-xs">O</span>
                                Objective
                            </h4>
                            <div class="bg-slate-50 p-5 rounded-2xl border border-slate-100 text-slate-700 leading-relaxed text-sm">
                                {{ $medicalRecord->objective }}
                            </div>
                        </div>
                        <div>
                            <h4 class="flex items-center gap-2 text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">
                                <span class="w-6 h-6 rounded-full bg-birawa-100 text-birawa-600 flex items-center justify-center text-xs">A</span>
                                Assessment
                            </h4>
                            <div class="bg-slate-50 p-5 rounded-2xl border border-slate-100 text-slate-700 leading-relaxed text-sm">
                                {{ $medicalRecord->assessment }}
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Plan & Usage -->
                    <div class="space-y-8">
                        <div>
                            <h4 class="flex items-center gap-2 text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">
                                <span class="w-6 h-6 rounded-full bg-birawa-100 text-birawa-600 flex items-center justify-center text-xs">P</span>
                                Plan (Treatment)
                            </h4>
                            <div class="bg-slate-50 p-5 rounded-2xl border border-slate-100 text-slate-700 leading-relaxed text-sm">
                                {{ $medicalRecord->plan_treatment }}
                            </div>
                        </div>
                        
                        <!-- Internal Note -->
                        <div class="bg-amber-50 rounded-2xl p-5 border border-amber-100">
                            <h4 class="flex items-center gap-2 text-xs font-bold text-amber-600 uppercase tracking-wider mb-3">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                                Internal Plan / Recipe
                            </h4>
                            <p class="text-sm text-amber-900 italic leading-relaxed">
                                {{ $medicalRecord->plan_recipe ?? 'No internal notes.' }}
                            </p>
                        </div>

                        <div>
                            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Medical Usage Log</h4>
                            <div class="bg-white border border-slate-100 rounded-2xl overflow-hidden shadow-sm">
                                <ul class="divide-y divide-slate-50">
                                    @forelse($medicalRecord->usageLogs as $log)
                                        <li class="px-4 py-3 flex justify-between items-center text-sm">
                                            <span class="font-medium text-slate-700">{{ $log->inventoryItem->item_name ?? 'Unknown Item' }}</span>
                                            <span class="bg-slate-100 text-slate-600 px-2 py-1 rounded-lg text-xs font-bold">
                                                {{ $log->quantity_used }} {{ $log->inventoryItem->unit ?? '' }}
                                            </span>
                                        </li>
                                    @empty
                                        <li class="px-4 py-6 text-center text-slate-400 text-sm italic">No items used during this visit.</li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

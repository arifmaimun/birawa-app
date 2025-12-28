<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Medical Record Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-start mb-6 border-b pb-4">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900">Patient: {{ $medicalRecord->patient->name }}</h3>
                            <p class="text-sm text-gray-600">Recorded by: {{ $medicalRecord->doctor->name }} on {{ $medicalRecord->created_at->format('d M Y, H:i') }}</p>
                        </div>
                        <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">Finalized</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                        <!-- Left Column: SOAP -->
                        <div class="space-y-6">
                            <div>
                                <h4 class="text-sm font-bold text-gray-500 uppercase tracking-wider">Subjective</h4>
                                <p class="mt-1 text-gray-900 bg-gray-50 p-3 rounded">{{ $medicalRecord->subjective }}</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-gray-500 uppercase tracking-wider">Objective</h4>
                                <p class="mt-1 text-gray-900 bg-gray-50 p-3 rounded">{{ $medicalRecord->objective }}</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-gray-500 uppercase tracking-wider">Assessment</h4>
                                <p class="mt-1 text-gray-900 bg-gray-50 p-3 rounded">{{ $medicalRecord->assessment }}</p>
                            </div>
                        </div>

                        <!-- Right Column: Plan & Usage -->
                        <div class="space-y-6">
                            <div>
                                <h4 class="text-sm font-bold text-gray-500 uppercase tracking-wider">Plan (Treatment)</h4>
                                <p class="mt-1 text-gray-900 bg-gray-50 p-3 rounded">{{ $medicalRecord->plan_treatment }}</p>
                            </div>
                            
                            <!-- Internal Note: Only visible if it's the creator or authorized peer -->
                            <div class="border-l-4 border-yellow-400 pl-4">
                                <h4 class="text-sm font-bold text-yellow-600 uppercase tracking-wider">Internal Plan / Recipe (Hidden from Client)</h4>
                                <p class="mt-1 text-gray-700 italic">{{ $medicalRecord->plan_recipe ?? 'No internal notes.' }}</p>
                            </div>

                            <div class="mt-8">
                                <h4 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-2">Medical Usage Log</h4>
                                <ul class="list-disc list-inside text-sm text-gray-700 bg-gray-50 p-3 rounded">
                                    @forelse($medicalRecord->usageLogs as $log)
                                        <li>{{ $log->inventoryItem->item_name ?? 'Unknown Item' }} - {{ $log->quantity_used }} {{ $log->inventoryItem->unit ?? '' }}</li>
                                    @empty
                                        <li class="italic text-gray-500">No items used.</li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end mt-6">
                        <a href="{{ route('visits.index') }}" class="text-gray-600 hover:text-gray-900 font-medium">Back to Visits</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

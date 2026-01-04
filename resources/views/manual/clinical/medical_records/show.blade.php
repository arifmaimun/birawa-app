<x-manual.layouts.app>
    <x-slot name="header">
        View Medical Record
    </x-slot>

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h2 class="text-xl font-bold text-gray-800">{{ $medicalRecord->patient->name }}</h2>
                        <p class="text-sm text-gray-600">
                            {{ $medicalRecord->patient->species }} - {{ $medicalRecord->patient->gender }} 
                            ({{ $medicalRecord->patient->client->name ?? 'No Owner' }})
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-500">Date: {{ $medicalRecord->created_at->format('d M Y H:i') }}</p>
                        <p class="text-sm text-gray-500">Doctor: {{ $medicalRecord->doctor->name ?? 'Unknown' }}</p>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $medicalRecord->is_locked ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                            {{ $medicalRecord->is_locked ? 'Locked' : 'Editable' }}
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="font-bold text-gray-700 mb-2">Subjective (Keluhan)</h3>
                        <p class="whitespace-pre-line text-gray-800">{{ $medicalRecord->subjective }}</p>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="font-bold text-gray-700 mb-2">Objective (Pemeriksaan)</h3>
                        <p class="whitespace-pre-line text-gray-800">{{ $medicalRecord->objective ?? '-' }}</p>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="font-bold text-gray-700 mb-2">Assessment (Diagnosa)</h3>
                        <p class="whitespace-pre-line text-gray-800">{{ $medicalRecord->assessment ?? '-' }}</p>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="font-bold text-gray-700 mb-2">Plan (Tindakan & Resep)</h3>
                        <div class="mb-2">
                            <span class="font-semibold text-xs text-gray-500 uppercase">Treatment</span>
                            <p class="whitespace-pre-line text-gray-800">{{ $medicalRecord->plan_treatment ?? '-' }}</p>
                        </div>
                        <div>
                            <span class="font-semibold text-xs text-gray-500 uppercase">Recipe</span>
                            <p class="whitespace-pre-line text-gray-800">{{ $medicalRecord->plan_recipe ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex justify-end">
                    <a href="{{ route('manual.medical-records.index') }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 mr-2">
                        Back to List
                    </a>
                    @if(!$medicalRecord->is_locked)
                        <a href="{{ route('manual.medical-records.edit', $medicalRecord) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                            Edit Record
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-manual.layouts.app>

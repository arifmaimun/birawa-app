<x-filament-panels::page>
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Left Sidebar: Patient History -->
        <div class="lg:col-span-1 space-y-6">
            <x-filament::section>
                <x-slot name="heading">
                    Patient Profile
                </x-slot>
                
                <div class="space-y-2">
                    <div class="font-bold text-lg">{{ $record->patient->name }}</div>
                    <div class="text-sm text-gray-500">{{ $record->patient->breed }} - {{ $record->patient->gender }}</div>
                    <div class="text-sm text-gray-500">Owner: {{ $record->patient->client?->name ?? '-' }}</div>
                </div>
            </x-filament::section>

            <x-filament::section>
                <x-slot name="heading">
                    Medical History
                </x-slot>
                
                <div class="relative border-l border-gray-200 dark:border-gray-700 ml-3 space-y-6">
                    @php
                        $histories = \App\Models\MedicalRecord::where('patient_id', $record->patient_id)
                            ->latest()
                            ->limit(5)
                            ->get();
                    @endphp

                    @foreach($histories as $history)
                    <div class="mb-6 ml-6">
                        <span class="absolute flex items-center justify-center w-6 h-6 bg-blue-100 rounded-full -left-3 ring-8 ring-white dark:ring-gray-900 dark:bg-blue-900">
                            <svg class="w-2.5 h-2.5 text-blue-800 dark:text-blue-300" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/>
                            </svg>
                        </span>
                        <h3 class="flex items-center mb-1 text-lg font-semibold text-gray-900 dark:text-white">
                            {{ $history->created_at->format('d M Y') }}
                        </h3>
                        <div class="mb-2 text-sm font-normal text-gray-500 dark:text-gray-400">
                            Dr. {{ $history->doctor->name ?? '-' }}
                        </div>
                        <p class="mb-2 text-base font-normal text-gray-500 dark:text-gray-400">
                            {{ Str::limit($history->assessment, 100) }}
                        </p>
                    </div>
                    @endforeach
                </div>
            </x-filament::section>
        </div>

        <!-- Main Content: Consultation Form -->
        <div class="lg:col-span-3">
            <x-filament-panels::form wire:submit="save">
                {{ $this->form }}

                <div class="flex justify-end mt-4">
                    <x-filament::button type="submit" size="lg">
                        Finish Consultation & Create Invoice
                    </x-filament::button>
                </div>
            </x-filament-panels::form>
        </div>
    </div>
</x-filament-panels::page>

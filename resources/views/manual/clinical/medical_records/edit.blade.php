<x-manual.layouts.app>
    <x-slot name="header">
        Edit Medical Record
    </x-slot>

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <form method="POST" action="{{ route('manual.medical-records.update', $medicalRecord) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 gap-6">
                        <!-- Patient Info (Read Only) -->
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <h3 class="font-bold">{{ $medicalRecord->patient->name }}</h3>
                            <p class="text-sm text-gray-600">{{ $medicalRecord->patient->species }} - {{ $medicalRecord->patient->client->name ?? '' }}</p>
                        </div>

                        <!-- SOAP -->
                        <div class="border-t border-gray-200 pt-4 mt-2">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">SOAP Notes</h3>
                            
                            <!-- Subjective -->
                            <div class="mb-4">
                                <x-manual.input-label for="subjective" value="Subjective (Keluhan/Anamnesa)" />
                                <textarea id="subjective" name="subjective" rows="3" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>{{ old('subjective', $medicalRecord->subjective) }}</textarea>
                                <x-manual.input-error :messages="$errors->get('subjective')" class="mt-2" />
                            </div>

                            <!-- Objective -->
                            <div class="mb-4">
                                <x-manual.input-label for="objective" value="Objective (Pemeriksaan Fisik)" />
                                <textarea id="objective" name="objective" rows="3" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('objective', $medicalRecord->objective) }}</textarea>
                                <x-manual.input-error :messages="$errors->get('objective')" class="mt-2" />
                            </div>

                            <!-- Assessment -->
                            <div class="mb-4">
                                <x-manual.input-label for="assessment" value="Assessment (Diagnosa Sementara)" />
                                <textarea id="assessment" name="assessment" rows="2" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('assessment', $medicalRecord->assessment) }}</textarea>
                                <x-manual.input-error :messages="$errors->get('assessment')" class="mt-2" />
                            </div>

                            <!-- Plan Treatment -->
                            <div class="mb-4">
                                <x-manual.input-label for="plan_treatment" value="Plan (Tindakan)" />
                                <textarea id="plan_treatment" name="plan_treatment" rows="3" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('plan_treatment', $medicalRecord->plan_treatment) }}</textarea>
                                <x-manual.input-error :messages="$errors->get('plan_treatment')" class="mt-2" />
                            </div>

                             <!-- Plan Recipe -->
                             <div class="mb-4">
                                <x-manual.input-label for="plan_recipe" value="Recipe (Resep Obat)" />
                                <textarea id="plan_recipe" name="plan_recipe" rows="3" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('plan_recipe', $medicalRecord->plan_recipe) }}</textarea>
                                <x-manual.input-error :messages="$errors->get('plan_recipe')" class="mt-2" />
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-6">
                        <a href="{{ route('manual.medical-records.show', $medicalRecord) }}" class="text-gray-600 hover:text-gray-900 mr-4">Cancel</a>
                        <x-manual.primary-button class="ml-4">
                            {{ __('Update Record') }}
                        </x-manual.primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-manual.layouts.app>

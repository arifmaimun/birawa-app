<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Medical Record') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Medical History Section -->
            @if($medicalHistory->count() > 0)
            <div class="mb-8 bg-blue-50 overflow-hidden shadow-sm sm:rounded-lg border border-blue-100">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-blue-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Medical History (Last 5 Visits)
                    </h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-blue-200">
                            <thead class="bg-blue-100">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-blue-800 uppercase tracking-wider">Date</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-blue-800 uppercase tracking-wider">Doctor</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-blue-800 uppercase tracking-wider">Diagnosis/Assessment</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-blue-800 uppercase tracking-wider">Treatment</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-blue-100">
                                @foreach($medicalHistory as $history)
                                    <tr class="hover:bg-blue-50 transition-colors">
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">
                                            {{ $history->created_at->format('d M Y') }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">
                                            {{ $history->doctor->name ?? 'Unknown' }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-700">
                                            @if($history->diagnoses->count() > 0)
                                                <div class="flex flex-wrap gap-1">
                                                    @foreach($history->diagnoses as $diag)
                                                        <span class="px-2 py-0.5 rounded text-xs font-semibold bg-indigo-100 text-indigo-700">
                                                            {{ $diag->name }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @else
                                                {{ Str::limit($history->assessment, 50) }}
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-700">
                                            {{ Str::limit($history->plan_treatment, 50) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900">Patient: {{ $visit->patient->name }}</h3>
                        <p class="text-sm text-gray-600">Owner: {{ $visit->patient->owners->first()->name ?? 'N/A' }}</p>
                        <p class="text-sm text-gray-600">Complaint: {{ $visit->complaint }}</p>
                    </div>

                    <form action="{{ route('medical-records.store', $visit) }}" method="POST">
                        @csrf
                        
                        <!-- SOAP Section -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="subjective" class="block text-sm font-medium text-gray-700">Subjective (Keluhan Pemilik)</label>
                                <textarea name="subjective" id="subjective" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-birawa-500 focus:ring-birawa-500" required>{{ old('subjective', $visit->complaint) }}</textarea>
                            </div>
                            <div>
                                <label for="objective" class="block text-sm font-medium text-gray-700">Objective (Temuan Klinis)</label>
                                <textarea name="objective" id="objective" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-birawa-500 focus:ring-birawa-500" required>{{ old('objective') }}</textarea>
                            </div>
                            
                            <div class="col-span-2">
                                <label for="diagnoses" class="block text-sm font-medium text-gray-700">Smart Diagnosis (Pilih satu atau lebih)</label>
                                <select name="diagnoses[]" id="diagnoses" multiple class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-birawa-500 focus:ring-birawa-500 h-32">
                                    @foreach($diagnoses->groupBy('category') as $category => $items)
                                        <optgroup label="{{ $category ?? 'Other' }}">
                                            @foreach($items as $diagnosis)
                                                <option value="{{ $diagnosis->id }}" {{ in_array($diagnosis->id, old('diagnoses', [])) ? 'selected' : '' }}>
                                                    {{ $diagnosis->code }} - {{ $diagnosis->name }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                                <p class="text-xs text-gray-500 mt-1">Hold Ctrl (Windows) or Cmd (Mac) to select multiple.</p>
                            </div>

                            <div class="col-span-2">
                                <label for="assessment" class="block text-sm font-medium text-gray-700">Additional Assessment Notes (Optional)</label>
                                <textarea name="assessment" id="assessment" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-birawa-500 focus:ring-birawa-500" placeholder="Tambahkan catatan diagnosa manual jika tidak ada di list...">{{ old('assessment') }}</textarea>
                            </div>

                            <div>
                                <label for="plan_treatment" class="block text-sm font-medium text-gray-700">Plan (Treatment & Advice - Visible to Client)</label>
                                <textarea name="plan_treatment" id="plan_treatment" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-birawa-500 focus:ring-birawa-500" required>{{ old('plan_treatment') }}</textarea>
                            </div>
                            <div class="col-span-2">
                                <label for="plan_recipe" class="block text-sm font-medium text-gray-700">Plan Recipe (Internal Notes / Hidden)</label>
                                <textarea name="plan_recipe" id="plan_recipe" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-birawa-500 focus:ring-birawa-500">{{ old('plan_recipe') }}</textarea>
                            </div>
                        </div>

                        <!-- Inventory Usage Section -->
                        <div class="mb-6 border-t pt-4">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Medication & Consumables Used</h3>
                            <div class="space-y-4" id="inventory-list">
                                @if($inventories->count() > 0)
                                    <div class="grid grid-cols-12 gap-4 items-center">
                                        <div class="col-span-8 text-sm font-medium text-gray-700">Item Name (Stock)</div>
                                        <div class="col-span-4 text-sm font-medium text-gray-700">Quantity Used</div>
                                    </div>
                                    @foreach($inventories as $index => $item)
                                        <div class="grid grid-cols-12 gap-4 items-center">
                                            <div class="col-span-8">
                                                <label class="inline-flex items-center">
                                                    <span class="ml-2 text-gray-700">{{ $item->item_name }} ({{ $item->stock_qty }} {{ $item->base_unit ?? $item->unit }})</span>
                                                </label>
                                                <input type="hidden" name="inventory_items[{{ $index }}][id]" value="{{ $item->id }}">
                                            </div>
                                            <div class="col-span-4">
                                                <input type="number" name="inventory_items[{{ $index }}][qty]" min="0" max="{{ $item->stock_qty }}" step="0.01" value="0" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-birawa-500 focus:ring-birawa-500 text-sm">
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <p class="text-sm text-gray-500 italic">No inventory items found. Please add items to your personal inventory first.</p>
                                @endif
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="bg-birawa-600 hover:bg-birawa-700 text-white font-bold py-2 px-4 rounded shadow-lg">
                                Save Medical Record & Complete Visit
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<x-filament-panels::page>
    <div class="space-y-6">
        <div class="p-6 bg-white rounded-lg shadow dark:bg-gray-800">
            <h2 class="text-lg font-bold mb-4">Test History</h2>
            
            @if(count($testRuns) > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th class="px-6 py-3">Batch ID</th>
                                <th class="px-6 py-3">Date</th>
                                <th class="px-6 py-3">Records Created</th>
                                <th class="px-6 py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($testRuns as $run)
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                    <td class="px-6 py-4 font-medium text-gray-900 dark:text-white whitespace-nowrap">
                                        {{ $run['batch_id'] }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ \Carbon\Carbon::parse($run['timestamp'])->format('M d, Y H:i:s') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ $run['count'] }}
                                    </td>
                                    <td class="px-6 py-4 space-x-2">
                                        <a href="{{ $run['report_url'] }}" target="_blank" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">View Report</a>
                                        
                                        <button wire:click="cleanData('{{ $run['file'] }}')" 
                                                wire:confirm="Are you sure you want to delete this test data?"
                                                class="font-medium text-red-600 dark:text-red-500 hover:underline ml-4">
                                            Clean Data
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-500">No test runs found.</p>
            @endif
        </div>
    </div>
</x-filament-panels::page>

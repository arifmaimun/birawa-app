<x-manual.layouts.app>
    <x-slot name="header">
        Patients
    </x-slot>

    <div class="mb-6 flex justify-between items-center">
        <div class="flex-1 max-w-lg">
            <form method="GET" action="{{ route('manual.patients.index') }}">
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" 
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" 
                           placeholder="Search patients by name or owner...">
                    <button type="submit" class="absolute inset-y-0 right-0 px-4 py-2 bg-indigo-600 text-white rounded-r-md hover:bg-indigo-700">
                        Search
                    </button>
                </div>
            </form>
        </div>
        <a href="{{ route('manual.patients.create') }}" class="ml-4 px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
            + New Patient
        </a>
    </div>

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Patient Info
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Owner
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Species/Breed
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Age/Gender
                        </th>
                        <th scope="col" class="relative px-6 py-3">
                            <span class="sr-only">Actions</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($patients as $patient)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $patient->name }}</div>
                            @if($patient->allergies)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Allergy</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $patient->client->name ?? 'Unknown' }}</div>
                            <div class="text-xs text-gray-500">{{ $patient->client->phone ?? '' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $patient->species }}</div>
                            <div class="text-xs text-gray-500">{{ $patient->breed }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $patient->gender }}
                            @if($patient->dob)
                                <br><span class="text-xs">{{ $patient->dob->age }} yrs</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('manual.patients.edit', $patient) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $patients->links() }}
        </div>
    </div>
</x-manual.layouts.app>

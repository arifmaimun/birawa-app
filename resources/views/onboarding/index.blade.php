<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Client Onboarding') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-md mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Register or Link Client</h3>
                    <p class="text-sm text-gray-500 mb-6">Enter the client's phone number to check if they are already registered. If not, a new account will be created and login details sent via WhatsApp.</p>

                    <form action="{{ route('onboarding.check') }}" method="POST">
                        @csrf
                        
                        <div class="mb-4">
                            <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number (WhatsApp)</label>
                            <input type="text" name="phone" id="phone" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-birawa-500 focus:ring-birawa-500" placeholder="e.g. 08123456789" required>
                        </div>

                        <div class="mb-6">
                            <label for="patient_id" class="block text-sm font-medium text-gray-700">Link to Patient (Optional context)</label>
                            <select name="patient_id" id="patient_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-birawa-500 focus:ring-birawa-500" required>
                                <option value="">Select Patient</option>
                                @foreach(\App\Models\Patient::all() as $patient)
                                    <option value="{{ $patient->id }}">{{ $patient->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="bg-birawa-600 hover:bg-birawa-700 text-white font-bold py-2 px-4 rounded shadow-lg flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                                Process & WhatsApp
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

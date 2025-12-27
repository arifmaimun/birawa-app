<x-app-layout>
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Edit Kunjungan</h1>
    </div>

    <div class="bg-white rounded-lg shadow p-6 max-w-2xl">
        <form action="{{ route('visits.update', $visit) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="mb-4">
                <label for="patient_id" class="block text-gray-700 text-sm font-bold mb-2">Pasien</label>
                <select name="patient_id" id="patient_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('patient_id') border-red-500 @enderror" required>
                    <option value="">Pilih Pasien</option>
                    @foreach($patients as $patient)
                        <option value="{{ $patient->id }}" {{ (old('patient_id', $visit->patient_id) == $patient->id) ? 'selected' : '' }}>
                            {{ $patient->name }} ({{ $patient->species }}) - Pemilik: {{ $patient->owner->name }}
                        </option>
                    @endforeach
                </select>
                @error('patient_id')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="mb-4">
                    <label for="scheduled_at" class="block text-gray-700 text-sm font-bold mb-2">Waktu Kunjungan</label>
                    <input type="datetime-local" name="scheduled_at" id="scheduled_at" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('scheduled_at') border-red-500 @enderror" value="{{ old('scheduled_at', \Carbon\Carbon::parse($visit->scheduled_at)->format('Y-m-d\TH:i')) }}" required>
                    @error('scheduled_at')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="status" class="block text-gray-700 text-sm font-bold mb-2">Status</label>
                    <select name="status" id="status" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('status') border-red-500 @enderror" required>
                        <option value="scheduled" {{ old('status', $visit->status) == 'scheduled' ? 'selected' : '' }}>Scheduled (Terjadwal)</option>
                        <option value="completed" {{ old('status', $visit->status) == 'completed' ? 'selected' : '' }}>Completed (Selesai)</option>
                        <option value="cancelled" {{ old('status', $visit->status) == 'cancelled' ? 'selected' : '' }}>Cancelled (Dibatalkan)</option>
                    </select>
                    @error('status')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mb-4">
                <label for="complaint" class="block text-gray-700 text-sm font-bold mb-2">Keluhan / Alasan Kunjungan</label>
                <textarea name="complaint" id="complaint" rows="3" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('complaint') border-red-500 @enderror">{{ old('complaint', $visit->complaint) }}</textarea>
                @error('complaint')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="mb-4">
                <label for="transport_fee" class="block text-gray-700 text-sm font-bold mb-2">Biaya Transport (Jika Home Visit)</label>
                <input type="number" name="transport_fee" id="transport_fee" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('transport_fee') border-red-500 @enderror" value="{{ old('transport_fee', $visit->transport_fee) }}" min="0">
                @error('transport_fee')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between mt-4">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Update
                </button>
                <a href="{{ route('visits.index') }}" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                    Batal
                </a>
            </div>
        </form>
    </div>
</x-app-layout>

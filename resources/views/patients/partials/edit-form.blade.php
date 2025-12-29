<form action="{{ route('patients.update', $patient) }}" method="POST" class="p-1">
    @csrf
    @method('PUT')
    
    @include('patients.form-fields', ['clients' => $clients, 'patient' => $patient])
    
    <div class="mt-6 flex justify-end gap-3">
        <button type="button" x-on:click="$dispatch('close-modal', 'edit-patient-modal')" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium">
            Batal
        </button>
        <button type="submit" class="px-4 py-2 bg-birawa-600 text-white rounded-lg hover:bg-birawa-700 transition-colors font-bold shadow-lg shadow-birawa-500/30">
            Simpan Perubahan
        </button>
    </div>
</form>
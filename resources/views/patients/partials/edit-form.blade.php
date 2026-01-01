<form action="{{ route('patients.update', $patient) }}" method="POST" class="p-1" x-data="{ isSubmitting: false }" @submit="isSubmitting = true">
    @csrf
    @method('PUT')
    
    <x-patients.form-fields :clients="$clients" :patient="$patient" :withCard="false" />
    
    <div class="mt-6 flex justify-end gap-3">
        <button type="button" x-on:click="$dispatch('close-modal', 'edit-patient-modal')" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium">
            Batal
        </button>
        <button type="submit" 
            :disabled="isSubmitting"
            :class="{'opacity-75 cursor-not-allowed': isSubmitting}"
            class="px-4 py-2 bg-birawa-600 text-white rounded-lg hover:bg-birawa-700 transition-colors font-bold shadow-lg shadow-birawa-500/30 flex items-center gap-2">
            <span x-show="isSubmitting" class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"></span>
            <span x-text="isSubmitting ? 'Menyimpan...' : 'Simpan Perubahan'"></span>
        </button>
    </div>
</form>

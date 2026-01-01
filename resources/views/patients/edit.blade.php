<x-app-layout :hideHeader="true">
    <x-breadcrumb :items="[
        ['label' => 'Home', 'route' => route('dashboard')],
        ['label' => 'Pasien', 'route' => route('patients.index')],
        ['label' => $patient->name, 'route' => route('patients.show', $patient)],
        ['label' => 'Edit']
    ]" />

    <div class="max-w-xl px-4 py-6 mx-auto pb-28">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Edit Data Pasien</h1>
            <p class="text-sm text-gray-500">Perbarui informasi hewan peliharaan</p>
        </div>

        <form action="{{ route('patients.update', $patient) }}" method="POST" x-data="{ isSubmitting: false }" @submit="isSubmitting = true">
            @csrf
            @method('PUT')
            
            <x-patients.form-fields 
                :clients="$clients" 
                :patient="$patient" 
                :withCard="true" 
            />

            <!-- Fixed Bottom Action Bar -->
            <div class="fixed bottom-20 left-0 right-0 p-4 bg-white/80 backdrop-blur-md border-t border-gray-200 z-20"> 
                <div class="max-w-xl mx-auto flex gap-3">
                    <a href="{{ route('patients.index') }}" class="flex-1 px-4 py-3.5 text-center font-bold text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors">
                        Batal
                    </a>
                    <button type="submit" 
                        :disabled="isSubmitting"
                        :class="{'opacity-75 cursor-not-allowed': isSubmitting}"
                        class="flex-[2] px-4 py-3.5 text-center font-bold text-white bg-gradient-to-r from-birawa-600 to-birawa-500 rounded-xl shadow-lg shadow-birawa-500/30 hover:shadow-birawa-500/50 transform active:scale-[0.98] transition-all flex justify-center items-center gap-2">
                        <span x-show="isSubmitting" class="animate-spin rounded-full h-5 w-5 border-b-2 border-white"></span>
                        <span x-text="isSubmitting ? 'Menyimpan...' : 'Simpan Perubahan'"></span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>

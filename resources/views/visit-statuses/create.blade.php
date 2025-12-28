<x-app-layout>
    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold">Tambah Status Kunjungan</h2>
                        <a href="{{ route('visit-statuses.index') }}" class="text-gray-500 hover:text-gray-700">
                            &larr; Kembali
                        </a>
                    </div>

                    <form action="{{ route('visit-statuses.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700">Nama Status</label>
                            <input type="text" name="name" id="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-birawa-500 focus:ring-birawa-500 sm:text-sm" required>
                        </div>

                        <div class="mb-4">
                            <label for="color" class="block text-sm font-medium text-gray-700">Warna (Hex Code)</label>
                            <div class="flex gap-2 items-center">
                                <input type="color" id="colorPicker" class="h-9 w-9 p-0 border-0 rounded overflow-hidden" onchange="document.getElementById('color').value = this.value">
                                <input type="text" name="color" id="color" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-birawa-500 focus:ring-birawa-500 sm:text-sm" placeholder="#000000" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="order" class="block text-sm font-medium text-gray-700">Urutan (Order)</label>
                            <input type="number" name="order" id="order" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-birawa-500 focus:ring-birawa-500 sm:text-sm" value="0">
                        </div>

                        <div class="mb-6">
                            <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                            <textarea name="description" id="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-birawa-500 focus:ring-birawa-500 sm:text-sm"></textarea>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="bg-birawa-600 hover:bg-birawa-700 text-white font-bold py-2 px-4 rounded">
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

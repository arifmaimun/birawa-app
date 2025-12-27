<x-app-layout>
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Edit Pasien</h1>
    </div>

    <div class="bg-white rounded-lg shadow p-6 max-w-2xl">
        <form action="{{ route('patients.update', $patient) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="mb-4">
                <label for="owner_id" class="block text-gray-700 text-sm font-bold mb-2">Pemilik</label>
                <select name="owner_id" id="owner_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('owner_id') border-red-500 @enderror" required>
                    <option value="">Pilih Pemilik</option>
                    @foreach($owners as $owner)
                        <option value="{{ $owner->id }}" {{ (old('owner_id', $patient->owner_id) == $owner->id) ? 'selected' : '' }}>{{ $owner->name }}</option>
                    @endforeach
                </select>
                @error('owner_id')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Nama Hewan</label>
                <input type="text" name="name" id="name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('name') border-red-500 @enderror" value="{{ old('name', $patient->name) }}" required>
                @error('name')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="mb-4">
                    <label for="species" class="block text-gray-700 text-sm font-bold mb-2">Jenis (Spesies)</label>
                    <input type="text" name="species" id="species" placeholder="Kucing, Anjing, dll" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('species') border-red-500 @enderror" value="{{ old('species', $patient->species) }}" required>
                    @error('species')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="breed" class="block text-gray-700 text-sm font-bold mb-2">Ras</label>
                    <input type="text" name="breed" id="breed" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('breed') border-red-500 @enderror" value="{{ old('breed', $patient->breed) }}">
                    @error('breed')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="mb-4">
                    <label for="gender" class="block text-gray-700 text-sm font-bold mb-2">Jenis Kelamin</label>
                    <select name="gender" id="gender" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('gender') border-red-500 @enderror" required>
                        <option value="jantan" {{ old('gender', $patient->gender) == 'jantan' ? 'selected' : '' }}>Jantan</option>
                        <option value="betina" {{ old('gender', $patient->gender) == 'betina' ? 'selected' : '' }}>Betina</option>
                    </select>
                    @error('gender')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="dob" class="block text-gray-700 text-sm font-bold mb-2">Tanggal Lahir (Opsional)</label>
                    <input type="date" name="dob" id="dob" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('dob') border-red-500 @enderror" value="{{ old('dob', $patient->dob) }}">
                    @error('dob')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex items-center justify-between mt-4">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Update
                </button>
                <a href="{{ route('patients.index') }}" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                    Batal
                </a>
            </div>
        </form>
    </div>
</x-app-layout>

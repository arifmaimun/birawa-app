<x-app-layout>
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-800">Detail Produk</h1>
        <a href="{{ route('products.index') }}" class="text-blue-500 hover:text-blue-700">Kembali ke Daftar</a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">Informasi Produk</h2>
        </div>
        <div class="p-6">
            <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-6">
                <div>
                    <dt class="text-sm font-medium text-gray-500">SKU</dt>
                    <dd class="mt-1 text-lg text-gray-900">{{ $product->sku }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Tipe</dt>
                    <dd class="mt-1 text-lg text-gray-900">{{ ucfirst($product->type) }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Nama Produk</dt>
                    <dd class="mt-1 text-lg text-gray-900">{{ $product->name }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Stok</dt>
                    <dd class="mt-1 text-lg text-gray-900">{{ $product->stock }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Harga Beli</dt>
                    <dd class="mt-1 text-lg text-gray-900">Rp {{ number_format($product->cost, 0, ',', '.') }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Harga Jual</dt>
                    <dd class="mt-1 text-lg text-gray-900">Rp {{ number_format($product->price, 0, ',', '.') }}</dd>
                </div>
            </dl>
        </div>
        <div class="bg-gray-50 px-6 py-4 flex justify-end">
            <a href="{{ route('products.edit', $product) }}" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded mr-2">
                Edit
            </a>
            <form action="{{ route('products.destroy', $product) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">Hapus</button>
            </form>
        </div>
    </div>
</x-app-layout>

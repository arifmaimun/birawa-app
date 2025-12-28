<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Product & Service Management</h1>
                <p class="text-sm text-slate-500">Manage your catalog of items and services</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('products.create') }}" class="inline-flex items-center justify-center px-4 py-2 bg-birawa-600 border border-transparent rounded-xl font-bold text-xs text-white uppercase tracking-widest hover:bg-birawa-700 focus:bg-birawa-700 active:bg-birawa-900 focus:outline-none focus:ring-2 focus:ring-birawa-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-lg shadow-birawa-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Add Product
                </a>
            </div>
        </div>

        <!-- Search & Filter -->
        <div class="mb-6">
            <form action="{{ route('products.index') }}" method="GET" class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" 
                       class="pl-10 block w-full rounded-xl border-slate-200 shadow-sm focus:border-birawa-500 focus:ring-birawa-500 placeholder-slate-400 transition-colors" 
                       placeholder="Search by name, SKU, or type...">
            </form>
        </div>

        <!-- Products Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($products as $product)
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 hover:shadow-md transition-shadow relative overflow-hidden group">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wide {{ $product->type === 'jasa' ? 'bg-purple-50 text-purple-700 border border-purple-100' : 'bg-emerald-50 text-emerald-700 border border-emerald-100' }}">
                                {{ ucfirst($product->type) }}
                            </span>
                            <a href="{{ route('products.show', $product) }}" class="block font-bold text-slate-800 mt-2 text-lg hover:text-birawa-600 transition-colors">
                                {{ $product->name }}
                            </a>
                            <p class="text-xs text-slate-400 font-mono mt-1">SKU: {{ $product->sku }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-lg text-slate-800">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                            <p class="text-xs text-slate-400">Sales Price</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4 py-3 border-t border-b border-slate-50 my-3">
                        <div>
                            <p class="text-xs text-slate-400 uppercase font-bold tracking-wider">Cost</p>
                            <p class="text-sm font-medium text-slate-600">Rp {{ number_format($product->cost, 0, ',', '.') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-slate-400 uppercase font-bold tracking-wider">Stock</p>
                            @if($product->type === 'barang')
                                <p class="text-sm font-bold {{ $product->stock < 5 ? 'text-rose-600' : 'text-slate-600' }}">
                                    {{ $product->stock }}
                                </p>
                            @else
                                <p class="text-sm text-slate-400">-</p>
                            @endif
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 pt-1">
                        <a href="{{ route('products.edit', $product) }}" class="p-2 text-slate-400 hover:text-birawa-600 hover:bg-birawa-50 rounded-lg transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </a>
                        <form action="{{ route('products.destroy', $product) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this product?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12 bg-white rounded-2xl border border-slate-100 border-dashed">
                    <svg class="mx-auto h-12 w-12 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-slate-900">No products found</h3>
                    <p class="mt-1 text-sm text-slate-500">Get started by adding a new product or service.</p>
                    <div class="mt-6">
                        <a href="{{ route('products.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-bold rounded-xl text-white bg-birawa-600 hover:bg-birawa-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-birawa-500">
                            <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Add Product
                        </a>
                    </div>
                </div>
            @endforelse
        </div>

        <div class="mt-6">
            {{ $products->withQueryString()->links() }}
        </div>
    </div>
</x-app-layout>

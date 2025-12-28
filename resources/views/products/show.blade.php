<x-app-layout>
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">Product Details</h1>
                    <p class="text-sm text-slate-500">View detailed information about this item</p>
                </div>
                <a href="{{ route('products.index') }}" class="text-sm font-bold text-slate-500 hover:text-birawa-600 transition-colors flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to List
                </a>
            </div>
        </div>

        <div class="bg-white rounded-3xl shadow-lg border border-slate-100 overflow-hidden">
            <!-- Product Header -->
            <div class="bg-slate-50 border-b border-slate-100 p-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wide {{ $product->type === 'jasa' ? 'bg-purple-100 text-purple-700 border border-purple-200' : 'bg-emerald-100 text-emerald-700 border border-emerald-200' }} mb-2">
                        {{ ucfirst($product->type) }}
                    </span>
                    <h2 class="text-3xl font-bold text-slate-800">{{ $product->name }}</h2>
                    <p class="text-slate-500 font-mono mt-1">SKU: {{ $product->sku }}</p>
                </div>
                <div class="text-left md:text-right">
                    <p class="text-sm font-bold text-slate-400 uppercase tracking-wider">Selling Price</p>
                    <p class="text-3xl font-bold text-birawa-600">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                </div>
            </div>

            <!-- Details Grid -->
            <div class="p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Stock Info -->
                    <div class="bg-slate-50 rounded-2xl p-6 border border-slate-100">
                        <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-4">Inventory Status</h3>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-600 font-medium">Current Stock</span>
                            @if($product->type === 'barang')
                                <span class="text-2xl font-bold {{ $product->stock < 5 ? 'text-rose-600' : 'text-slate-800' }}">
                                    {{ $product->stock }}
                                </span>
                            @else
                                <span class="text-slate-400 text-sm italic">N/A (Service)</span>
                            @endif
                        </div>
                    </div>

                    <!-- Cost Info -->
                    <div class="bg-slate-50 rounded-2xl p-6 border border-slate-100">
                        <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-4">Cost Analysis</h3>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-slate-600 font-medium">Cost Price</span>
                            <span class="font-bold text-slate-800">Rp {{ number_format($product->cost, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between pt-2 border-t border-slate-200">
                            <span class="text-slate-600 font-medium">Margin</span>
                            <span class="font-bold text-emerald-600">
                                @if($product->cost > 0)
                                    {{ number_format((($product->price - $product->cost) / $product->cost) * 100, 1) }}%
                                @else
                                    100%
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions Footer -->
            <div class="bg-slate-50 px-8 py-6 border-t border-slate-100 flex flex-col sm:flex-row justify-end gap-3">
                <a href="{{ route('products.edit', $product) }}" class="inline-flex justify-center items-center px-6 py-2.5 bg-white border border-slate-200 rounded-xl text-sm font-bold text-slate-700 shadow-sm hover:bg-slate-50 hover:text-slate-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-birawa-500 transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit Product
                </a>
                <form action="{{ route('products.destroy', $product) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this product? This action cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center px-6 py-2.5 bg-rose-50 border border-rose-100 rounded-xl text-sm font-bold text-rose-600 hover:bg-rose-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-rose-500 transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Delete Product
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

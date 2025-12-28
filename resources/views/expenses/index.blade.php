<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Expense Management</h1>
                <p class="text-sm text-slate-500">Track operational and capital expenditures</p>
            </div>
            <a href="{{ route('expenses.create') }}" class="inline-flex items-center justify-center px-4 py-2 bg-birawa-600 border border-transparent rounded-xl font-bold text-xs text-white uppercase tracking-widest hover:bg-birawa-700 focus:bg-birawa-700 active:bg-birawa-900 focus:outline-none focus:ring-2 focus:ring-birawa-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-lg shadow-birawa-100">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Record Expense
            </a>
        </div>

        <!-- Expenses Grid/List -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($expenses as $expense)
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 hover:shadow-md transition-shadow">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wide {{ $expense->type === 'OPEX' ? 'bg-blue-50 text-blue-700 border border-blue-100' : 'bg-purple-50 text-purple-700 border border-purple-100' }}">
                                {{ $expense->type }}
                            </span>
                            <h3 class="font-bold text-slate-800 mt-2">{{ $expense->category }}</h3>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-lg text-slate-800">Rp {{ number_format($expense->amount, 0, ',', '.') }}</p>
                            <p class="text-xs text-slate-400 font-mono">{{ $expense->transaction_date->format('d M Y') }}</p>
                        </div>
                    </div>
                    
                    @if($expense->notes)
                        <div class="bg-slate-50 rounded-xl p-3 mb-4">
                            <p class="text-sm text-slate-600 line-clamp-2">{{ $expense->notes }}</p>
                        </div>
                    @endif

                    <div class="flex justify-end pt-3 border-t border-slate-100">
                        <form action="{{ route('expenses.destroy', $expense) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this expense?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-sm font-bold text-rose-500 hover:text-rose-700 transition-colors flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12 bg-white rounded-2xl border border-slate-100 border-dashed">
                    <svg class="mx-auto h-12 w-12 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-slate-900">No expenses recorded</h3>
                    <p class="mt-1 text-sm text-slate-500">Get started by recording a new expense.</p>
                    <div class="mt-6">
                        <a href="{{ route('expenses.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-bold rounded-xl text-white bg-birawa-600 hover:bg-birawa-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-birawa-500">
                            <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Record Expense
                        </a>
                    </div>
                </div>
            @endforelse
        </div>

        <div class="mt-6">
            {{ $expenses->links() }}
        </div>
    </div>
</x-app-layout>

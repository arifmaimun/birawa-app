<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="max-w-lg mx-auto bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-8 text-center">
                <div class="mb-6">
                    <div class="mx-auto h-20 w-20 bg-slate-50 rounded-full flex items-center justify-center">
                        <svg class="h-10 w-10 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                </div>
                <h3 class="text-xl font-bold text-slate-800">Medical Record is Locked</h3>
                <p class="mt-2 text-sm text-slate-500 leading-relaxed">
                    This medical record belongs to <span class="font-bold text-slate-700">Dr. {{ $medicalRecord->doctor->name }}</span>. You do not have permission to view the full details.
                </p>

                <div class="mt-8">
                    @if($pendingRequest)
                        <div class="inline-flex items-center px-4 py-2 border border-amber-100 text-sm font-bold rounded-xl text-amber-700 bg-amber-50">
                            <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Access Request Pending
                        </div>
                    @else
                        <form action="{{ route('medical-records.request-access', $medicalRecord) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center px-6 py-2.5 border border-transparent text-sm font-bold rounded-xl shadow-lg text-white bg-birawa-600 hover:bg-birawa-700 focus:outline-none focus:ring-4 focus:ring-birawa-100 transition-all active:scale-95">
                                Request Access
                            </button>
                        </form>
                    @endif
                </div>
                
                <div class="mt-8 pt-6 border-t border-slate-50">
                    <a href="{{ route('dashboard') }}" class="text-sm font-bold text-slate-400 hover:text-slate-600 transition-colors">
                        &larr; Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Services Catalog</h1>
                <p class="text-sm text-slate-500">Manage your medical services and treatments</p>
            </div>
            <a href="{{ route('services.create') }}" class="px-4 py-2 bg-birawa-600 text-white rounded-xl font-bold hover:bg-birawa-700 transition-colors shadow-lg shadow-birawa-500/30">
                + Add New Service
            </a>
        </div>

        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <!-- Search -->
            <div class="p-4 border-b border-slate-50">
                <form action="{{ route('services.index') }}" method="GET">
                    <div class="relative">
                        <input type="text" name="search" value="{{ $search }}" placeholder="Search services..." class="w-full pl-10 pr-4 py-2 rounded-xl border-slate-200 focus:border-birawa-500 focus:ring-birawa-500">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-50">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Service Name</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Duration</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Price</th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-50">
                        @forelse($services as $service)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-slate-800">{{ $service->service_name }}</div>
                                    @if($service->description)
                                        <div class="text-xs text-slate-500 truncate max-w-xs">{{ $service->description }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-slate-600">{{ $service->duration_minutes }} mins / {{ $service->unit }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-emerald-600">Rp {{ number_format($service->price, 0, ',', '.') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('services.edit', $service) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-slate-400">
                                    <svg class="mx-auto h-12 w-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                                    <p class="mt-2 text-sm">No services found.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-slate-50">
                {{ $services->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
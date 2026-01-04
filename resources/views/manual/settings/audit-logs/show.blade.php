<x-manual.layouts.app>
    <x-slot name="header">
        Audit Log Details
    </x-slot>

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="mb-6">
                    <a href="{{ route('manual.audit-logs.index') }}" class="text-indigo-600 hover:text-indigo-900 flex items-center">
                        <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to Logs
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Basic Info -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">User</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $auditLog->user->name ?? 'System' }}</dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Action</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $auditLog->action == 'created' ? 'bg-green-100 text-green-800' : 
                                           ($auditLog->action == 'updated' ? 'bg-yellow-100 text-yellow-800' : 
                                           ($auditLog->action == 'deleted' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800')) }}">
                                        {{ ucfirst($auditLog->action) }}
                                    </span>
                                </dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Model</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $auditLog->model_type }}</dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Model ID</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $auditLog->model_id }}</dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">IP Address</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $auditLog->ip_address ?? '-' }}</dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Date</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $auditLog->created_at->format('d M Y H:i:s') }}</dd>
                            </div>
                            <div class="sm:col-span-2">
                                <dt class="text-sm font-medium text-gray-500">User Agent</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $auditLog->user_agent ?? '-' }}</dd>
                            </div>
                        </dl>
                    </div>

                    <!-- Changes -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Changes</h3>
                        
                        <div class="space-y-4">
                            @if($auditLog->old_values)
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500 mb-2">Old Values</h4>
                                    <pre class="bg-gray-50 p-3 rounded-md text-xs overflow-auto">{{ json_encode($auditLog->old_values, JSON_PRETTY_PRINT) }}</pre>
                                </div>
                            @endif

                            @if($auditLog->new_values)
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500 mb-2">New Values</h4>
                                    <pre class="bg-gray-50 p-3 rounded-md text-xs overflow-auto">{{ json_encode($auditLog->new_values, JSON_PRETTY_PRINT) }}</pre>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-manual.layouts.app>

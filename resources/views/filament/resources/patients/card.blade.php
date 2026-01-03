<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 p-5 border border-gray-200 dark:border-gray-700 relative group h-full flex flex-col cursor-pointer"
     wire:click="mountTableAction('view', {{ $getRecord()->getKey() }})"
     role="button"
     tabindex="0"
     aria-label="View patient {{ $getRecord()->name }}">
    
    {{-- Header: Status Badge & Context Menu --}}
    <div class="flex justify-between items-start mb-4">
        <div class="flex items-center space-x-2 flex-wrap gap-y-1">
            @if($getRecord()->is_sterile)
                <span class="px-2.5 py-0.5 text-xs font-semibold bg-green-100 text-green-800 rounded-full dark:bg-green-900 dark:text-green-200" title="Sterile">
                    Sterile
                </span>
            @endif
            
            @if($getRecord()->allergies || $getRecord()->special_conditions)
                 <span class="px-2.5 py-0.5 text-xs font-semibold bg-red-100 text-red-800 rounded-full dark:bg-red-900 dark:text-red-200 flex items-center gap-1" title="Has Allergies/Conditions">
                    <x-heroicon-s-exclamation-triangle class="w-3 h-3" />
                    Alert
                </span>
            @endif

            @php
                $lastVisit = $getRecord()->visits()->latest('scheduled_at')->first();
                $statusName = $lastVisit?->visitStatus?->name;
                $statusColor = match(strtolower($statusName ?? '')) {
                    'completed' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                    'scheduled' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                    'cancelled' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                    default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'
                };
            @endphp
            @if($statusName)
                 <span class="px-2.5 py-0.5 text-xs font-medium rounded-full {{ $statusColor }}">
                    {{ $statusName }}
                </span>
            @endif
        </div>

        {{-- Context Menu Trigger --}}
        <div class="relative" x-data="{ open: false }" @click.stop>
            <button @click="open = !open" 
                    class="p-1.5 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500"
                    aria-label="Options for {{ $getRecord()->name }}"
                    aria-expanded="open">
                <x-heroicon-m-ellipsis-vertical class="w-5 h-5 text-gray-500" />
            </button>
            
            <div x-show="open" 
                 @click.outside="open = false"
                 class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 rounded-lg shadow-xl py-1 z-50 border border-gray-200 dark:border-gray-700 divide-y divide-gray-100 dark:divide-gray-700"
                 x-transition:enter="transition ease-out duration-100"
                 x-transition:enter-start="transform opacity-0 scale-95"
                 x-transition:enter-end="transform opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="transform opacity-100 scale-100"
                 x-transition:leave-end="transform opacity-0 scale-95"
                 style="display: none;">
                
                <div class="py-1">
                    <button wire:click="mountTableAction('view', {{ $getRecord()->getKey() }})" 
                            class="group flex w-full items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <x-heroicon-s-eye class="mr-3 h-4 w-4 text-gray-400 group-hover:text-primary-500" />
                        View Record
                    </button>
                    <button wire:click="mountTableAction('edit', {{ $getRecord()->getKey() }})" 
                            class="group flex w-full items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <x-heroicon-s-pencil-square class="mr-3 h-4 w-4 text-gray-400 group-hover:text-primary-500" />
                        Edit Profile
                    </button>
                </div>
                <div class="py-1">
                     <button wire:click="mountTableAction('addOwner', {{ $getRecord()->getKey() }})" 
                            class="group flex w-full items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <x-heroicon-s-user-plus class="mr-3 h-4 w-4 text-gray-400 group-hover:text-primary-500" />
                        Add Owner
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Body: Photo & Info --}}
    <div class="flex flex-col items-center flex-grow text-center">
        <div class="relative mb-4 group-hover:scale-105 transition-transform duration-300">
            @if($getRecord()->photo)
                <img src="{{ Storage::url($getRecord()->photo) }}" alt="" class="w-24 h-24 rounded-full object-cover border-4 border-white dark:border-gray-800 shadow-sm ring-2 ring-primary-100 dark:ring-primary-900">
            @else
                <div class="w-24 h-24 rounded-full bg-primary-50 dark:bg-gray-700 flex items-center justify-center text-primary-300 dark:text-gray-500 ring-2 ring-primary-100 dark:ring-gray-600">
                    <x-heroicon-s-user class="w-12 h-12" />
                </div>
            @endif
        </div>
        
        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-1 group-hover:text-primary-600 transition-colors">{{ $getRecord()->name }}</h3>
        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-4">{{ $getRecord()->species }} <span class="text-gray-300 mx-1">•</span> {{ $getRecord()->breed }}</p>
        
        {{-- Quick Stats --}}
        <div class="grid grid-cols-2 gap-2 w-full mt-auto pt-4 border-t border-gray-100 dark:border-gray-700">
            <div class="flex flex-col">
                <span class="text-[10px] uppercase tracking-wider text-gray-400 font-semibold">Gender</span>
                <span class="text-sm font-medium text-gray-700 dark:text-gray-200 capitalize">{{ $getRecord()->gender }}</span>
            </div>
            <div class="flex flex-col">
                <span class="text-[10px] uppercase tracking-wider text-gray-400 font-semibold">Age</span>
                <span class="text-sm font-medium text-gray-700 dark:text-gray-200">{{ $getRecord()->dob ? $getRecord()->dob->age . ' yrs' : '-' }}</span>
            </div>
        </div>
    </div>
</div>

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm hover:shadow-md transition-shadow duration-300 p-4 border border-gray-200 dark:border-gray-700 relative group h-full flex flex-col cursor-pointer"
     wire:click="mountTableAction('view', {{ $getRecord()->getKey() }})">
    
    {{-- Header: Status Badge & Context Menu --}}
    <div class="flex justify-between items-start mb-4">
        <div class="flex items-center space-x-2 flex-wrap gap-y-1">
            @if($getRecord()->is_sterile)
                <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full dark:bg-green-900 dark:text-green-200">
                    Sterile
                </span>
            @else
                <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full dark:bg-yellow-900 dark:text-yellow-200">
                    Non-Sterile
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
                 <span class="px-2 py-1 text-xs font-medium rounded-full {{ $statusColor }}">
                    {{ $statusName }}
                </span>
            @endif
        </div>

        {{-- Context Menu Trigger --}}
        {{-- We stop propagation so clicking menu doesn't open the card details --}}
        <div class="relative" x-data="{ open: false }" @click.stop>
            <button @click="open = !open" class="p-1 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full transition-colors">
                <x-heroicon-m-ellipsis-vertical class="w-5 h-5 text-gray-500" />
            </button>
            
            <div x-show="open" 
                 @click.outside="open = false"
                 class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-md shadow-lg py-1 z-50 border border-gray-200 dark:border-gray-700"
                 x-transition:enter="transition ease-out duration-100"
                 x-transition:enter-start="transform opacity-0 scale-95"
                 x-transition:enter-end="transform opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="transform opacity-100 scale-100"
                 x-transition:leave-end="transform opacity-0 scale-95"
                 style="display: none;">
                
                {{-- Actions --}}
                <button wire:click="mountTableAction('view', {{ $getRecord()->getKey() }})" 
                        class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                    Lihat Rekam Medis Lengkap
                </button>
                <button wire:click="mountTableAction('edit', {{ $getRecord()->getKey() }})" 
                        class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                    Edit Profil
                </button>
                {{-- Custom Action example --}}
                <button wire:click="mountTableAction('addOwner', {{ $getRecord()->getKey() }})" 
                        class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                    Tambah Pemilik Baru
                </button>
            </div>
        </div>
    </div>

    {{-- Body: Photo & Info --}}
    <div class="flex flex-col items-center flex-grow text-center">
        <div class="relative mb-3">
            @if($getRecord()->photo)
                <img src="{{ Storage::url($getRecord()->photo) }}" alt="{{ $getRecord()->name }}" class="w-20 h-20 rounded-full object-cover border-2 border-primary-500">
            @else
                <div class="w-20 h-20 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-gray-400">
                    <x-heroicon-s-user class="w-10 h-10" />
                </div>
            @endif
        </div>
        
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">{{ $getRecord()->name }}</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">{{ $getRecord()->species }} • {{ $getRecord()->breed }}</p>
        
        <div class="text-xs text-gray-500 dark:text-gray-400 mt-auto pt-2 border-t border-gray-100 dark:border-gray-700 w-full flex justify-between">
            <span>{{ $getRecord()->gender }}</span>
            <span>{{ $getRecord()->dob ? $getRecord()->dob->age . ' yrs' : 'N/A' }}</span>
        </div>
    </div>
</div>

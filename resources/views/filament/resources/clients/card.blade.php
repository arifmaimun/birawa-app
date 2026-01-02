<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm hover:shadow-md transition-shadow duration-300 p-4 border border-gray-200 dark:border-gray-700 relative group h-full flex flex-col cursor-pointer"
     wire:click="mountTableAction('view', {{ $getRecord()->getKey() }})">
    
    {{-- Header --}}
    <div class="flex justify-between items-start mb-4">
        <div class="flex items-center space-x-3">
             @if($getRecord()->photo)
                <img src="{{ Storage::url($getRecord()->photo) }}" alt="{{ $getRecord()->name }}" class="w-12 h-12 rounded-full object-cover border border-gray-200 dark:border-gray-600">
            @else
                <div class="w-12 h-12 rounded-full bg-primary-100 dark:bg-primary-900 flex items-center justify-center text-primary-600 dark:text-primary-400">
                    <span class="text-lg font-bold">{{ substr($getRecord()->name, 0, 1) }}</span>
                </div>
            @endif
            <div>
                <h3 class="text-base font-bold text-gray-900 dark:text-white leading-tight">{{ $getRecord()->name }}</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $getRecord()->phone }}</p>
            </div>
        </div>
        
        {{-- Context Menu --}}
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
                
                <button wire:click="mountTableAction('edit', {{ $getRecord()->getKey() }})" 
                        class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                    Edit Profil Client
                </button>
                <button wire:click="mountTableAction('addPatient', {{ $getRecord()->getKey() }})" 
                        class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                    Tambah Pasien Baru
                </button>
                 <button wire:click="mountTableAction('linkPatient', {{ $getRecord()->getKey() }})" 
                        class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                    Tautkan Pasien Existing
                </button>
            </div>
        </div>
    </div>

    {{-- Stats --}}
    <div class="mt-auto grid grid-cols-2 gap-2 pt-3 border-t border-gray-100 dark:border-gray-700">
        <div class="text-center">
            <span class="block text-lg font-semibold text-gray-900 dark:text-white">{{ $getRecord()->patients()->count() }}</span>
            <span class="text-xs text-gray-500 dark:text-gray-400">Pasien</span>
        </div>
        <div class="text-center border-l border-gray-100 dark:border-gray-700">
             {{-- Placeholder for Spending --}}
            <span class="block text-lg font-semibold text-green-600 dark:text-green-400">
                Rp {{ number_format($getRecord()->total_spending, 0, ',', '.') }}
            </span>
            <span class="text-xs text-gray-500 dark:text-gray-400">Total Spending</span>
        </div>
    </div>
</div>

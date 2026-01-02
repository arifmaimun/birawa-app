<nav x-data="{ open: false }" class="z-30">
    <!-- Mobile Bottom Navigation (Visible on small screens, hidden on md+) -->
    <div class="md:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-slate-200 h-20 z-50 flex justify-around items-center px-2 pb-safe transition-transform duration-300">
        <!-- Dashboard/Home -->
        <a href="{{ route('dashboard') }}" class="flex flex-col items-center justify-center w-full h-full space-y-1 {{ request()->routeIs('dashboard') ? 'text-birawa-600' : 'text-slate-400 hover:text-slate-600' }}">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
            <span class="text-[10px] font-medium">Home</span>
        </a>
        
        <!-- Patients -->
        <a href="{{ route('patients.index') }}" class="flex flex-col items-center justify-center w-full h-full space-y-1 {{ request()->routeIs('patients.*') ? 'text-birawa-600' : 'text-slate-400 hover:text-slate-600' }}">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            <span class="text-[10px] font-medium">Patients</span>
        </a>

        <!-- Visits -->
        <a href="{{ route('visits.index') }}" class="flex flex-col items-center justify-center w-full h-full space-y-1 {{ request()->routeIs('visits.*') ? 'text-birawa-600' : 'text-slate-400 hover:text-slate-600' }}">
             <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            <span class="text-[10px] font-medium">Visits</span>
        </a>

        <!-- Menu/More -->
        <div class="relative w-full h-full flex flex-col items-center justify-center">
            <button @click="open = !open" class="flex flex-col items-center justify-center w-full h-full space-y-1 {{ request()->routeIs('clients.*', 'inventory.*', 'expenses.*', 'products.*', 'settings.*', 'profile.*') ? 'text-birawa-600' : 'text-slate-400 hover:text-slate-600' }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
                <span class="text-[10px] font-medium">Menu</span>
            </button>
            
            <!-- Upward Menu (Popover) -->
            <div x-show="open" @click.away="open = false" 
                 x-cloak
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-10 scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                 x-transition:leave-end="opacity-0 translate-y-10 scale-95"
                 class="absolute bottom-20 right-2 w-56 bg-white rounded-2xl shadow-xl border border-slate-100 overflow-hidden mb-2 z-50 origin-bottom-right">
                 
                 <div class="px-4 py-3 bg-slate-50 border-b border-slate-100">
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Management</p>
                 </div>
                 
                 <a href="{{ route('clients.index') }}" class="flex items-center px-4 py-3 text-sm text-slate-700 hover:bg-slate-50 border-b border-slate-50 transition-colors">
                    <svg class="w-5 h-5 mr-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    Clients
                 </a>
                 <a href="{{ route('inventory.index') }}" class="flex items-center px-4 py-3 text-sm text-slate-700 hover:bg-slate-50 border-b border-slate-50 transition-colors">
                    <svg class="w-5 h-5 mr-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    My Inventory
                 </a>
                 <a href="{{ route('services.index') }}" class="flex items-center px-4 py-3 text-sm text-slate-700 hover:bg-slate-50 border-b border-slate-50 transition-colors">
                    <svg class="w-5 h-5 mr-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                    Services
                 </a>
                 <a href="{{ route('expenses.index') }}" class="flex items-center px-4 py-3 text-sm text-slate-700 hover:bg-slate-50 border-b border-slate-50 transition-colors">
                    <svg class="w-5 h-5 mr-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Expenses
                 </a>
                 {{-- <a href="{{ route('products.index') }}" class="flex items-center px-4 py-3 text-sm text-slate-700 hover:bg-slate-50 border-b border-slate-50 transition-colors">
                    <svg class="w-5 h-5 mr-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    Clinic Products
                 </a> --}}
                 <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-3 text-sm text-slate-700 hover:bg-slate-50 border-b border-slate-50 transition-colors">
                    <svg class="w-5 h-5 mr-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    My Profile
                 </a>
                 <a href="{{ route('settings.index') }}" class="flex items-center px-4 py-3 text-sm text-slate-700 hover:bg-slate-50 border-b border-slate-50 transition-colors">
                    <svg class="w-5 h-5 mr-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    Settings
                 </a>

                 @if(auth()->user()->role === 'superadmin')
                 <a href="{{ route('admin.users.index') }}" class="flex items-center px-4 py-3 text-sm text-slate-700 hover:bg-slate-50 border-b border-slate-50 transition-colors">
                    <svg class="w-5 h-5 mr-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    User Management
                 </a>
                 <a href="{{ route('admin.audit-logs.index') }}" class="flex items-center px-4 py-3 text-sm text-slate-700 hover:bg-slate-50 border-b border-slate-50 transition-colors">
                    <svg class="w-5 h-5 mr-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Audit Logs
                 </a>
                 @endif
                 
                 <div class="border-t border-slate-100 my-1"></div>
                 
                 <form method="POST" action="{{ route('logout') }}" class="block">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-3 text-sm text-red-600 hover:bg-red-50 font-medium flex items-center">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        Logout
                    </button>
                 </form>
            </div>
        </div>
    </div>

    <!-- Desktop Sidebar (Hidden on small screens, visible on md+) -->
    <div class="hidden md:flex fixed top-16 left-0 h-[calc(100vh-4rem)] w-64 bg-white border-r border-slate-200 flex-col overflow-y-auto z-40 pb-4">
        <div class="py-4 px-4">
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-4 px-2">Main Menu</p>
            <div class="space-y-1">
                <a href="{{ route('dashboard') }}" class="flex items-center px-2 py-2.5 text-sm font-medium rounded-xl {{ request()->routeIs('dashboard') ? 'bg-birawa-50 text-birawa-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }} transition-colors">
                    <svg class="w-5 h-5 mr-3 {{ request()->routeIs('dashboard') ? 'text-birawa-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    Home
                </a>
                
                <a href="{{ route('patients.index') }}" class="flex items-center px-2 py-2.5 text-sm font-medium rounded-xl {{ request()->routeIs('patients.*') ? 'bg-birawa-50 text-birawa-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }} transition-colors">
                    <svg class="w-5 h-5 mr-3 {{ request()->routeIs('patients.*') ? 'text-birawa-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    Patients
                </a>

                <a href="{{ route('visits.index') }}" class="flex items-center px-2 py-2.5 text-sm font-medium rounded-xl {{ request()->routeIs('visits.*') ? 'bg-birawa-50 text-birawa-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }} transition-colors">
                    <svg class="w-5 h-5 mr-3 {{ request()->routeIs('visits.*') ? 'text-birawa-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    Visits
                </a>
            </div>

            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-4 mt-8 px-2">Management</p>
            <div class="space-y-1">
                <a href="{{ route('clients.index') }}" class="flex items-center px-2 py-2.5 text-sm font-medium rounded-xl {{ request()->routeIs('clients.*') ? 'bg-birawa-50 text-birawa-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }} transition-colors">
                    <svg class="w-5 h-5 mr-3 {{ request()->routeIs('clients.*') ? 'text-birawa-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    Clients
                </a>
                <a href="{{ route('inventory.index') }}" class="flex items-center px-2 py-2.5 text-sm font-medium rounded-xl {{ request()->routeIs('inventory.*') ? 'bg-birawa-50 text-birawa-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }} transition-colors">
                    <svg class="w-5 h-5 mr-3 {{ request()->routeIs('inventory.*') ? 'text-birawa-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    Inventory & Products
                </a>
                <a href="{{ route('expenses.index') }}" class="flex items-center px-2 py-2.5 text-sm font-medium rounded-xl {{ request()->routeIs('expenses.*') ? 'bg-birawa-50 text-birawa-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }} transition-colors">
                    <svg class="w-5 h-5 mr-3 {{ request()->routeIs('expenses.*') ? 'text-birawa-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Expenses
                </a>
            </div>

            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-4 mt-8 px-2">Settings</p>
            <div class="space-y-1">
                <a href="{{ route('profile.edit') }}" class="flex items-center px-2 py-2.5 text-sm font-medium rounded-xl {{ request()->routeIs('profile.*') ? 'bg-birawa-50 text-birawa-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }} transition-colors">
                    <svg class="w-5 h-5 mr-3 {{ request()->routeIs('profile.*') ? 'text-birawa-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    Profile
                </a>
                <a href="{{ route('settings.index') }}" class="flex items-center px-2 py-2.5 text-sm font-medium rounded-xl {{ request()->routeIs('settings.*') ? 'bg-birawa-50 text-birawa-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }} transition-colors">
                    <svg class="w-5 h-5 mr-3 {{ request()->routeIs('settings.*') ? 'text-birawa-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    Settings
                </a>
            </div>
        </div>
        
        <div class="mt-auto p-4 border-t border-slate-100">
             <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex items-center w-full px-2 py-2.5 text-sm font-medium text-red-600 rounded-xl hover:bg-red-50 transition-colors">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    Logout
                </button>
            </form>
        </div>
    </div>
</nav>

@props(['header' => null, 'hideHeader' => false, 'hideNav' => false, 'backUrl' => null])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Birawa Vet') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
    
    <!-- PWA -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <script src="{{ asset('pwa-install.js') }}" defer></script>
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/sw.js').then(function(registration) {
                    console.log('ServiceWorker registration successful with scope: ', registration.scope);
                }, function(err) {
                    console.log('ServiceWorker registration failed: ', err);
                });
            });
        }
    </script>
</head>
<body class="bg-slate-50 font-sans antialiased text-slate-900 pb-24"> <!-- Added padding-bottom for mobile nav -->
    
    <!-- Top Header (Sticky) -->
    <header class="fixed top-0 left-0 right-0 z-40 bg-white/90 backdrop-blur-md border-b border-slate-100 px-4 h-16 flex items-center justify-between shadow-sm transition-all duration-300">
        <div class="flex items-center gap-3">
             @php
                 $finalBackUrl = $backUrl;
                 if (!$finalBackUrl && !request()->routeIs('dashboard')) {
                     $finalBackUrl = url()->previous() !== url()->current() ? url()->previous() : route('dashboard');
                 }
             @endphp
             
             @if($finalBackUrl)
                <a href="{{ $finalBackUrl }}" class="mr-1 p-2 -ml-2 text-slate-500 hover:text-slate-800 rounded-full hover:bg-slate-100 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
             @endif
             <div class="w-9 h-9 bg-gradient-to-br from-birawa-500 to-birawa-600 rounded-xl flex items-center justify-center text-white font-bold text-lg shadow-birawa-sm">
                B
             </div>
             <div class="font-bold text-lg text-slate-800 tracking-tight">{{ $header ?? 'Birawa Vet' }}</div>
        </div>
        <div class="flex items-center gap-3" x-data="{ open: false }">
            <button id="pwa-install-btn" style="display: none;" class="hidden sm:inline-flex items-center px-3 py-1.5 bg-birawa-50 text-birawa-600 border border-birawa-200 rounded-lg text-xs font-bold hover:bg-birawa-100 transition-colors shadow-sm">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Install App
            </button>
            <div class="relative">
                 <button @click="open = !open" class="h-9 w-9 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-600 font-bold text-sm hover:bg-slate-200 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-birawa-500 overflow-hidden">
                     @if(Auth::user()->avatar)
                        <img src="{{ Storage::url(Auth::user()->avatar) }}" alt="{{ Auth::user()->name }}" class="w-full h-full object-cover">
                     @else
                        {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                     @endif
                 </button>

                 <!-- Dropdown -->
                 <div x-show="open" @click.away="open = false" 
                      x-transition:enter="transition ease-out duration-200"
                      x-transition:enter-start="opacity-0 scale-95"
                      x-transition:enter-end="opacity-100 scale-100"
                      x-transition:leave="transition ease-in duration-75"
                      x-transition:leave-start="opacity-100 scale-100"
                      x-transition:leave-end="opacity-0 scale-95"
                      class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg py-1 border border-slate-100 z-50 origin-top-right">
                      
                      <div class="px-4 py-2 border-b border-slate-50">
                          <p class="text-sm font-bold text-slate-800">{{ Auth::user()->name }}</p>
                          <p class="text-xs text-slate-500 truncate">{{ Auth::user()->email }}</p>
                      </div>
                      
                      <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">Profile</a>
                      
                      <form method="POST" action="{{ route('logout') }}">
                          @csrf
                          <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                              Log Out
                          </button>
                      </form>
                 </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="pt-20 px-4 min-h-screen md:ml-64 transition-all duration-300">
        <!-- Flash Messages -->
        @if (session('success'))
            <div x-data="{ show: true }" x-show="show" class="mb-4 bg-green-50 border border-green-200 text-green-800 rounded-lg p-4 flex justify-between items-start shadow-sm" role="alert">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button @click="show = false" class="text-green-500 hover:text-green-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        @endif

        @if (session('error'))
            <div x-data="{ show: true }" x-show="show" class="mb-4 bg-red-50 border border-red-200 text-red-800 rounded-lg p-4 flex justify-between items-start shadow-sm" role="alert">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
                <button @click="show = false" class="text-red-500 hover:text-red-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        @endif

        {{ $slot }}
    </main>

    <!-- Navigation (Responsive) -->
    @unless($hideNav)
        <x-navigation />
    @endunless
    
    @stack('scripts')
</body>
</html>

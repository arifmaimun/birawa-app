<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Birawa App') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-white shadow-md hidden md:block">
            <div class="p-6">
                <a href="/" class="text-2xl font-bold text-gray-800">Birawa App</a>
            </div>
            <nav class="mt-6">
                <a href="{{ route('dashboard') }}" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-200 {{ request()->routeIs('dashboard') ? 'bg-gray-200 text-gray-900 font-bold' : 'text-gray-600' }}">
                    Dashboard
                </a>
                <a href="{{ route('owners.index') }}" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-200 {{ request()->routeIs('owners.*') ? 'bg-gray-200 text-gray-900 font-bold' : 'text-gray-600' }}">
                    Pemilik (Owners)
                </a>
                <a href="{{ route('patients.index') }}" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-200 {{ request()->routeIs('patients.*') ? 'bg-gray-200 text-gray-900 font-bold' : 'text-gray-600' }}">
                    Pasien (Patients)
                </a>
                <a href="{{ route('visits.index') }}" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-200 {{ request()->routeIs('visits.*') ? 'bg-gray-200 text-gray-900 font-bold' : 'text-gray-600' }}">
                    Kunjungan (Visits)
                </a>
                <a href="{{ route('products.index') }}" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-200 {{ request()->routeIs('products.*') ? 'bg-gray-200 text-gray-900 font-bold' : 'text-gray-600' }}">
                    Produk & Layanan
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col">
            <!-- Header (Mobile Menu Button could go here) -->
            <header class="bg-white shadow p-4 md:hidden">
                <div class="flex justify-between items-center">
                    <span class="text-xl font-bold">Birawa App</span>
                    <button class="text-gray-500 focus:outline-none">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
                        </svg>
                    </button>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 p-6 overflow-y-auto">
                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <strong class="font-bold">Success!</strong>
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <strong class="font-bold">Error!</strong>
                        <span class="block sm:inline">{{ session('error') }}</span>
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>

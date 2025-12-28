<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Birawa Vet</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-4 font-sans antialiased">
    <div class="max-w-md w-full bg-white rounded-3xl shadow-xl border border-slate-100 p-8">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-extrabold text-birawa-600 tracking-tight">Birawa Vet</h1>
            <p class="text-slate-400 mt-2 text-sm font-medium">Professional Home Visit Veterinary Platform</p>
        </div>

        @if ($errors->any())
            <div class="bg-rose-50 border border-rose-100 rounded-xl p-4 mb-6 flex items-start gap-3">
                <svg class="h-5 w-5 text-rose-500 mt-0.5 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <p class="text-sm text-rose-600 font-medium">
                    {{ $errors->first() }}
                </p>
            </div>
        @endif

        <form action="{{ route('login.authenticate') }}" method="POST" class="space-y-5">
            @csrf
            
            <div>
                <label for="email" class="block text-sm font-bold text-slate-700 mb-1">Email Address</label>
                <input type="email" name="email" id="email" required autofocus
                    class="w-full rounded-xl border-slate-200 focus:border-birawa-500 focus:ring-birawa-500 placeholder-slate-400 transition-colors shadow-sm py-2.5"
                    value="{{ old('email') }}" placeholder="doctor@example.com">
            </div>

            <div>
                <label for="password" class="block text-sm font-bold text-slate-700 mb-1">Password</label>
                <input type="password" name="password" id="password" required
                    class="w-full rounded-xl border-slate-200 focus:border-birawa-500 focus:ring-birawa-500 placeholder-slate-400 transition-colors shadow-sm py-2.5"
                    placeholder="••••••••">
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input id="remember_me" name="remember" type="checkbox" class="h-4 w-4 text-birawa-600 focus:ring-birawa-500 border-gray-300 rounded">
                    <label for="remember_me" class="ml-2 block text-sm text-slate-600">Remember me</label>
                </div>

                <div class="text-sm">
                    <a href="#" class="font-bold text-birawa-600 hover:text-birawa-700 transition-colors">Forgot password?</a>
                </div>
            </div>

            <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-lg shadow-birawa-100 text-sm font-bold text-white bg-birawa-600 hover:bg-birawa-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-birawa-500 transition-all active:scale-95">
                Sign in
            </button>
        </form>

        <div class="mt-8 text-center text-xs text-slate-400">
            <p>Don't have an account? <span class="font-bold text-slate-500">Contact Admin (Invite Only)</span></p>
        </div>
    </div>
</body>
</html>

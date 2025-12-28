<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Referral Letter - Birawa Vet</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Nunito', 'ui-sans-serif', 'system-ui', '-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'Roboto', 'Helvetica Neue', 'Arial', 'sans-serif'],
                    },
                    colors: {
                        birawa: {
                            50: '#f0fdfa',
                            100: '#ccfbf1',
                            200: '#99f6e4',
                            300: '#5eead4',
                            400: '#2dd4bf',
                            500: '#14b8a6',
                            600: '#0d9488',
                            700: '#0f766e',
                            800: '#115e59',
                            900: '#134e4a',
                            950: '#042f2e',
                        }
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Nunito', sans-serif; }
        @media print {
            .no-print { display: none !important; }
            body { background-color: white; }
            .shadow-lg, .shadow-xl { box-shadow: none !important; }
            .border { border-color: #e2e8f0 !important; }
            .bg-slate-50 { background-color: white !important; }
        }
    </style>
</head>
<body class="bg-slate-50 font-sans antialiased min-h-screen py-10 px-4">
    <div class="max-w-3xl mx-auto">
        <!-- Actions -->
        <div class="mb-6 flex justify-between items-center no-print">
            <a href="{{ url('/') }}" class="text-sm font-bold text-slate-500 hover:text-birawa-600 transition-colors flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Home
            </a>
            <button onclick="window.print()" class="px-5 py-2.5 bg-slate-800 text-white rounded-xl text-sm font-bold shadow-lg hover:bg-slate-700 flex items-center gap-2 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Print Referral
            </button>
        </div>

        <div class="bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden relative">
            <!-- Header Banner -->
            <div class="bg-slate-900 text-white p-8 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-64 h-64 bg-slate-800 rounded-bl-full -mr-32 -mt-32 opacity-50">
                </div>
                <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <h1 class="text-3xl font-bold tracking-tight">BIRAWA VET</h1>
                        <p class="text-slate-400 mt-1 text-sm font-medium tracking-wide">Professional Veterinary Services</p>
                    </div>
                    <div class="text-left md:text-right">
                        <h2 class="text-xl font-bold text-white uppercase tracking-wider">Referral Letter</h2>
                        <p class="text-slate-400 text-sm">Date: {{ $referral->created_at->format('d M Y') }}</p>
                    </div>
                </div>
            </div>

            <div class="p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-10 border-b border-slate-100 pb-10">
                    <div>
                        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">From</h3>
                        <p class="font-bold text-slate-800 text-lg">{{ $referral->sourceDoctor->name }}</p>
                        <p class="text-sm text-slate-600">{{ $referral->sourceDoctor->doctorProfile->specialty ?? 'Veterinarian' }}</p>
                        <p class="text-sm text-slate-500">{{ $referral->sourceDoctor->email }}</p>
                        @if($referral->sourceDoctor->doctorProfile && $referral->sourceDoctor->doctorProfile->clinic_name)
                            <p class="text-sm text-slate-500 font-medium mt-1">{{ $referral->sourceDoctor->doctorProfile->clinic_name }}</p>
                        @endif
                    </div>
                    <div class="md:text-right">
                        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">To</h3>
                        <p class="font-bold text-slate-800 text-lg">{{ $referral->target_clinic_name }}</p>
                        <p class="text-sm text-slate-500 italic mt-1">"{{ $referral->notes }}"</p>
                    </div>
                </div>

                <div class="bg-birawa-50 rounded-2xl p-6 mb-10 border border-birawa-100">
                    <h3 class="text-birawa-800 text-xs uppercase font-bold tracking-wider mb-4 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Patient Information
                    </h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                        <div>
                            <span class="block text-xs text-birawa-600/70 uppercase font-semibold mb-1">Name</span>
                            <span class="font-bold text-birawa-900 text-lg">{{ $referral->patient->name }}</span>
                        </div>
                        <div>
                            <span class="block text-xs text-birawa-600/70 uppercase font-semibold mb-1">Species/Breed</span>
                            <span class="font-bold text-birawa-900">{{ ucfirst($referral->patient->species) }}</span>
                            <span class="block text-xs text-birawa-700">{{ $referral->patient->breed }}</span>
                        </div>
                        <div>
                            <span class="block text-xs text-birawa-600/70 uppercase font-semibold mb-1">Age/Gender</span>
                            <span class="font-bold text-birawa-900">{{ $referral->patient->date_of_birth ? \Carbon\Carbon::parse($referral->patient->date_of_birth)->age . ' y.o' : 'N/A' }}</span>
                            <span class="block text-xs text-birawa-700">{{ ucfirst($referral->patient->gender) }}</span>
                        </div>
                        <div>
                            <span class="block text-xs text-birawa-600/70 uppercase font-semibold mb-1">Owner</span>
                            <span class="font-bold text-birawa-900">{{ $referral->patient->owners->first()->name ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>

                <div class="space-y-8">
                    <div>
                        <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-2 border-b border-slate-200 pb-2">Diagnosis</h3>
                        <div class="prose prose-slate max-w-none text-slate-600 bg-slate-50 p-4 rounded-xl border border-slate-100">
                            {{ $referral->diagnosis }}
                        </div>
                    </div>

                    <div>
                        <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-2 border-b border-slate-200 pb-2">Reason for Referral</h3>
                        <div class="prose prose-slate max-w-none text-slate-600 bg-slate-50 p-4 rounded-xl border border-slate-100">
                            {{ $referral->reason }}
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="mt-16 pt-8 border-t border-slate-100 flex flex-col md:flex-row justify-between items-center gap-4">
                    <div class="text-center md:text-left text-xs text-slate-400">
                        <p>Generated by Birawa Vet System</p>
                        <p class="mt-1">Valid until {{ $referral->created_at->addHours(48)->format('d M Y, H:i') }}</p>
                    </div>
                    <div class="text-center md:text-right">
                        <p class="font-bold text-slate-800">{{ $referral->sourceDoctor->name }}</p>
                        <p class="text-xs text-slate-500 uppercase tracking-wider mt-1">Signature</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

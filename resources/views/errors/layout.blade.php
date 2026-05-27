<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'Terjadi Kesalahan') - Pickup Order PNC</title>
    <!-- Alpine.js -->

    <!-- Vite CSS & JS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .error-grid-bg {
            background-image: radial-gradient(var(--color-shadow-grey-200) 1px, transparent 1px);
            background-size: 20px 20px;
        }
    </style>
</head>
<body class="bg-vanilla-custard-50 text-shadow-grey-900 min-h-screen flex items-center justify-center error-grid-bg font-sans selection:bg-fern-200 selection:text-fern-900">
    
    <div class="max-w-3xl w-full px-6 py-12 text-center">
        <!-- Main Content -->
        @yield('content')

        <!-- Navigation Buttons -->
        <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
            <button onclick="window.history.back()" class="w-full sm:w-auto px-6 py-3 rounded-2xl bg-white border border-shadow-grey-200 text-shadow-grey-700 font-semibold shadow-sm hover:bg-shadow-grey-50 hover:shadow-md transition-all active:scale-95 flex items-center justify-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                Kembali
            </button>
            <a href="{{ url('/') }}" class="w-full sm:w-auto px-6 py-3 rounded-2xl bg-fern-700 text-white font-semibold shadow-md hover:bg-fern-800 hover:shadow-lg transition-all active:scale-95 flex items-center justify-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                </svg>
                Ke Beranda Utama
            </a>
        </div>
    </div>

</body>
</html>

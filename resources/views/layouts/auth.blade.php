<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Login - PNC')</title>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-base-200 flex items-center justify-center p-4">

    <div class="w-full max-w-4xl rounded-3xl overflow-hidden shadow-2xl flex flex-col md:flex-row min-h-[520px]">

        {{-- ===== PANEL KIRI: Dark branding ===== --}}
        <div class="bg-shadow-grey-900 text-white p-8 md:p-10 flex flex-col justify-between md:w-5/12 shrink-0">
            <div>
                {{-- Badge --}}
                <div class="bg-fern-600 text-white text-xs font-bold px-3 py-1.5 rounded-lg w-fit mb-8">
                    Sistem Pickup Order
                </div>

                {{-- Headline --}}
                <h1 class="text-2xl sm:text-3xl md:text-4xl font-extrabold leading-tight text-white">
                    @yield('hero-title', 'Pesan Makanan Langsung, Tanpa Perlu Ke kantin')
                </h1>
            </div>

            {{-- Footer branding --}}
            <div class="mt-8 md:mt-0">
                <div class="bg-white text-base-content rounded-xl px-4 py-3 flex items-center gap-3 w-fit">
                    <span class="font-bold text-sm">Made From and For</span>
                    <span class="text-2xl">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                    </span>
                </div>
            </div>
        </div>

        {{-- ===== PANEL KANAN: Form area ===== --}}
        <div class="bg-fern-50 flex-1 flex items-center justify-center p-8 md:p-10">
            <div class="w-full max-w-sm">
                @yield('form')
            </div>
        </div>

    </div>

</body>
</html>

{{-- 
  Layout Utama Aplikasi (Sisi Mahasiswa / User):
  - Menyediakan metadata global, csrf-token untuk otentikasi AJAX request, serta integrasi aset Vite (CSS/JS).
  - Menyertakan padding bawah ('pb-16 lg:pb-0') untuk mencegah konten halaman tertutup/tumpang tindih 
    oleh bottom dock ('x-dock') yang aktif pada mode tampilan mobile.
  - Memasang komponen kerangka global seperti navbar atas, bottom-nav dock, footer, dan toast notifikasi.
--}}
<!DOCTYPE html>
<html lang="id" data-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Pickup Order - PNC')</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('assets/brand/favicon.svg') }}">
    <link rel="apple-touch-icon" href="{{ asset('assets/brand/apple-touch-icon.svg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>

<body class="pb-16 lg:pb-0 bg-base-100">

    @if (!App\Helpers\OrderHelper::isOrderTimeActive())
        <div class="bg-amber-50 border-b border-amber-200/80 text-amber-900 text-center text-xs sm:text-sm py-2.5 px-4 flex items-center justify-center gap-2 relative z-50">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <span class="font-medium">
                <span class="font-bold text-amber-950">Info Layanan:</span> Pemesanan online tutup (Buka hari {{ App\Helpers\OrderHelper::getActiveDaysFormatted() }} pukul {{ config('app.order_hours.start') }} - {{ config('app.order_hours.end') }} WIB). Anda tetap dapat membeli secara offline langsung di kantin.
            </span>
        </div>
    @endif

    <x-navbar />

    @yield('content')

    <x-dock />

    <x-footer />

    <x-toast />
    @stack('scripts')
</body>

</html>

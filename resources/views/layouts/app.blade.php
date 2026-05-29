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
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>

<body class="pb-16 lg:pb-0 bg-base-100">

    <x-navbar />

    @yield('content')

    <x-dock />

    <x-footer />

    <x-toast />
    @stack('scripts')
</body>

</html>

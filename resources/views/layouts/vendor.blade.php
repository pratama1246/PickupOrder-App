{{-- 
  Layout Utama Dashboard Vendor (Pemilik Kantin):
  - Menyusun struktur dashboard menggunakan komponen Drawer DaisyUI ('drawer lg:drawer-open') 
    untuk navigasi responsive (sidebar persisten di desktop, drawer laci di mobile).
  - Mengelola tinggi viewport secara penuh ('h-screen overflow-hidden') dengan scrollbars 
    hanya pada area utama ('main') guna menjaga stabilitas letak navbar dan dock.
  - Memuat file aset CSS/JS via Vite, komponen toast notifikasi, serta x-dock sebagai bottom-nav mobile.
--}}
<!DOCTYPE html>
<html lang="id" data-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Vendor Dashboard - PNC')</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('assets/brand/favicon.svg') }}">
    <link rel="apple-touch-icon" href="{{ asset('assets/brand/apple-touch-icon.svg') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>

<body class="bg-base-100 h-screen overflow-hidden flex flex-col">

    <x-vendor.navbar />

    <div class="drawer lg:drawer-open flex-1 overflow-hidden">
        <input id="vendor-drawer" type="checkbox" class="drawer-toggle" />

        <div class="drawer-content flex flex-col h-full overflow-hidden">

            <main class="flex-1 overflow-y-auto bg-emerald-50 px-3 py-4 pb-24 sm:p-6 sm:pb-24 lg:p-8">
                @yield('content')
            </main>

            <x-dock role="vendor" />

        </div>

        <div class="drawer-side z-40 h-full">
            <label for="vendor-drawer" aria-label="close sidebar" class="drawer-overlay"></label>
            <x-vendor.sidebar />
        </div>
    </div>

    <x-toast />
    @stack('scripts')
</body>

</html>

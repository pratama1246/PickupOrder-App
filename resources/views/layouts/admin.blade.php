<!DOCTYPE html>
<html lang="id" data-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard - PNC')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>

<body class="bg-base-100 h-screen overflow-hidden flex flex-col">

    <x-admin.navbar />

    <div class="drawer lg:drawer-open flex-1 overflow-hidden">
        <input id="admin-drawer" type="checkbox" class="drawer-toggle" />

        <div class="drawer-content flex flex-col h-full overflow-hidden">

            <main class="flex-1 overflow-y-auto bg-base-100 p-4 pb-24 sm:p-6 sm:pb-24 lg:p-8">
                @yield('content')
            </main>

            <x-dock role="admin" />

        </div>

        <div class="drawer-side z-40 h-full">
            <label for="admin-drawer" aria-label="close sidebar" class="drawer-overlay"></label>
            <x-admin.sidebar />
        </div>
    </div>

    <x-toast />
    @stack('scripts')
</body>

</html>

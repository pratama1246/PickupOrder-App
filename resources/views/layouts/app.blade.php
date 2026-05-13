<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'Pickup Order - PNC')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="pb-16 lg:pb-0 bg-base-100">

    {{-- NAVBAR --}}
    <x-navbar />

    @yield('content')

    {{-- DOCK (mobile) --}}
    <x-dock />

    {{-- FOOTER --}}
    <x-footer />

    @stack('scripts')
</body>
</html>

@extends('errors.layout')

@section('title', 'Sesi Kedaluwarsa')

@section('content')
    <div x-data="{ punched: false }" class="flex flex-col items-center">

        <h1 class="text-7xl font-black text-shadow-grey-400 mb-2">419</h1>
        <h2 class="text-2xl md:text-3xl font-bold text-shadow-grey-800 mb-6">Waktu Antrean Habis</h2>

        <!-- Interactive Ticket -->
        <div class="relative w-72 h-40 group select-none transition-transform duration-500"
            :class="{ 'scale-95 opacity-50 rotate-3': punched }">

            <!-- Ticket Body -->
            <div
                class="absolute inset-0 bg-vanilla-custard-100 rounded-xl shadow-lg border-2 border-dashed border-vanilla-custard-400 flex items-center justify-between p-4 overflow-hidden">
                <!-- Cutout left/right -->
                <div
                    class="absolute -left-3 top-1/2 -translate-y-1/2 w-6 h-6 bg-vanilla-custard-50 rounded-full border-r-2 border-vanilla-custard-400">
                </div>
                <div
                    class="absolute -right-3 top-1/2 -translate-y-1/2 w-6 h-6 bg-vanilla-custard-50 rounded-full border-l-2 border-vanilla-custard-400">
                </div>

                <div class="pl-4">
                    <p class="text-xs text-shadow-grey-500 font-bold tracking-widest mb-1">COUPON CODE</p>
                    <p class="text-2xl font-black text-vanilla-custard-700">EXPIRED</p>
                </div>

                <div
                    class="w-16 h-full flex flex-col justify-between items-end border-l-2 border-dashed border-vanilla-custard-400 pl-2">
                    <div class="w-full h-2 bg-vanilla-custard-300 rounded mb-1"></div>
                    <div class="w-3/4 h-2 bg-vanilla-custard-300 rounded mb-1"></div>
                    <div class="w-full h-2 bg-vanilla-custard-300 rounded mb-1"></div>
                    <div class="w-1/2 h-2 bg-vanilla-custard-300 rounded mb-1"></div>
                    <div class="w-full h-2 bg-vanilla-custard-300 rounded"></div>
                </div>
            </div>

            <!-- Punch Hole (appears when clicked) -->
            <div class="absolute right-12 top-1/2 -translate-y-1/2 w-8 h-8 bg-vanilla-custard-50 rounded-full shadow-inner opacity-0 scale-0 transition-all duration-300"
                :class="{ 'opacity-100 scale-100': punched }"></div>
        </div>

        <!-- Description -->
        <p class="mt-8 mb-6 text-shadow-grey-600 max-w-lg mx-auto text-lg leading-relaxed">
            Sesi belanjamu kedaluwarsa karena ditinggal terlalu lama. Tenang, cukup segarkan sesi untuk kembali memesan
            makanan.
        </p>

        <!-- Custom Refresh Button -->
        <button @click="punched = true; setTimeout(() => window.location.reload(), 800);"
            class="px-6 py-3 rounded-2xl bg-vanilla-custard-600 text-white font-semibold shadow-md hover:bg-vanilla-custard-700 active:scale-95 transition-all flex items-center justify-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" :class="{ 'animate-spin': punched }" fill="none"
                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            Segarkan Sesi
        </button>
    </div>
@endsection

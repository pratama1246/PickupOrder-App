@extends('errors.layout')

@section('title', 'Kesalahan Server')

{{-- 
  Halaman Error 500 (Kesalahan Server Internal):
  - Memperluas kerangka tata letak error pusat ('errors.layout').
  - Mengimplementasikan visualisasi interaktif bertema panci masak gosong yang bergetar memantul ('animate-bounce') 
    dan mengepulkan asap ('animate-ping') menggunakan state Alpine.js lokal ('running', 'smoke').
  - Menyediakan tombol darurat 'STOP' yang memberhentikan getaran panci secara instan dan 
    meredakan kepulan asap secara asinkron dengan efek transisi opacity pasca-jeda ('setTimeout').
--}}

@section('content')
    <div x-data="{ running: true, smoke: true }" class="flex flex-col items-center">

        <h1 class="text-7xl font-black text-red-500 mb-2">500</h1>
        <h2 class="text-2xl md:text-3xl font-bold text-shadow-grey-800 mb-6">Dapurnya Berasap!</h2>

        <div class="relative w-48 h-56 group select-none">

            <div class="absolute -top-12 left-1/2 -translate-x-1/2 flex space-x-2 transition-opacity duration-1000"
                :class="{ 'opacity-0': !smoke, 'opacity-100': smoke }">
                <div class="w-4 h-4 bg-shadow-grey-300 rounded-full animate-ping delay-100"></div>
                <div class="w-6 h-6 bg-shadow-grey-200 rounded-full animate-ping delay-300"></div>
                <div class="w-5 h-5 bg-shadow-grey-300 rounded-full animate-ping delay-500"></div>
            </div>

            <div class="absolute bottom-0 left-1/2 -translate-x-1/2 w-40 h-32 bg-shadow-grey-800 rounded-b-3xl rounded-t-lg shadow-xl flex justify-center border-t-8 border-shadow-grey-700 transition-transform duration-75"
                :class="{ 'animate-bounce': running }">

                <div class="mt-4 px-3 py-1 bg-red-500 text-white font-bold text-xs rounded-full border border-red-700">
                    ERROR 500
                </div>

            </div>

            <div class="absolute top-16 left-1/2 -translate-x-1/2 w-44 h-8 bg-shadow-grey-600 rounded-t-full shadow-md transition-all duration-700 origin-bottom"
                :class="{ '-translate-y-8 rotate-12': running, 'translate-y-0 rotate-0': !running }">
                <div class="absolute -top-3 left-1/2 -translate-x-1/2 w-10 h-3 bg-shadow-grey-700 rounded-full"></div>
            </div>

            <button @click="running = false; setTimeout(() => smoke = false, 500);"
                class="absolute -right-8 bottom-6 w-16 h-16 bg-red-600 hover:bg-red-700 rounded-full shadow-lg border-4 border-red-800 text-white font-black text-xs active:scale-90 transition-all flex items-center justify-center z-10"
                :class="{ 'bg-shadow-grey-500 border-shadow-grey-600': !running }">
                <span x-show="running">STOP</span>
                <span x-show="!running">OK!</span>
            </button>
        </div>

        <p class="mt-10 mb-4 text-shadow-grey-600 max-w-lg mx-auto text-lg leading-relaxed">
            Terjadi kesalahan internal pada sistem kami atau panci masakannya gosong! Tim teknisi koki kami sedang sibuk
            merapikannya.
        </p>

    </div>
@endsection

@extends('errors.layout')

@section('title', 'Halaman Tidak Ditemukan')

{{-- 
  Halaman Error 404 (Halaman Tidak Ditemukan):
  - Memperluas kerangka tata letak error pusat ('errors.layout').
  - Menyediakan visualisasi interaktif berupa penutup saji makanan (cloche) 
    yang dapat dibuka-tutup dengan mengutak-atik properti 'open' di Alpine.js.
  - Memicu perputaran CSS ('rotate-60') dan translasi letak tudung saji untuk mengungkap piring kosong di bawahnya.
--}}

@section('content')
    <div x-data="{ open: false }" class="flex flex-col items-center">

        <h1 class="text-7xl font-black text-fern-700 mb-2">404</h1>
        <h2 class="text-2xl md:text-3xl font-bold text-shadow-grey-800 mb-6">Halaman Tidak Ditemukan</h2>

        <div class="relative w-64 h-64 cursor-pointer group" @click="open = !open">
            <div
                class="absolute bottom-4 left-1/2 -translate-x-1/2 w-48 h-12 bg-white rounded-[50%] shadow-[0_8px_15px_rgba(0,0,0,0.1)] border-b-4 border-shadow-grey-200 z-10 flex items-center justify-center">
                <div class="absolute -top-6 text-sm font-bold text-fern-600 opacity-0 transition-opacity duration-500 delay-300"
                    :class="{ 'opacity-100': open }">
                    Zonk! 🍽️
                </div>
            </div>

            <div class="absolute bottom-10 left-1/2 -translate-x-1/2 w-40 h-32 bg-vanilla-custard-300 rounded-t-full shadow-inner border border-vanilla-custard-400 z-20 origin-bottom-right transition-transform duration-700 ease-[cubic-bezier(0.34,1.56,0.64,1)] group-hover:-translate-y-2"
                :class="{ 'rotate-60 translate-x-12 -translate-y-8': open }">
                <div
                    class="absolute -top-4 left-1/2 -translate-x-1/2 w-8 h-8 rounded-full bg-vanilla-custard-400 border border-vanilla-custard-500">
                </div>
                <div class="absolute top-4 left-4 w-6 h-16 bg-white/30 rounded-full rotate-45 blur-[2px]"></div>
            </div>

        </div>

        <p class="mt-8 text-shadow-grey-600 max-w-lg mx-auto text-lg leading-relaxed">
            Piringnya kosong. Menu atau halaman yang kamu cari mungkin salah saji, sudah diganti, atau sudah disantap orang
            lain.
        </p>

    </div>
@endsection

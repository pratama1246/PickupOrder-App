@extends('errors.layout')

@section('title', 'Akses Ditolak')

{{-- 
  Halaman Error 403 (Akses Ditolak):
  - Memperluas kerangka tata letak error pusat ('errors.layout').
  - Mengimplementasikan interaksi visual berupa pintu terkunci yang bergetar saat diklik 
    dengan mengelola state 'shaking' di Alpine.js lokal dan menggunakan setTimeout untuk mereset getaran.
  - Membawa teks informatif untuk membatasi akses wilayah internal bagi pengguna non-otoritas.
--}}

@section('content')
    <div x-data="{ shaking: false }" class="flex flex-col items-center">

        <h1 class="text-7xl font-black text-emerald-600 mb-2">403</h1>
        <h2 class="text-2xl md:text-3xl font-bold text-shadow-grey-800 mb-6">Akses Ditolak</h2>

        <div class="relative w-56 h-72 cursor-pointer group select-none"
            @click="if(!shaking) { shaking = true; setTimeout(() => shaking = false, 600); }">

            <div class="absolute inset-0 bg-dark-spruce-800 rounded-lg p-2 shadow-xl border border-dark-spruce-900">
                <div class="w-full h-full bg-dark-spruce-50 rounded shadow-inner flex flex-col items-center justify-start pt-6 border border-shadow-grey-300 relative transition-transform duration-75"
                    :class="{ '-translate-x-1 translate-y-1': shaking }">

                    <div
                        class="bg-red-500 text-white font-black text-xl py-2 px-4 rounded shadow-md -rotate-3 border-2 border-red-700">
                        KHUSUS VENDOR/ADMIN
                    </div>

                    {{-- Elemen visual pintu dan gagang kunci dengan animasi peringatan saat diklik --}}
                    <div
                        class="absolute right-4 top-1/2 -translate-y-1/2 w-4 h-16 bg-shadow-grey-400 rounded-full shadow-md flex items-center justify-center">
                         <div class="w-6 h-8 bg-shadow-grey-600 rounded -ml-6 absolute shadow-sm"
                            :class="{ 'animate-ping': shaking }">
                            <div class="w-1.5 h-3 bg-shadow-grey-900 mx-auto mt-2 rounded-full"></div>
                        </div>
                    </div>

                    {{-- Pesan penolakan yang muncul secara dinamis via transisi opacity saat user mencoba mengetuk pintu --}}
                    <div class="absolute -top-16 -right-16 bg-white text-dark-spruce-800 text-sm font-bold p-3 rounded-2xl rounded-bl-none shadow-lg opacity-0 transition-opacity duration-300"
                        :class="{ 'opacity-100': shaking }">
                        Eits! Gak boleh masuk! 🛑
                    </div>
                </div>
            </div>
        </div>

        <p class="mt-12 text-shadow-grey-600 max-w-lg mx-auto text-lg leading-relaxed">
            Kamu tidak punya izin masuk ke area dapur ini. Area ini khusus untuk para Vendor kantin dan Admin PNC.
        </p>

    </div>
@endsection

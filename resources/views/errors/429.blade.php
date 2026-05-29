@extends('errors.layout')

@section('title', 'Terlalu Banyak Permintaan')

{{-- 
  Halaman Error 429 (Terlalu Banyak Permintaan / Rate Limit):
  - Memperluas kerangka tata letak error pusat ('errors.layout').
  - Mengimplementasikan visualisasi interaktif berupa lampu lalu lintas (traffic light) merah menyala.
  - Memanfaatkan state Alpine.js lokal ('flashing', 'clicks') untuk mengendalikan efek pencahayaan lampu merah 
    dan menampilkan bubble teks humoris dinamis yang berubah isinya jika diklik berturut-turut.
--}}

@section('content')
    <div x-data="{ flashing: false, clicks: 0 }" class="flex flex-col items-center text-center">

        <h1 class="text-7xl font-black text-emerald-600 mb-2">429</h1>
        <h2 class="text-2xl md:text-3xl font-bold text-shadow-grey-800 mb-6">Terlalu Banyak Request!</h2>

        <div class="relative w-32 h-72 cursor-pointer group select-none mt-4"
            @click="clicks++; flashing = true; setTimeout(() => flashing = false, 400);">

            <div class="absolute inset-0 bg-dark-spruce-800 rounded-4xl p-4 shadow-xl border-4 border-dark-spruce-900 flex flex-col items-center justify-between z-10 transition-transform duration-100"
                :class="{ 'scale-95': flashing }">

                <div class="relative w-16 h-16 rounded-full border-4 border-dark-spruce-900 shadow-inner transition-all duration-200"
                    :class="flashing ? 'bg-red-500 shadow-[0_0_40px_rgba(239,68,68,1)]' :
                        'bg-red-600 shadow-[0_0_15px_rgba(220,38,38,0.6)]'">
                    <div class="absolute top-1 left-2 w-6 h-3 bg-white/30 rounded-full blur-[1px]"></div>
                </div>

                <div class="relative w-16 h-16 bg-yellow-600/30 rounded-full border-4 border-dark-spruce-900 shadow-inner">
                    <div class="absolute top-1 left-2 w-6 h-3 bg-white/10 rounded-full blur-[1px]"></div>
                </div>

                <div class="relative w-16 h-16 bg-emerald-600/30 rounded-full border-4 border-dark-spruce-900 shadow-inner">
                    <div class="absolute top-1 left-2 w-6 h-3 bg-white/10 rounded-full blur-[1px]"></div>
                </div>

            </div>

            <div class="absolute -top-12 -right-36 w-40 bg-white text-dark-spruce-800 text-sm font-bold p-3 rounded-2xl rounded-bl-none shadow-lg opacity-0 transition-opacity duration-300 z-20 text-left"
                :class="{ 'opacity-100': flashing }">
                <span
                    x-text="clicks > 5 ? 'Udah dibilang sabar! 😭' : (clicks > 2 ? 'Masih merah bang. Hehe!' : 'Rem dulu! 🛑')"></span>
            </div>
        </div>

        <p class="mt-16 text-shadow-grey-600 max-w-lg mx-auto text-lg leading-relaxed">
            Sistem mendeteksi terlalu banyak permintaan dari perangkatmu dalam waktu singkat. Tarik napas sebentar, lalu
            coba lagi.
        </p>

    </div>
@endsection

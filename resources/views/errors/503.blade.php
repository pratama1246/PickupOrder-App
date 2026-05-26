@extends('errors.layout')

@section('title', 'Kantin Tutup Sementara')

@section('content')
<div class="flex flex-col items-center">
    
    <h1 class="text-7xl font-black text-shadow-grey-500 mb-2">503</h1>
    <h2 class="text-2xl md:text-3xl font-bold text-shadow-grey-800 mb-6">Kantin Sedang Bersih-bersih</h2>

    <!-- Hanging Signboard -->
    <div class="relative w-64 h-48 group cursor-default mb-8">
        <!-- Ropes -->
        <div class="absolute top-0 left-10 w-1 h-12 bg-shadow-grey-400 origin-top animate-pulse"></div>
        <div class="absolute top-0 right-10 w-1 h-12 bg-shadow-grey-400 origin-top animate-pulse"></div>
        
        <!-- Board -->
        <div class="absolute top-12 left-0 w-full h-24 bg-dark-spruce-800 rounded-lg shadow-xl border-4 border-dark-spruce-900 flex flex-col items-center justify-center origin-top transition-transform duration-700 ease-in-out hover:rotate-3">
            <p class="text-white font-black text-2xl tracking-widest">TUTUP</p>
            <p class="text-shadow-grey-300 text-xs font-semibold mt-1">MAINTENANCE</p>
        </div>
    </div>

    <!-- Description -->
    <p class="text-shadow-grey-600 max-w-lg mx-auto text-lg leading-relaxed">
        Mohon maaf, layanan sedang dihentikan sementara untuk pemeliharaan rutin agar sistem lebih cepat dan stabil. Coba kembali beberapa saat lagi!
    </p>

</div>
@endsection

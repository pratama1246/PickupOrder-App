@extends('layouts.vendor')

@section('title', 'Dashboard - Vendor PNC')

@section('content')

<div class="max-w-4xl">
    <h1 class="text-2xl font-bold text-base-content mb-6">Statistik Penjualan</h1>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
        
        <div class="bg-base-200 rounded-xl p-5 shadow-sm border border-base-content/5">
            <p class="text-xs sm:text-sm font-bold text-base-content/70 mb-2">Pesanan Baru</p>
            <p class="text-xl sm:text-2xl font-bold text-base-content">7 Pesanan</p>
        </div>

        <div class="bg-base-200 rounded-xl p-5 shadow-sm border border-base-content/5">
            <p class="text-xs sm:text-sm font-bold text-base-content/70 mb-2">Sedang Diproses</p>
            <p class="text-xl sm:text-2xl font-bold text-base-content">8 Pesanan</p>
        </div>

        <div class="bg-base-200 rounded-xl p-5 shadow-sm border border-base-content/5">
            <p class="text-xs sm:text-sm font-bold text-base-content/70 mb-2">Siap Pickup</p>
            <p class="text-xl sm:text-2xl font-bold text-base-content">4 Pesanan</p>
        </div>

    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        
        <div class="bg-base-200 rounded-xl p-5 shadow-sm border border-base-content/5">
            <p class="text-xs sm:text-sm font-bold text-base-content/70 mb-2">Total Pendapatan</p>
            <p class="text-xl sm:text-2xl font-bold text-base-content">Rp. 2.500.000</p>
        </div>

        <div class="bg-base-200 rounded-xl p-5 shadow-sm border border-base-content/5">
            <p class="text-xs sm:text-sm font-bold text-base-content/70 mb-2">Menu Habis</p>
            <p class="text-xl sm:text-2xl font-bold text-red-500">2 Menu</p>
        </div>

    </div>
</div>

@endsection

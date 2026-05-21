@extends('layouts.vendor')

@section('title', 'Dashboard - Vendor PNC')

@section('content')

<div class="max-w-4xl">
    <h1 class="text-2xl font-bold text-base-content mb-6">Statistik Penjualan</h1>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">

        <div class="bg-base-200 rounded-xl p-5 shadow-sm border border-base-content/5">
            <p class="text-xs sm:text-sm font-bold text-base-content/70 mb-2">Pesanan Baru</p>
            <p class="text-xl sm:text-2xl font-bold text-base-content">{{ $stats['pesanan_baru'] }} Pesanan</p>
        </div>

        <div class="bg-base-200 rounded-xl p-5 shadow-sm border border-base-content/5">
            <p class="text-xs sm:text-sm font-bold text-base-content/70 mb-2">Sedang Diproses</p>
            <p class="text-xl sm:text-2xl font-bold text-base-content">{{ $stats['sedang_dimasak'] }} Pesanan</p>
        </div>

        <div class="bg-base-200 rounded-xl p-5 shadow-sm border border-base-content/5">
            <p class="text-xs sm:text-sm font-bold text-base-content/70 mb-2">Siap Pickup</p>
            <p class="text-xl sm:text-2xl font-bold text-base-content">{{ $stats['siap_pickup'] }} Pesanan</p>
        </div>

    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

        <div class="bg-base-200 rounded-xl p-5 shadow-sm border border-base-content/5">
            <p class="text-xs sm:text-sm font-bold text-base-content/70 mb-2">Total Pendapatan</p>
            <p class="text-xl sm:text-2xl font-bold text-base-content">Rp {{ number_format($stats['total_pendapatan'], 0, ',', '.') }}</p>
        </div>

        <div class="bg-base-200 rounded-xl p-5 shadow-sm border border-base-content/5">
            <p class="text-xs sm:text-sm font-bold text-base-content/70 mb-2">Menu Habis</p>
            <p class="text-xl sm:text-2xl font-bold {{ $stats['menu_habis'] > 0 ? 'text-red-500' : 'text-base-content' }}">{{ $stats['menu_habis'] }} Menu</p>
        </div>

    </div>
</div>

@endsection

@extends('layouts.admin')

@section('title', 'Dashboard - Admin PNC')

@section('content')

<div class="max-w-4xl">
    <h1 class="text-2xl font-bold text-base-content mb-6">Statistik Penjualan</h1>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
        <x-admin.stat-card label="Pengguna"     value="{{ $stats['total_pengguna'] }} Pengguna" />
        <x-admin.stat-card label="Total Kantin" value="{{ $stats['total_kantin'] }} Kantin" />
        <x-admin.stat-card label="Total Order"  value="{{ $stats['total_order'] }} Pesanan" />
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <x-admin.stat-card label="Total Transaksi" value="Rp {{ number_format($stats['total_transaksi'], 0, ',', '.') }}" />
        <x-admin.stat-card label="Total Menu"      value="{{ $stats['total_menu'] }} Menu" />
    </div>
</div>

@endsection

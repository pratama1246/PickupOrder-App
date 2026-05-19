@extends('layouts.admin')

@section('title', 'Dashboard - Admin PNC')

@section('content')

<div class="max-w-4xl">
    <h1 class="text-2xl font-bold text-base-content mb-6">Statistik Penjualan</h1>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
        <x-admin.stat-card label="Pengguna"     value="100 Pengguna" />
        <x-admin.stat-card label="Total Kantin" value="5 Kantin" />
        <x-admin.stat-card label="Total Order"  value="25 Pesanan" />
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <x-admin.stat-card label="Total Transaksi" value="Rp. 5.500.000" />
        <x-admin.stat-card label="Total Menu"      value="87 Menu" />
    </div>
</div>

@endsection

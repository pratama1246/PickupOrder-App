@extends('layouts.vendor')

@section('title', 'Detail Transaksi - Vendor PNC')

@section('content')

<div class="max-w-4xl pb-10 lg:pb-0">
    <x-breadcrumb
        compact
        :links="[
            ['label' => 'Order', 'url' => route('vendor.order.index')],
            ['label' => 'Detail Transaksi']
        ]"
    />

    <div class="mb-6 sm:mb-8">
        <h1 class="text-2xl sm:text-4xl font-bold text-base-content mb-2">Detail Transaksi</h1>
        <p class="text-base-content/70 text-sm sm:text-lg font-medium">Informasi lengkap mengenai pesanan pelanggan.</p>
    </div>

    <x-vendor.order-detail-card :order="$order" :canteen="$canteen" />

</div>

@endsection

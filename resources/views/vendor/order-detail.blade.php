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

    <h1 class="text-2xl font-bold text-base-content mb-6">Detail Transaksi</h1>

    <x-vendor.order-detail-card :order="$order" :canteen="$canteen" />

</div>

@endsection

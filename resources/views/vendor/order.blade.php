@extends('layouts.vendor')

@section('title', 'Daftar Transaksi - Vendor PNC')

@section('content')

<div class="max-w-4xl pb-10 lg:pb-0">
    <h1 class="text-2xl font-bold text-base-content mb-6">Daftar Transaksi</h1>

    <div class="flex flex-col gap-4">

        @forelse($orders as $order)
            <div class="bg-vanilla-custard-50 border border-base-content/30 rounded-2xl p-4 sm:p-6 shadow-sm flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h2 class="text-base sm:text-lg font-bold text-base-content mb-1">No. Orderan : {{ $order->order_code }}</h2>
                    <p class="text-xs sm:text-sm font-medium text-base-content mb-3">Jenis Pesanan : {{ $order->items->sum('qty') }} item</p>
                    <x-status-badge :status="$order->status_label" />
                </div>

                <a href="{{ route('vendor.order.show', $order->id) }}" class="btn bg-base-300 hover:bg-base-400 text-base-content border-none w-full sm:w-auto px-6 shadow-sm font-bold">
                    Detail
                </a>
            </div>
        @empty
            <div class="bg-vanilla-custard-50 border border-base-content/30 rounded-2xl p-8 text-center shadow-sm">
                <p class="text-base-content/60 font-medium">Belum ada transaksi masuk.</p>
            </div>
        @endforelse

    </div>

    <div class="mt-6">
        {{ $orders->links() }}
    </div>
</div>

@endsection

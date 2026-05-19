@extends('layouts.vendor')

@section('title', 'Daftar Transaksi - Vendor PNC')

@section('content')

<div class="max-w-4xl">
    <h1 class="text-2xl font-bold text-base-content mb-6">Daftar Transaksi</h1>

    <div class="flex flex-col gap-4">
        
        @for($i = 1; $i <= 3; $i++)
        <div class="bg-vanilla-custard-50 border border-base-content/30 rounded-2xl p-4 sm:p-6 shadow-sm flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="text-base sm:text-lg font-bold text-base-content mb-1">No. Orderan : PNC-123455478836</h2>
                <p class="text-xs sm:text-sm font-medium text-base-content mb-3">Jenis Pesanan :</p>
                <x-status-badge status="Diproses" />
            </div>
            
            <a href="/vendor/order/detail" class="btn bg-base-300 hover:bg-base-400 text-base-content border-none w-full sm:w-auto px-6 shadow-sm font-bold">
                Detail
            </a>
        </div>
        @endfor

    </div>
</div>

@endsection

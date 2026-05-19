@extends('layouts.vendor')

@section('title', 'Detail Transaksi - Vendor PNC')

@section('content')

<div class="max-w-4xl">
    <h1 class="text-2xl font-bold text-base-content mb-6">Detail Transaksi</h1>

    <div class="bg-vanilla-custard-50 border border-base-content/30 rounded-3xl p-6 sm:p-8 shadow-sm">
        
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-base-content/20 pb-4 mb-6">
            <div>
                <h2 class="text-xl font-bold text-base-content">Kantin 1</h2>
                <p class="text-sm font-medium text-base-content/70 mt-1">No. Order : PNC-123455478836</p>
            </div>
            <x-status-badge status="Diproses" />
        </div>

        <div class="bg-white border border-base-content/20 rounded-2xl p-4 sm:p-6 mb-6 shadow-sm">
            <x-user.order-item 
                image="{{ asset('assets/food/Nasi Rames.jpg') }}"
                name="Nasi Rames"
                description="Nasi + Sayur Mi + Kering Tempe + Sayur Sawi"
                price="Rp. 20.000"
                quantity="1"
                variant="list"
            />
            <x-user.order-item 
                image="{{ asset('assets/food/es teh.jpg') }}"
                name="Es Teh"
                price="Rp. 6.000"
                quantity="1"
                variant="list"
            />
            
            <div class="mt-4 pt-4 border-t border-base-content/10">
                <p class="text-xs font-bold text-base-content/60 uppercase mb-1">Catatan dari Pembeli</p>
                <p class="text-sm font-medium text-base-content">Nasi rames jangan pedes ya bu, es tehnya manis banget.</p>
            </div>
        </div>

        <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6 pt-2">
            <div>
                <h3 class="text-lg font-bold text-base-content mb-1">Total Belanja</h3>
                <p class="text-2xl font-extrabold text-fern-700">Rp. 26.000</p>
            </div>
            
            <div class="flex-1 max-w-sm w-full">
                <ul class="text-xs sm:text-sm font-medium text-base-content/80 space-y-1.5 mb-5">
                    <li class="flex items-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-base-content/50 shrink-0"></span>
                        Jadwal Pickup: 11.30 - 12.00
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-base-content/50 shrink-0"></span>
                        Metode Pembayaran: QRIS
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-base-content/50 shrink-0"></span>
                        Status: Menunggu Konfirmasi
                    </li>
                </ul>

                <div class="flex flex-col sm:flex-row gap-3">
                    <button class="btn bg-fern-700 hover:bg-fern-800 text-white border-none flex-1 font-bold shadow-sm">
                        Ubah Status
                    </button>
                    <button class="btn bg-red-500 hover:bg-red-600 text-white border-none flex-1 font-bold shadow-sm">
                        Batalkan
                    </button>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection

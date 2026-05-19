@extends('layouts.app')

@section('title', 'Detail Pesanan - PNC')

@section('content')
<main class="min-h-screen bg-base-100 pb-12">
    
    {{-- Breadcrumb --}}
    <x-breadcrumb 
        class="pt-8 pb-4"
        :links="[
            ['label' => 'Beranda', 'url' => '/'],
            ['label' => 'Riwayat', 'url' => '/riwayat'],
            ['label' => 'Order No. PNC-123455478836']
        ]" 
    />

    {{-- Header Section --}}
    <section class="px-4 sm:px-10 md:px-16 lg:px-24 pb-6">
        <div class="max-w-8xl flex flex-col sm:flex-row sm:justify-between sm:items-end gap-4">
            <div>
                <h1 class="text-2xl sm:text-4xl font-bold text-base-content mb-2">Detail Pesanan</h1>
                <p class="text-base-content/70 text-sm sm:text-lg font-medium">Menampilkan detail dari daftar pesanan yang pernah dibuat pengguna.</p>
            </div>
        </div>
    </section>

    {{-- Order Detail Card Section --}}
    <section class="px-4 sm:px-10 md:px-16 lg:px-24">
        <div class="max-w-8xl">
            <div class="max-w-4xl space-y-6">
            
            <div class="bg-vanilla-custard-50 border border-base-content/30 rounded-3xl p-4 sm:p-8 shadow-sm">
                
                {{-- Top Header --}}
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 sm:gap-4 border-b border-base-content/20 pb-6 mb-6">
                    <div>
                        <h2 class="text-lg sm:text-2xl font-bold text-base-content mb-1">No. Order : PNC-123455478836</h2>
                        <x-status-badge status="Diproses" />
                    </div>
                    <button class="btn bg-fern-700 hover:bg-fern-800 text-white border-none rounded-lg font-bold text-sm w-full sm:w-auto">
                        Pesan Lagi
                    </button>
                </div>

                {{-- Info Box --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">
                    <div class="bg-white rounded-2xl p-4 border border-base-content/10 shadow-sm">
                        <p class="text-xs text-base-content/50 uppercase font-bold mb-1">Lokasi Kantin</p>
                        <p class="font-bold text-base-content text-sm">Kantin Jurusan Komputer dan Bisnis</p>
                    </div>
                    <div class="bg-white rounded-2xl p-4 border border-base-content/10 shadow-sm">
                        <p class="text-xs text-base-content/50 uppercase font-bold mb-1">Metode Pembayaran</p>
                        <p class="font-bold text-base-content text-sm">QRIS</p>
                    </div>
                    <div class="bg-white rounded-2xl p-4 border border-base-content/10 shadow-sm">
                        <p class="text-xs text-base-content/50 uppercase font-bold mb-1">Waktu Pickup</p>
                        <p class="font-bold text-base-content text-sm">11.30 - 12.00</p>
                    </div>
                    <div class="bg-white rounded-2xl p-4 border border-base-content/10 shadow-sm">
                        <p class="text-xs text-base-content/50 uppercase font-bold mb-1">Status Antrian</p>
                        <p class="font-bold text-base-content text-sm">Antrian ke-3 dari 7 Pesanan</p>
                    </div>
                </div>
                
                {{-- Inner White Card (Items) --}}
                <div class="bg-white border border-base-content/30 rounded-2xl sm:rounded-3xl p-4 sm:p-6 mb-6">
                    <div class="flex justify-between items-center border-b border-base-content/10 pb-4 mb-4">
                        <h3 class="text-lg sm:text-xl font-bold text-base-content">Kantin 1</h3>
                        <span class="text-xs sm:text-sm font-extrabold text-base-content">4 Pesanan</span>
                    </div>

                    {{-- Item 1 --}}
                    <x-user.order-item 
                        image="{{ asset('assets/food1.jpg') }}"
                        name="Nasi Rames"
                        description="Nasi + Sayur Mi + Kering Tempe + Sayur Sawi"
                        price="Rp. 20.000"
                        quantity="2"
                        variant="list"
                    />

                    {{-- Item 2 --}}
                    <x-user.order-item 
                        image="{{ asset('assets/es_teh.jpg') }}"
                        name="Es Teh"
                        price="Rp. 6.000"
                        quantity="2"
                        variant="list"
                    />
                </div>

                {{-- Catatan --}}
                <div class="mb-6">
                    <p class="text-sm font-bold text-base-content mb-2">Catatan untuk kantin (Opsional)</p>
                    <div class="bg-white border border-base-content/20 rounded-xl p-4 text-sm text-base-content/70">
                        Nasi rames jangan pedes ya bu, es tehnya manis banget.
                    </div>
                </div>

                {{-- Total Belanja --}}
                <div class="flex justify-between items-center border-t border-base-content/20 pt-6">
                    <h3 class="text-lg sm:text-xl font-bold text-base-content">Total Belanja</h3>
                    <p class="text-2xl sm:text-3xl font-extrabold text-fern-700">Rp. 26.000</p>
                </div>

            </div>
        </div>
    </div>
</section>
</main>
@endsection

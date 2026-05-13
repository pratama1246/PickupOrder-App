@extends('layouts.app')

@section('title', 'Riwayat Pesanan - PNC')

@section('content')
<main class="min-h-screen bg-base-100 pb-12">
    
    {{-- Breadcrumb --}}
    <x-breadcrumb 
        class="pt-8 pb-4"
        :links="[
            ['label' => 'Beranda', 'url' => '/'],
            ['label' => 'Riwayat']
        ]" 
    />

    {{-- Header Section --}}
    <section class="px-4 sm:px-10 md:px-16 lg:px-24 pb-6">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-2xl sm:text-4xl font-extrabold text-base-content mb-2">Riwayat Pesanan</h1>
            <p class="text-base-content/70 text-sm sm:text-lg font-medium">Menampilkan daftar pesanan yang pernah dibuat pengguna.</p>
        </div>
    </section>

    {{-- Filter and Search Section --}}
    <section class="px-4 sm:px-10 md:px-16 lg:px-24 mb-8">
        <div class="max-w-4xl mx-auto flex flex-col sm:flex-row items-stretch sm:items-center gap-3 sm:gap-4">
            {{-- Filter Button --}}
            <button class="btn btn-md bg-base-200 hover:bg-base-300 text-base-content text-sm font-bold border-none rounded-full px-6 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
                Filter By:
            </button>
            
            {{-- Search --}}
            <input type="text" placeholder="Cari riwayat pesanan..." class="input input-bordered input-md w-full sm:max-w-[18rem] border-base-content/40 rounded-full focus:outline-none focus:border-base-content font-medium text-sm sm:text-base" />
        </div>
    </section>

    {{-- Order List Section --}}
    <section class="px-4 sm:px-10 md:px-16 lg:px-24">
        <div class="max-w-4xl mx-auto space-y-6">
            
            {{-- Card Riwayat --}}
            <div class="bg-vanilla-custard-50 border border-base-content/30 rounded-3xl p-4 sm:p-10 shadow-sm">
                
                {{-- Top Header of Card --}}
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 sm:gap-4 mb-6">
                    <div>
                        <h2 class="text-lg sm:text-2xl font-extrabold text-base-content mb-1">No. Orderan : PNC-123455478836</h2>
                        <p class="text-xs sm:text-sm font-bold text-base-content">Jenis Pesanan :</p>
                    </div>
                    <x-status-badge status="Diproses" />
                </div>
                
                {{-- Inner White Card --}}
                <div class="bg-white border border-base-content/30 rounded-2xl sm:rounded-3xl p-4 sm:p-6 mb-6">
                    <div class="flex justify-between items-center mb-4 sm:mb-6">
                        <h3 class="text-xl sm:text-2xl font-bold text-base-content">Kantin 1</h3>
                        <span class="text-xs sm:text-sm font-extrabold text-base-content">4 Pesanan</span>
                    </div>

                    {{-- Item 1 --}}
                    <x-order-item 
                        image="{{ asset('assets/food1.jpg') }}"
                        name="Nasi Rames"
                        description="Nasi + Sayur Mi + Kering Tempe + Sayur Sawi"
                        price="Rp. 20.000,00"
                        variant="card"
                    />

                    {{-- Item 2 --}}
                    <x-order-item 
                        image="{{ asset('assets/es_teh.jpg') }}"
                        name="Es Teh"
                        price="Rp. 6.000,00"
                        variant="card"
                    />
                </div>

                {{-- Action Buttons --}}
                <div class="flex flex-col sm:flex-row justify-end gap-3 mt-6 sm:mt-8">
                    <a href="/riwayat/detail" class="btn bg-[#d9d9d9] hover:bg-gray-400 text-black border-none w-full sm:w-auto px-8 py-2 min-h-0 h-auto rounded-md font-bold text-sm">
                        Detail
                    </a>
                    <button class="btn bg-fern-700 hover:bg-fern-800 text-white border-none w-full sm:w-auto px-8 py-2 min-h-0 h-auto rounded-md font-bold text-sm">
                        Pesan Lagi
                    </button>
                </div>

            </div>

        </div>
    </section>
</main>
@endsection

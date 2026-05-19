@extends('layouts.app')

@section('title', 'Keranjang Belanja - PNC')

@section('content')
<main class="min-h-screen bg-base-100 pb-16">

    {{-- Breadcrumb --}}
    <x-breadcrumb
        class="pt-8 pb-4"
        :links="[
            ['label' => 'Beranda', 'url' => '/'],
            ['label' => 'Keranjang Belanja']
        ]"
    />

    {{-- Header --}}
    <section class="px-4 sm:px-10 md:px-16 lg:px-24 pb-6">
        <div class="max-w-8xl mx-auto">
            <h1 class="text-2xl sm:text-4xl font-bold text-base-content mb-1">Keranjang Belanja</h1>
            <p class="text-base-content/70 text-sm sm:text-base font-medium">Silahkan pilih kantin dan detail pesanan terlebih dahulu</p>
        </div>
    </section>

    {{-- Main Content --}}
    <section class="px-4 sm:px-10 md:px-16 lg:px-24">
        <div class="max-w-8xl mx-auto flex flex-col lg:flex-row gap-6 lg:gap-8 items-start">

            {{-- ======= KIRI: ITEM LIST ======= --}}
            <div class="w-full lg:flex-1 min-w-0 space-y-5">

                {{-- Card Kantin 1 --}}
                <div class="bg-vanilla-custard-50 border border-base-content/20 rounded-3xl p-5 sm:p-6 shadow-sm">

                    {{-- Header Kantin --}}
                    <div class="flex items-center justify-between mb-5">
                        <h2 class="text-lg sm:text-xl font-bold text-base-content">Kantin 1</h2>
                        <span class="text-sm font-bold text-base-content/60">4 Pesanan</span>
                    </div>

                    {{-- Item List --}}
                    <div class="space-y-3 mb-5">

                        <x-user.cart-item
                            image="{{ asset('assets/food/Nasi Rames.jpg') }}"
                            name="Nasi Rames"
                            description="Nasi + Sayur Mi + Kering Tempe + Sayur Sawi"
                            :price="10000"
                            :quantity="2"
                        />

                        <x-user.cart-item
                            image="{{ asset('assets/food/es teh.jpg') }}"
                            name="Es Teh"
                            :price="3000"
                            :quantity="2"
                        />

                    </div>

                    {{-- Catatan --}}
                    <textarea
                        rows="3"
                        placeholder="Catatan untuk kantin (Opsional)"
                        class="textarea textarea-bordered w-full rounded-2xl text-sm font-medium border-base-content/20 bg-white focus:outline-none focus:border-base-content/40 resize-none placeholder:text-base-content/40"
                    ></textarea>

                </div>

            </div>

            {{-- ======= KANAN: RINGKASAN ======= --}}
            <div class="w-full lg:w-80 xl:w-96 shrink-0">
                <x-user.cart-summary
                    :canteens="[
                        ['name' => 'Kantin 1', 'itemCount' => 4, 'subtotal' => 26000],
                    ]"
                    :total="36000"
                    checkoutUrl="/checkout"
                />
            </div>

        </div>
    </section>

</main>
@endsection

@extends('layouts.app')

@section('title', 'Nasi Rames - Kantin 1 - PNC')

@section('content')
<main class="min-h-screen bg-base-100 pb-12">

    <x-breadcrumb
        class="pt-8 pb-4"
        :links="[
            ['label' => 'Beranda', 'url' => '/'],
            ['label' => 'Kantin 1', 'url' => '/kantin'],
            ['label' => 'Nasi Rames']
        ]"
    />

    <section class="px-4 sm:px-10 md:px-16 lg:px-24 pb-6">
        <div class="max-w-8xl mx-auto">
            <h1 class="text-2xl sm:text-4xl font-bold text-base-content mb-1">Detail Menu</h1>
            <p class="text-base-content/70 text-sm sm:text-base font-medium">Lihat dulu menu yang mau dipesan</p>
        </div>
    </section>

    <section class="px-4 sm:px-10 md:px-16 lg:px-24">
        <div class="max-w-8xl mx-auto flex flex-col lg:flex-row gap-8 lg:gap-12 items-start">

            <div class="w-full lg:max-w-sm xl:max-w-md shrink-0">
                <div class="bg-vanilla-custard-50 border border-base-content/20 rounded-3xl p-5 shadow-sm">

                    <div class="w-full aspect-square rounded-2xl overflow-hidden mb-5 bg-base-200">
                        <img
                            src="{{ asset('assets/food/Nasi Rames.jpg') }}"
                            onerror="this.src='https://ui-avatars.com/api/?name=Nasi+Rames&background=random'"
                            alt="Nasi Rames"
                            class="w-full h-full object-cover"
                        />
                    </div>

                    <p class="text-sm text-base-content/60 font-medium mb-1">Kantin 1</p>
                    <h2 class="text-2xl sm:text-3xl font-bold text-base-content mb-2">Nasi Rames</h2>
                    <p class="text-lg font-bold text-base-content mb-3">Rp. 10.000</p>

                    <p class="text-sm text-base-content/70 font-medium leading-relaxed mb-4">
                        Perpaduan nasi hangat dengan aneka lauk pilihan dan sambal khas yang bikin makan jadi puas dan nagih.
                    </p>

                    <div class="flex flex-wrap gap-2 mb-5">
                        @foreach(['Nasi', 'Sayur', 'Ayam'] as $tag)
                            <span class="bg-base-200 text-base-content/70 text-xs font-bold px-3 py-1 rounded-full border border-base-content/10">
                                {{ $tag }}
                            </span>
                        @endforeach
                    </div>

                    <div class="mb-5" x-data="{ qty: 1, harga: 10000 }">
                        <x-user.quantity-control x-model="qty" />

                        <div class="mt-5 pt-4 border-t border-base-content/10">
                            <p class="text-sm font-bold text-base-content/60 mb-1">Total :</p>
                            <p class="text-2xl sm:text-3xl font-extrabold text-base-content"
                               x-text="'Rp. ' + (qty * harga).toLocaleString('id-ID')">
                                Rp. 10.000
                            </p>
                        </div>

                        <button class="btn bg-fern-700 hover:bg-fern-800 text-white border-none w-full mt-5 rounded-2xl font-bold text-sm shadow-lg active:scale-95 transition-all"
                                x-on:click="
                                    let cart = JSON.parse(localStorage.getItem('cart') || '{}');
                                    let name = 'Nasi Rames';
                                    if (cart[name]) {
                                        cart[name].qty += qty;
                                    } else {
                                        cart[name] = { qty: qty, price: harga };
                                    }
                                    localStorage.setItem('cart', JSON.stringify(cart));
                                    window.dispatchEvent(new Event('cart-updated'));
                                    alert('Berhasil ditambahkan ke keranjang!');
                                ">
                            Tambah ke Keranjang
                        </button>
                    </div>

                </div>
            </div>

            <div class="w-full min-w-0">

                <div class="mb-8">
                    <x-user.info-bar rating="4.7" estimasi="10 - 15 Menit" :populer="true" :tersedia="true" />
                </div>

                <h2 class="text-xl sm:text-2xl font-bold text-base-content mb-4">Menu Lain dari Kantin 1</h2>

                <div class="flex gap-4 overflow-x-auto pb-4 snap-x snap-mandatory scrollbar-hide -mx-4 px-4 sm:-mx-10 sm:px-10 md:-mx-16 md:px-16 lg:mx-0 lg:px-0">
                    @foreach(range(1, 6) as $i)
                        <div class="snap-start shrink-0 w-64 sm:w-72">
                            <x-user.foodcard />
                        </div>
                    @endforeach
                </div>

            </div>

        </div>
    </section>

</main>
@endsection

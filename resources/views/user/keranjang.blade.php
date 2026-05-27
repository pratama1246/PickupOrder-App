@extends('layouts.app')

@section('title', 'Keranjang Belanja - PNC')

@section('content')
<main class="min-h-screen bg-base-100 pb-16">

    <x-breadcrumb
        class="pt-8 pb-4"
        maxWidth="max-w-7xl"
        :links="[
            ['label' => 'Beranda', 'url' => '/'],
            ['label' => 'Keranjang Belanja']
        ]"
    />

    <section class="px-4 sm:px-10 md:px-16 lg:px-24 pb-6">
        <div class="max-w-7xl mx-auto">
            <h1 class="text-2xl sm:text-4xl font-bold text-base-content mb-1">Keranjang Belanja</h1>
            <p class="text-base-content/70 text-sm sm:text-base font-medium">Silahkan periksa detail pesanan Anda</p>
        </div>
    </section>

    <section class="px-4 sm:px-10 md:px-16 lg:px-24">
        
        <form id="checkout-prepare-form" action="{{ route('checkout.prepare') }}" method="POST" class="hidden">
            @csrf
        </form>

        <div class="max-w-7xl mx-auto flex flex-col lg:flex-row gap-6 lg:gap-8 items-start">
            <div class="w-full lg:flex-1 min-w-0 space-y-5">
                @forelse ($grouped as $canteenId => $data)
                    <div class="bg-vanilla-custard-50 border border-base-content/20 rounded-3xl p-5 sm:p-6 shadow-sm">

                        <div class="flex items-center justify-between mb-5">
                            <h2 class="text-lg sm:text-xl font-bold text-base-content">{{ $data['canteen_name'] }}</h2>
                            <span class="text-sm font-bold text-base-content/60"><span>{{ count($data['items']) }}</span> Pesanan</span>
                        </div>

                        <div class="space-y-3 mb-5">
                            @foreach ($data['items'] as $item)
                                <x-user.cart-item
                                    :itemId="$item['menu_id']"
                                    :image="$item['image'] ? asset('storage/' . $item['image']) : asset('assets/food/' . strtolower($item['name']) . '.jpg')"
                                    :name="$item['name']"
                                    :description="$item['description'] ?? null"
                                    :price="$item['price']"
                                    :quantity="$item['quantity']"
                                />
                            @endforeach
                        </div>

                        <textarea
                            name="notes[{{ $canteenId }}]"
                            form="checkout-prepare-form"
                            rows="2"
                            placeholder="Catatan untuk kantin {{ $data['canteen_name'] }} (Opsional)"
                            class="textarea textarea-bordered w-full rounded-2xl text-sm font-medium border-base-content/20 bg-white focus:outline-none focus:border-base-content/40 resize-none placeholder:text-base-content/40"
                        >{{ session('checkout_notes')[$canteenId] ?? '' }}</textarea>

                    </div>
                @empty
                    <div class="bg-white border border-base-content/20 rounded-3xl p-8 text-center shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-16 h-16 mx-auto text-base-content/30 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        <h3 class="text-lg font-bold text-base-content mb-1">Keranjang Belanja Kosong</h3>
                        <p class="text-sm text-base-content/60 mb-5 font-medium">Anda belum menambahkan makanan atau minuman.</p>
                        <a href="/pesan" class="btn bg-fern-700 hover:bg-fern-800 text-white border-none px-6 rounded-xl font-bold text-sm shadow-md active:scale-95 transition-all">
                            Mulai Cari Menu
                        </a>
                    </div>
                @endforelse
            </div>

            @if(count($grouped) > 0)
                <div class="w-full lg:w-80 xl:w-96 shrink-0">
                    <x-user.cart-summary
                        :canteens="$grouped"
                        :total="$total"
                        isSubmit="true"
                    />
                </div>
            @endif
        </div>
    </section>

</main>
@endsection

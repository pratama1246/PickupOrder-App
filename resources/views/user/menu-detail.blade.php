@extends('layouts.app')

@section('title', 'Nasi Rames - Kantin 1 - PNC')

@section('content')
<main class="min-h-screen bg-base-100 pb-12">

    <x-breadcrumb
        class="pt-8 pb-4"
        maxWidth="max-w-7xl"
        :links="[
            ['label' => 'Beranda', 'url' => '/'],
            ['label' => $menu->canteen->name, 'url' => route('canteen.show', $menu->canteen_id)],
            ['label' => $menu->name]
        ]"
    />

    <section class="px-4 sm:px-10 md:px-16 lg:px-24 pb-6">
        <div class="max-w-7xl mx-auto">
            <h1 class="text-2xl sm:text-4xl font-bold text-base-content mb-1">Detail Menu</h1>
            <p class="text-base-content/70 text-sm sm:text-base font-medium">Lihat dulu menu yang mau dipesan</p>
        </div>
    </section>

    <section class="px-4 sm:px-10 md:px-16 lg:px-24">
        <div class="max-w-7xl mx-auto flex flex-col lg:flex-row gap-8 lg:gap-12 items-start">

            <div class="w-full lg:max-w-sm xl:max-w-md shrink-0">
                <div class="bg-vanilla-custard-50 border border-base-content/20 rounded-3xl p-5 shadow-sm">

                    <div class="w-full aspect-square rounded-2xl overflow-hidden mb-5 bg-base-200">
                        <img
                            src="{{ $menu->image ? asset('storage/' . $menu->image) : asset('assets/food/Nasi Rames.jpg') }}"
                            onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($menu->name) }}&background=random'"
                            alt="{{ $menu->name }}"
                            class="w-full h-full object-cover"
                        />
                    </div>

                    <p class="text-sm text-base-content/60 font-medium mb-1">{{ $menu->canteen->name }}</p>
                    <h2 class="text-2xl sm:text-3xl font-bold text-base-content mb-2">{{ $menu->name }}</h2>
                    <p class="text-lg font-semibold text-base-content/80 mb-3">{{ $menu->formatted_price }}</p>

                    <p class="text-sm text-base-content/70 font-medium leading-relaxed mb-4">
                        {{ $menu->description ?? 'Belum ada deskripsi untuk menu ini.' }}
                    </p>

                    <div class="mb-5" x-data="{ qty: 1, harga: {{ $menu->price }} }">
                        <x-user.quantity-control x-model="qty" />

                        <div class="mt-5 pt-4 border-t border-base-content/10">
                            <p class="text-sm font-bold text-base-content/60 mb-1">Total :</p>
                            <p class="text-2xl sm:text-3xl font-bold text-fern-700"
                               x-text="'Rp. ' + (qty * harga).toLocaleString('id-ID')">
                                {{ $menu->formatted_price }}
                            </p>
                        </div>

                        <!-- Real Cart form to CartController@store -->
                        <form action="{{ route('cart.store') }}" method="POST" class="mt-5">
                            @csrf
                            <input type="hidden" name="menu_id" value="{{ $menu->id }}">
                            <input type="hidden" name="quantity" x-bind:value="qty">
                            <button type="submit" class="btn bg-fern-700 hover:bg-fern-800 text-white border-none w-full rounded-xl font-bold text-sm shadow-lg active:scale-95 transition-all {{ !$menu->isInStock() ? 'btn-disabled opacity-50' : '' }}">
                                {{ $menu->isInStock() ? 'Tambah ke Keranjang' : 'Stok Habis' }}
                            </button>
                        </form>
                    </div>

                </div>
            </div>

            <div class="w-full min-w-0">

                <div class="mb-8">
                    <x-user.info-bar rating="4.8" estimasi="10 - 15 Menit" :populer="true" :tersedia="$menu->isInStock()" />
                </div>

                <h2 class="text-xl sm:text-2xl font-bold text-base-content mb-4">Menu Lain dari {{ $menu->canteen->name }}</h2>

                <div class="flex gap-4 overflow-x-auto pb-4 snap-x snap-mandatory scrollbar-hide -mx-4 px-4 sm:-mx-10 sm:px-10 md:-mx-16 md:px-16 lg:mx-0 lg:px-0">
                    @forelse($otherMenus as $otherMenu)
                        <div class="snap-start shrink-0 w-80 sm:w-72">
                            <x-foodcard 
                                :id="$otherMenu->id" 
                                :name="$otherMenu->name"
                                :canteenName="$otherMenu->canteen->name"
                                :description="$otherMenu->description"
                                :price="$otherMenu->formatted_price"
                                :image="$otherMenu->image ? asset('storage/' . $otherMenu->image) : null"
                                rating="4.8"
                                :actionUrl="route('menu.show', ['canteenId' => $otherMenu->canteen_id, 'id' => $otherMenu->id])"
                            />
                        </div>
                    @empty
                        <p class="text-base-content/60 font-medium">Belum ada menu lain.</p>
                    @endforelse
                </div>

            </div>

        </div>
    </section>

</main>
@endsection

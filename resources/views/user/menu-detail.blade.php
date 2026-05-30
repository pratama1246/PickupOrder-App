@extends('layouts.app')

@section('title', 'Nasi Rames - Kantin 1 - PNC')

@section('content')
    <main class="min-h-screen bg-base-100 pb-12">

        <x-breadcrumb class="pt-8 pb-4" maxWidth="max-w-7xl" :links="[
            ['label' => 'Beranda', 'url' => '/'],
            ['label' => $menu->canteen->name, 'url' => route('canteen.show', $menu->canteen_id)],
            ['label' => $menu->name],
        ]" />

        <section class="px-3 sm:px-10 md:px-16 lg:px-24 pb-6">
            <div class="max-w-7xl mx-auto">
                <h1 class="text-2xl sm:text-4xl font-bold text-base-content mb-1">Detail Menu</h1>
                <p class="text-base-content/70 text-sm sm:text-base font-medium">Lihat dulu menu yang mau dipesan</p>
            </div>
        </section>

        <section class="px-3 sm:px-10 md:px-16 lg:px-24">
            <div class="max-w-7xl mx-auto flex flex-col md:flex-row gap-6 md:gap-8 lg:gap-12 items-start">

                <div class="w-full md:max-w-xs lg:max-w-sm xl:max-w-md shrink-0">
                    <div class="bg-vanilla-custard-50 border border-base-content/20 rounded-3xl p-5 shadow-sm">

                        <div class="w-full aspect-square rounded-2xl overflow-hidden mb-5 bg-base-200 relative">
                            <div class="absolute inset-0 flex items-center justify-center text-fern-700/40">
                                <span class="loading loading-bars loading-lg"></span>
                            </div>
                            <img src="{{ $menu->image ? asset('storage/' . $menu->image) : 'https://ui-avatars.com/api/?name=' . urlencode($menu->name) . '&background=random' }}"
                                onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($menu->name) }}&background=random'"
                                alt="{{ $menu->name }}" class="w-full h-full object-cover relative z-10" />
                        </div>

                        <p class="text-sm text-base-content/60 font-medium mb-1">{{ $menu->canteen->name }}</p>
                        <h2 class="text-2xl sm:text-3xl font-bold text-base-content mb-2">{{ $menu->name }}</h2>
                        <p class="text-lg font-semibold text-base-content/80 mb-3">{{ $menu->formatted_price }}</p>

                        <p class="text-sm text-base-content/70 font-medium leading-relaxed mb-4">
                            {{ $menu->description ?? 'Belum ada deskripsi untuk menu ini.' }}
                        </p>

                        {{-- 
                          Menggunakan Alpine.js untuk menghitung total harga pesanan secara real-time di sisi klien 
                          berdasarkan kuantitas (qty) yang dipilih sebelum dikirimkan ke server.
                        --}}
                        <div class="mb-5" x-data="{ qty: 1, harga: {{ $menu->price }} }">
                            <x-user.quantity-control x-model="qty" />

                            <div class="mt-5 pt-4 border-t border-base-content/10">
                                <p class="text-sm font-bold text-base-content/60 mb-1">Total :</p>
                                <p class="text-2xl sm:text-3xl font-bold text-fern-700"
                                    x-text="'Rp. ' + (qty * harga).toLocaleString('id-ID')">
                                    {{ $menu->formatted_price }}
                                </p>
                            </div>

                            <form action="{{ route('cart.store') }}" method="POST" class="mt-5">
                                @csrf
                                <input type="hidden" name="menu_id" value="{{ $menu->id }}">
                                <input type="hidden" name="quantity" x-bind:value="qty">
                                @if (!$menu->canteen || !$menu->canteen->is_open)
                                    <button type="button" disabled
                                        class="btn btn-disabled opacity-50 cursor-not-allowed w-full rounded-xl font-bold text-sm h-12 min-h-0">
                                        Kantin Tutup
                                    </button>
                                @else
                                    <button type="submit"
                                        class="btn bg-fern-700 hover:bg-fern-800 text-white border-none w-full rounded-xl font-bold text-sm shadow-lg active:scale-95 transition-all {{ !$menu->isInStock() ? 'btn-disabled opacity-50' : '' }}">
                                        {{ $menu->isInStock() ? 'Tambah ke Keranjang' : 'Stok Habis' }}
                                    </button>
                                @endif
                            </form>
                        </div>

                    </div>
                </div>

                <div class="w-full min-w-0">

                    <div class="mb-8">
                        <x-user.info-bar :rating="number_format($menu->average_rating, 1)" estimasi="10 - 15 Menit" :populer="$menu->recent_orders_count >= 5" :tersedia="$menu->isInStock()" />
                    </div>

                    <h2 class="text-xl sm:text-2xl font-bold text-base-content mb-4">Menu Lain dari
                        {{ $menu->canteen->name }}</h2>

                    <div
                        class="flex gap-4 overflow-x-auto pb-4 snap-x snap-mandatory scrollbar-hide -mx-3 px-3 sm:-mx-10 sm:px-10 md:mx-0 md:px-0">
                        @forelse($otherMenus as $otherMenu)
                            <div class="snap-start shrink-0 w-80 sm:w-72">
                                <x-foodcard :id="$otherMenu->id" :name="$otherMenu->name" :canteenName="$otherMenu->canteen->name" :description="$otherMenu->description"
                                    :price="$otherMenu->formatted_price" :image="$otherMenu->image ? asset('storage/' . $otherMenu->image) : null" :rating="number_format($otherMenu->average_rating, 1)" :actionUrl="route('menu.show', [
                                        'canteenId' => $otherMenu->canteen_id,
                                        'id' => $otherMenu->id,
                                    ])" />
                            </div>
                        @empty
                            <p class="text-base-content/60 font-medium">Belum ada menu lain.</p>
                        @endforelse
                    </div>

                    <div class="mt-12">
                        <h2 class="text-xl sm:text-2xl font-bold text-base-content mb-4">Ulasan Pembeli</h2>

                        <div class="space-y-4">
                            @forelse($reviews as $review)
                                <div class="bg-white border border-base-content/10 rounded-2xl p-4 sm:p-5 shadow-sm">
                                    <div class="flex items-start justify-between gap-4 mb-3">
                                        <div class="flex items-center gap-3">
                                            <div class="avatar">
                                                <div
                                                    class="w-10 h-10 rounded-full ring ring-fern-100 ring-offset-base-100 ring-offset-2">
                                                    <img src="{{ $review->reviewer_avatar }}" />
                                                </div>
                                            </div>
                                            <div>
                                                <h4 class="font-bold text-sm sm:text-base text-base-content">
                                                    {{ $review->reviewer_name }}</h4>
                                                <p class="text-xs text-base-content/50">
                                                    {{ $review->created_at->diffForHumans() }}</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-0.5 bg-vanilla-custard-50 px-2 py-1 rounded-lg">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-amber-400"
                                                viewBox="0 0 20 20" fill="currentColor">
                                                <path
                                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                            <span
                                                class="font-bold text-sm text-base-content">{{ $review->rating }}.0</span>
                                        </div>
                                    </div>
                                    @if ($review->comment)
                                        <p class="text-sm text-base-content/80 leading-relaxed">{{ $review->comment }}</p>
                                    @endif
                                </div>
                            @empty
                                <div
                                    class="p-6 text-center bg-vanilla-custard-50 border border-base-content/20 rounded-3xl">
                                    <p class="text-base-content/60 font-medium">Belum ada ulasan untuk menu ini.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                </div>

            </div>
        </section>

    </main>
@endsection

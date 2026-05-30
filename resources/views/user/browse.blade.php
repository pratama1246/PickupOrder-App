@extends('layouts.app')

@section('title', 'Pesan Makanan - PNC')

@section('content')
    {{-- 
      Menggunakan fungsi pembantu initLiveSearch() dari app.js yang memanfaatkan 
      Alpine.js untuk memantau perubahan input kata kunci, kategori, dan ID kantin. 
      Setiap perubahan memicu request AJAX didebounce ke server untuk me-refresh 
      kontainer '#pesanan-results' secara real-time.
    --}}
    <main class="min-h-screen bg-base-100 pb-12" id="pesanan-container" x-data="initLiveSearch('#pesanan-results')">
        <x-breadcrumb :links="[['label' => 'Beranda', 'url' => '/'], ['label' => 'Pesan']]" />

        <section class="px-3 sm:px-10 md:px-16 lg:px-24 pb-6">
            <div class="max-w-8xl mx-auto">
                <h1 class="text-2xl sm:text-4xl font-bold text-base-content mb-2">Pesan Makanan</h1>
                <p class="text-base-content/70 text-sm sm:text-lg font-medium">Eksplorasi seluruh kantin dan menu yang
                    tersedia hari ini.</p>
            </div>
        </section>

        <section class="px-3 sm:px-10 md:px-16 lg:px-24 mb-8">
            <div class="max-w-8xl mx-auto flex flex-col sm:flex-row items-stretch sm:items-center gap-3 sm:gap-4">

                <form action="" method="GET" @submit.prevent class="w-full sm:max-w-md">
                    <label
                        class="input input-bordered flex items-center w-full shadow-sm rounded-3xl border-base-content/40 focus-within:border-base-content input-md gap-2 px-2">
                        <div
                            class="bg-base-content/10 text-base-content rounded-full w-8 h-8 flex items-center justify-center shrink-0 pointer-events-none select-none">
                            <svg x-show="!loading" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-base-content" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <circle cx="11" cy="11" r="8" />
                                <path d="m21 21-4.35-4.35" />
                            </svg>
                            <span x-show="loading" class="loading loading-spinner loading-xs text-base-content" style="display: none;"></span>
                        </div>
                        <input type="text" x-model="keyword" class="grow text-sm sm:text-base font-medium pl-1"
                            placeholder="Cari kantin atau menu favoritmu..." />
                    </label>
                </form>

                <select x-model="category"
                    class="select select-bordered select-md rounded-full border-base-content/40 w-full sm:w-auto sm:min-w-56 focus:outline-none text-sm sm:text-base sm:pl-8">
                    <option value="" disabled class="hidden">Kategori Menu</option>
                    <option value="">Semua Kategori</option>
                    <option value="Makanan">Makanan</option>
                    <option value="Minuman">Minuman</option>
                    <option value="Camilan">Camilan</option>
                </select>

                <select x-model="canteen"
                    class="select select-bordered select-md rounded-full border-base-content/40 w-full sm:w-auto sm:min-w-56 focus:outline-none text-sm sm:text-base sm:pl-8">
                    <option value="" disabled class="hidden">Kantin</option>
                    <option value="">Semua Kantin</option>
                    @if (isset($allCanteens))
                        @foreach ($allCanteens as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    @endif
                </select>

            </div>
        </section>

        <div id="pesanan-results">
            @php
                $allMenus = $canteens->getCollection()->flatMap->menus->take(12);
                $hasCanteens = $canteens->count() > 0;
                $hasMenus = $allMenus->count() > 0;
            @endphp

            @if (!$hasCanteens && !$hasMenus)
                <section class="px-3 sm:px-10 md:px-16 lg:px-24 mb-12">
                    <div class="max-w-8xl mx-auto">
                        <div
                            class="col-span-full p-10 text-center bg-vanilla-custard-50 border border-base-content/25 rounded-3xl mt-4">
                            <div class="flex justify-center mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-base-content/30"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <p class="text-base-content/80 font-bold text-lg sm:text-xl">Menu atau kantin yang kamu cari
                                tidak ada.</p>
                            <p class="text-base-content/50 font-medium text-sm mt-2">Coba gunakan kata kunci lain atau hapus
                                filter kategori.</p>
                        </div>
                    </div>
                </section>
            @else
                @if ($hasCanteens)
                    <section class="px-3 sm:px-10 md:px-16 lg:px-24 mb-12">
                        <div class="max-w-8xl mx-auto">
                            <div class="mb-6 flex justify-between items-end sm:pl-2">
                                <div>
                                    <h2 class="text-xl sm:text-2xl font-bold text-base-content">Daftar Kantin</h2>
                                    <p class="text-base-content/60 text-sm mt-1">Pilih kantin langgananmu.</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                @foreach ($canteens as $canteen)
                                    <x-canteencard :id="$canteen->id" :name="$canteen->name" :image="$canteen->image ? asset('storage/' . $canteen->image) : null" :description="$canteen->description ?? 'Kantin pilihan mahasiswa.'"
                                        :menuCount="$canteen->available_menus_count ?? $canteen->menus->count()" :rating="number_format($canteen->average_rating, 1)" :actionUrl="route('canteen.show', $canteen->id)" actionText="Lihat Kantin" />
                                @endforeach
                            </div>

                            <div class="pt-6">
                                {{ $canteens->links() }}
                            </div>
                        </div>
                    </section>
                @endif

                @if ($hasMenus)
                    <section class="px-3 sm:px-10 md:px-16 lg:px-24 mb-12">
                        <div class="max-w-8xl mx-auto">
                            <div class="mb-6 sm:pl-2">
                                <h2 class="text-xl sm:text-2xl font-bold text-base-content">Semua Menu</h2>
                                <p class="text-base-content/60 text-sm mt-1">Menu lezat dari semua kantin yang buka hari
                                    ini.</p>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
                                @foreach ($allMenus as $menu)
                                    <x-foodcard :id="$menu->id" :name="$menu->name" :canteenName="$menu->canteen->name" :description="$menu->description"
                                        :price="$menu->formatted_price" :image="$menu->image ? asset('storage/' . $menu->image) : null" :rating="number_format($menu->average_rating, 1)" :actionUrl="route('menu.show', [
                                            'canteenId' => $menu->canteen_id,
                                            'id' => $menu->id,
                                        ])" />
                                @endforeach
                            </div>
                        </div>
                    </section>
                @endif
            @endif
        </div>

    </main>
@endsection

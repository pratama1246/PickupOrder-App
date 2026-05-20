@extends('layouts.app')

@section('title', 'Pesan Makanan - PNC')

@section('content')
<main class="min-h-screen bg-base-100 pb-12">
    <x-breadcrumb :links="[
        ['label' => 'Beranda', 'url' => '/'],
        ['label' => 'Pesan']
    ]" />

    <section class="px-4 sm:px-10 md:px-16 lg:px-24 pb-6">
        <div class="max-w-8xl mx-auto">
            <h1 class="text-2xl sm:text-4xl font-bold text-base-content mb-2">Pesan Makanan</h1>
            <p class="text-base-content/70 text-sm sm:text-lg font-medium">Eksplorasi seluruh kantin dan menu yang tersedia hari ini.</p>
        </div>
    </section>

    <section class="px-4 sm:px-10 md:px-16 lg:px-24 mb-8">
        <div class="max-w-8xl mx-auto flex flex-col sm:flex-row items-stretch sm:items-center gap-3 sm:gap-4">
            
            <label class="input input-bordered flex items-center gap-2 w-full sm:max-w-md shadow-sm rounded-full border-base-content/40 focus-within:border-base-content input-md sm:pl-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-base-content/50" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                </svg>
                <input type="search" class="grow text-sm sm:text-base font-medium pl-1" placeholder="Cari kantin atau menu favoritmu..." />
            </label>

            <select class="select select-bordered select-md rounded-full border-base-content/40 w-full sm:w-auto sm:min-w-56 focus:outline-none font-medium text-sm sm:text-base sm:pl-8">
                <option disabled selected>Kategori Menu</option>
                <option>Semua Kategori</option>
                <option>Nasi</option>
                <option>Ayam</option>
                <option>Sayur</option>
                <option>Minuman</option>
            </select>

            <select class="select select-bordered select-md rounded-full border-base-content/40 w-full sm:w-auto sm:min-w-56 focus:outline-none font-medium text-sm sm:text-base sm:pl-8">
                <option disabled selected>Semua Kantin</option>
                <option>Kantin 1</option>
                <option>Kantin 2</option>
                <option>Kantin 3</option>
            </select>

        </div>
    </section>

    <section class="px-4 sm:px-10 md:px-16 lg:px-24 mb-12">
        <div class="max-w-8xl mx-auto">
            <div class="mb-6 flex justify-between items-end sm:pl-2">
                <div>
                    <h2 class="text-xl sm:text-2xl font-bold text-base-content">Daftar Kantin</h2>
                    <p class="text-base-content/60 text-xs sm:text-sm mt-1">Pilih kantin langgananmu.</p>
                </div>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                @forelse ($canteens as $canteen)
                    <x-canteencard :id="$canteen->id" :name="$canteen->name" :image="$canteen->image ? asset('storage/' . $canteen->image) : null" :description="$canteen->description ?? 'Kantin pilihan mahasiswa.'"
                        :menuCount="$canteen->available_menus_count ?? $canteen->menus->count()" :actionUrl="route('canteen.show', $canteen->id)" actionText="Lihat Kantin" />
                @empty
                    <div
                        class="col-span-full p-8 text-center bg-vanilla-custard-50 border border-base-content/25 rounded-3xl">
                        <p class="text-base-content/60 font-medium">Belum ada kantin terdaftar atau buka.</p>
                    </div>
                @endforelse
            </div>

            <div class="pt-6">
                {{ $canteens->links() }}
            </div>
        </div>
    </section>

    <section class="px-4 sm:px-10 md:px-16 lg:px-24 mb-12">
        <div class="max-w-8xl mx-auto">
            <div class="mb-6 sm:pl-2">
                <h2 class="text-xl sm:text-2xl font-bold text-base-content">Semua Menu</h2>
                <p class="text-base-content/60 text-xs sm:text-sm mt-1">Menu lezat dari semua kantin yang buka hari ini.</p>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
                @php
                    $allMenus = $canteens->getCollection()->flatMap->menus->take(12);
                @endphp
                @forelse ($allMenus as $menu)
                    <x-foodcard :id="$menu->id" :name="$menu->name" :canteenName="$menu->canteen->name" :description="$menu->description"
                        :price="$menu->formatted_price" :image="$menu->image ? asset('storage/' . $menu->image) : null" rating="4.8" :actionUrl="route('menu.show', ['canteenId' => $menu->canteen_id, 'id' => $menu->id])" />
                @empty
                    <div
                        class="col-span-full p-8 text-center bg-vanilla-custard-50 border border-base-content/25 rounded-3xl">
                        <p class="text-base-content/60 font-medium">Belum ada menu tersedia dari kantin-kantin tersebut.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

</main>
@endsection

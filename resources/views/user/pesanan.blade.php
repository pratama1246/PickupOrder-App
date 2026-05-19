@extends('layouts.app')

@section('title', 'Pesan Makanan - PNC')

@section('content')
<main class="min-h-screen bg-base-100 pb-12">
    {{-- Breadcrumb --}}
    <section class="px-4 sm:px-10 md:px-16 lg:px-24 pt-8 pb-4">
        <div class="max-w-8xl mx-auto flex items-center gap-2 sm:gap-3 text-xs sm:text-sm font-bold">
            <a href="/" class="text-base-content/50 hover:text-base-content">Beranda</a>
            <span class="text-base-content/40 text-base sm:text-lg leading-none mb-1">»</span>
            <span class="text-base-content">Pesan</span>
        </div>
    </section>

    {{-- Header Section --}}
    <section class="px-4 sm:px-10 md:px-16 lg:px-24 pb-6">
        <div class="max-w-8xl mx-auto">
            <h1 class="text-2xl sm:text-4xl font-bold text-base-content mb-2">Pesan Makanan</h1>
            <p class="text-base-content/70 text-sm sm:text-lg font-medium">Eksplorasi seluruh kantin dan menu yang tersedia hari ini.</p>
        </div>
    </section>

    {{-- Filter and Search Section --}}
    <section class="px-4 sm:px-10 md:px-16 lg:px-24 mb-8">
        <div class="max-w-8xl mx-auto flex flex-col sm:flex-row items-stretch sm:items-center gap-3 sm:gap-4">
            
            {{-- Search Bar --}}
            <label class="input input-bordered flex items-center gap-2 w-full sm:max-w-md shadow-sm rounded-full border-base-content/40 focus-within:border-base-content input-md sm:pl-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-base-content/50" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                </svg>
                <input type="search" class="grow text-sm sm:text-base font-medium pl-1" placeholder="Cari kantin atau menu favoritmu..." />
            </label>

            {{-- Filter Kategori --}}
            <select class="select select-bordered select-md rounded-full border-base-content/40 w-full sm:w-auto sm:min-w-56 focus:outline-none font-medium text-sm sm:text-base sm:pl-8">
                <option disabled selected>Kategori Menu</option>
                <option>Semua Kategori</option>
                <option>Nasi</option>
                <option>Ayam</option>
                <option>Sayur</option>
                <option>Minuman</option>
            </select>

            {{-- Filter Kantin --}}
            <select class="select select-bordered select-md rounded-full border-base-content/40 w-full sm:w-auto sm:min-w-56 focus:outline-none font-medium text-sm sm:text-base sm:pl-8">
                <option disabled selected>Semua Kantin</option>
                <option>Kantin 1</option>
                <option>Kantin 2</option>
                <option>Kantin 3</option>
            </select>

        </div>
    </section>

    {{-- DAFTAR KANTIN --}}
    <section class="px-4 sm:px-10 md:px-16 lg:px-24 mb-12">
        <div class="max-w-8xl mx-auto">
            <div class="mb-6 flex justify-between items-end sm:pl-2">
                <div>
                    <h2 class="text-xl sm:text-2xl font-bold text-base-content">Daftar Kantin</h2>
                    <p class="text-base-content/60 text-xs sm:text-sm mt-1">Pilih kantin langgananmu.</p>
                </div>
            </div>
            
            {{-- Kantin Grid (Responsive) --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <x-user.canteencard />
                <x-user.canteencard />
                <x-user.canteencard />
                <x-user.canteencard />
                <x-user.canteencard />
                <x-user.canteencard />
            </div>
        </div>
    </section>

    {{-- DAFTAR MENU --}}
    <section class="px-4 sm:px-10 md:px-16 lg:px-24 mb-12">
        <div class="max-w-8xl mx-auto">
            <div class="mb-6 sm:pl-2">
                <h2 class="text-xl sm:text-2xl font-bold text-base-content">Semua Menu</h2>
                <p class="text-base-content/60 text-xs sm:text-sm mt-1">Menu lezat dari semua kantin yang buka hari ini.</p>
            </div>
            
            {{-- Menu Grid (Responsive) --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
                <x-user.foodcard />
                <x-user.foodcard />
                <x-user.foodcard />
                <x-user.foodcard />
                <x-user.foodcard />
                <x-user.foodcard />
                <x-user.foodcard />
                <x-user.foodcard />
            </div>

            {{-- Load More Button --}}
            <div class="flex justify-center mt-10">
                <button class="btn bg-[#d9d9d9] hover:bg-gray-400 text-black border-none px-8 py-2 min-h-0 h-auto rounded-full font-bold text-sm">
                    Muat Lebih Banyak
                </button>
            </div>
        </div>
    </section>

</main>
@endsection

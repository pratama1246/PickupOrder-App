@extends('layouts.app')

@section('title', 'Riwayat Pesanan - PNC')

@section('content')
<main class="min-h-screen bg-base-100 pb-12">
    
    <x-breadcrumb 
        class="pt-8 pb-4"
        :links="[
            ['label' => 'Beranda', 'url' => '/'],
            ['label' => 'Riwayat']
        ]" 
    />

    <section class="px-4 sm:px-10 md:px-16 lg:px-24 pb-6">
        <div class="max-w-8xl">
            <h1 class="text-2xl sm:text-4xl font-bold text-base-content mb-2">Riwayat Pesanan</h1>
            <p class="text-base-content/70 text-sm sm:text-lg font-medium">Menampilkan daftar pesanan yang pernah dibuat pengguna.</p>
        </div>
    </section>

    <section class="px-4 sm:px-10 md:px-16 lg:px-24 mb-8">
        <div class="max-w-8xl flex flex-col sm:flex-row items-stretch sm:items-center gap-3 sm:gap-4">
            <button class="btn btn-md bg-base-200 hover:bg-base-300 text-base-content text-sm font-bold border-none rounded-full px-6 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
                Filter By:
            </button>
            
            <form action="" method="GET" class="w-full sm:max-w-md relative">
                <label class="input input-bordered flex items-center w-full shadow-sm rounded-3xl border-base-content/40 focus-within:border-base-content input-md pr-12">
                    <input type="search" name="search" class="grow text-sm sm:text-base font-medium pl-2" placeholder="Cari riwayat pesanan..." value="{{ request('search') }}" />
                </label>
                <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 btn btn-circle btn-sm bg-fern-700 hover:bg-fern-800 text-white border-none min-h-0 w-8 h-8 transition-all duration-200 active:scale-95 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                    </svg>
                </button>
            </form>
        </div>
    </section>

    <section class="px-4 sm:px-10 md:px-16 lg:px-24">
        <div class="max-w-8xl">
            <div class="max-w-4xl space-y-6">
            @forelse ($orders as $order)
                <x-user.order-card :order="$order" />
            @empty
                <div class="p-8 text-center bg-vanilla-custard-50 border border-base-content/25 rounded-3xl">
                    <p class="text-base-content/60 font-medium">Belum ada riwayat pesanan.</p>
                </div>
            @endforelse

            <div class="pt-4">
                {{ $orders->links() }}
            </div>
            </div>
        </div>
    </section>
</main>
@endsection

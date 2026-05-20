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
            
            <label class="input input-bordered flex items-center gap-2 w-full sm:max-w-md shadow-sm rounded-full border-base-content/40 focus-within:border-base-content input-md sm:pl-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-base-content/50" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                </svg>
                <input type="search" class="grow text-sm sm:text-base font-medium pl-1" placeholder="Cari riwayat pesanan..." />
            </label>
        </div>
    </section>

    <section class="px-4 sm:px-10 md:px-16 lg:px-24">
        <div class="max-w-8xl">
            <div class="max-w-4xl space-y-6">
            @forelse ($orders as $order)
                <div class="bg-vanilla-custard-50 border border-base-content/30 rounded-3xl p-4 sm:p-10 shadow-sm">
                    
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 sm:gap-4 mb-6">
                        <div>
                            <h2 class="text-lg sm:text-2xl font-bold text-base-content mb-1">No. Orderan : {{ $order->order_code }}</h2>
                            <p class="text-xs sm:text-sm font-medium text-base-content">Waktu Pickup: {{ $order->pickup_time->format('H:i, d M Y') }}</p>
                        </div>
                        <x-status-badge :status="$order->status_label" />
                    </div>
                    
                    <div class="bg-white border border-base-content/30 rounded-2xl sm:rounded-3xl p-4 sm:p-6 mb-6">
                        <div class="flex justify-between items-center mb-4 sm:mb-6">
                            <h3 class="text-xl sm:text-2xl font-bold text-base-content">{{ $order->canteen->name }}</h3>
                            <span class="text-xs sm:text-sm font-bold text-base-content">{{ $order->items->sum('qty') }} Item</span>
                        </div>

                        @foreach ($order->items as $item)
                            <x-user.order-item 
                                :image="$item->menu && $item->menu->image ? asset('storage/' . $item->menu->image) : asset('assets/food/es teh.jpg')"
                                :name="$item->menu->name ?? 'Menu Dihapus'"
                                :description="$item->menu->description ?? ''"
                                :price="$item->menu ? $item->menu->formatted_price : 'Rp ' . number_format($item->price, 0, ',', '.')"
                                :quantity="$item->qty"
                                variant="card"
                            />
                        @endforeach
                    </div>

                    <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mt-6 sm:mt-8">
                        <div>
                            <p class="text-xs text-base-content/60 font-bold uppercase">Total Pembayaran</p>
                            <p class="text-xl sm:text-2xl font-black text-fern-700">{{ $order->formatted_total }}</p>
                        </div>
                        <div class="flex gap-3 w-full sm:w-auto">
                            <a href="{{ route('order.show', $order->id) }}" class="btn bg-base-300 hover:bg-base-400 text-base-content border-none w-full sm:w-auto px-8 py-2 min-h-0 h-auto rounded-md font-bold text-sm text-center">
                                Detail
                            </a>
                            @if ($order->status == 'menunggu' || $order->status == 'dimasak' || $order->status == 'siap_diambil')
                                <a href="{{ route('order.queue', $order->id) }}" class="btn bg-fern-700 hover:bg-fern-800 text-white border-none w-full sm:w-auto px-8 py-2 min-h-0 h-auto rounded-md font-bold text-sm text-center">
                                    Pantau Antrian
                                </a>
                            @endif
                        </div>
                    </div>

                </div>
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

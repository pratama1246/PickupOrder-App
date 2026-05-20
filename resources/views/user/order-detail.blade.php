@extends('layouts.app')

@section('title', 'Detail Pesanan - PNC')

@section('content')
<main class="min-h-screen bg-base-100 pb-12">
    
    <x-breadcrumb 
        class="pt-8 pb-4"
        :links="[
            ['label' => 'Beranda', 'url' => '/'],
            ['label' => 'Riwayat', 'url' => '/riwayat'],
            ['label' => 'Order No. PNC-123455478836']
        ]" 
    />

    <section class="px-4 sm:px-10 md:px-16 lg:px-24 pb-6">
        <div class="max-w-8xl flex flex-col sm:flex-row sm:justify-between sm:items-end gap-4">
            <div>
                <h1 class="text-2xl sm:text-4xl font-bold text-base-content mb-2">Detail Pesanan</h1>
                <p class="text-base-content/70 text-sm sm:text-lg font-medium">Menampilkan detail dari daftar pesanan yang pernah dibuat pengguna.</p>
            </div>
        </div>
    </section>

    <section class="px-4 sm:px-10 md:px-16 lg:px-24">
        <div class="max-w-8xl">
            <div class="max-w-4xl space-y-6">
            
            <div class="bg-vanilla-custard-50 border border-base-content/30 rounded-3xl p-4 sm:p-8 shadow-sm">
                
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 sm:gap-4 border-b border-base-content/20 pb-6 mb-6">
                    <div>
                        <h2 class="text-lg sm:text-2xl font-bold text-base-content mb-1">No. Order : {{ $order->order_code }}</h2>
                        <x-status-badge :status="$order->status_label" />
                    </div>
                    @if ($order->status == 'menunggu' || $order->status == 'dimasak' || $order->status == 'siap_diambil')
                        <a href="{{ route('order.queue', $order->id) }}" class="btn bg-fern-700 hover:bg-fern-800 text-white border-none rounded-lg font-bold text-sm w-full sm:w-auto text-center flex items-center justify-center">
                            Pantau Antrian
                        </a>
                    @endif
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">
                    <div class="bg-white rounded-2xl p-4 border border-base-content/10 shadow-sm">
                        <p class="text-xs text-base-content/50 uppercase font-bold mb-1">Kantin</p>
                        <p class="font-bold text-base-content text-sm">{{ $order->canteen->name }}</p>
                    </div>
                    <div class="bg-white rounded-2xl p-4 border border-base-content/10 shadow-sm">
                        <p class="text-xs text-base-content/50 uppercase font-bold mb-1">Metode Pembayaran</p>
                        <p class="font-bold text-base-content text-sm">Tunai / QRIS di Kantin</p>
                    </div>
                    <div class="bg-white rounded-2xl p-4 border border-base-content/10 shadow-sm">
                        <p class="text-xs text-base-content/50 uppercase font-bold mb-1">Waktu Pickup</p>
                        <p class="font-bold text-base-content text-sm">{{ $order->pickup_time->format('H:i, d M Y') }}</p>
                    </div>
                    <div class="bg-white rounded-2xl p-4 border border-base-content/10 shadow-sm">
                        <p class="text-xs text-base-content/50 uppercase font-bold mb-1">Status Sesi</p>
                        <p class="font-bold text-base-content text-sm">Pesanan {{ ucfirst($order->status) }}</p>
                    </div>
                </div>
                
                <div class="bg-white border border-base-content/30 rounded-2xl sm:rounded-3xl p-4 sm:p-6 mb-6">
                    <div class="flex justify-between items-center border-b border-base-content/10 pb-4 mb-4">
                        <h3 class="text-lg sm:text-xl font-bold text-base-content">Detail Item</h3>
                        <span class="text-xs sm:text-sm font-extrabold text-base-content">{{ $order->items->sum('qty') }} Item</span>
                    </div>

                    @foreach ($order->items as $item)
                        <x-user.order-item 
                            :image="$item->menu && $item->menu->image ? asset('storage/' . $item->menu->image) : asset('assets/food/es teh.jpg')"
                            :name="$item->menu->name ?? 'Menu Dihapus'"
                            :description="$item->menu->description ?? ''"
                            :price="$item->menu ? $item->menu->formatted_price : 'Rp ' . number_format($item->price, 0, ',', '.')"
                            :quantity="$item->qty"
                            variant="list"
                        />
                    @endforeach
                </div>

                <div class="mb-6">
                    <p class="text-sm font-bold text-base-content mb-2">Catatan untuk kantin</p>
                    <div class="bg-white border border-base-content/20 rounded-xl p-4 text-sm text-base-content/70">
                        {{ $order->notes ?? 'Tidak ada catatan.' }}
                    </div>
                </div>

                <div class="flex justify-between items-center border-t border-base-content/20 pt-6">
                    <h3 class="text-lg sm:text-xl font-bold text-base-content">Total Belanja</h3>
                    <p class="text-2xl sm:text-3xl font-extrabold text-fern-700">{{ $order->formatted_total }}</p>
                </div>

            </div>
        </div>
    </div>
</section>
</main>
@endsection

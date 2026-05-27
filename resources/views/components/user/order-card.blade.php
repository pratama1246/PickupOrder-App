@props([
    'order'
])

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

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mt-6 sm:mt-8">
        <div>
            <p class="text-xs text-base-content/60 font-bold uppercase">Total Pembayaran</p>
            <p class="text-xl sm:text-2xl font-black text-fern-700">{{ $order->formatted_total }}</p>
        </div>
        <div class="flex gap-3 w-full sm:w-auto">
            @if (in_array($order->status, ['selesai', 'dibatalkan']))
                <a href="{{ route('order.show', $order->id) }}" class="btn bg-base-300 hover:bg-base-400 text-base-content border-none w-full sm:w-auto px-8 min-h-0 h-11 rounded-xl font-bold text-sm text-center flex items-center justify-center active:scale-95 transition-all">
                    Detail
                </a>
            @else
                <a href="{{ route('order.show', $order->id) }}" class="btn bg-fern-700 hover:bg-fern-800 text-white border-none w-full sm:w-auto px-8 min-h-0 h-11 rounded-xl font-bold text-sm text-center flex items-center justify-center active:scale-95 transition-all">
                    Pantau Antrian
                </a>
            @endif
        </div>
    </div>

</div>

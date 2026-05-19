@props([
    'canteens'    => [],
    'total'       => 0,
    'checkoutUrl' => '/checkout',
])

<div {{ $attributes->merge(['class' => 'bg-vanilla-custard-50 border border-base-content/20 rounded-3xl p-5 sm:p-6 shadow-sm lg:sticky lg:top-24']) }}>

    <h3 class="text-lg sm:text-xl font-bold text-base-content mb-5">Ringkasan Belanja</h3>

    <div class="space-y-3 mb-5">
        @forelse($canteens as $canteen)
            <div class="flex items-center justify-between gap-3">
                <div class="flex items-center gap-2 min-w-0">
                    <span class="w-1.5 h-1.5 rounded-full bg-base-content/40 shrink-0"></span>
                    <span class="text-sm font-medium text-base-content truncate">
                        {{ $canteen['name'] }} (<span x-text="totalQty">{{ $canteen['itemCount'] }}</span> Item)
                    </span>
                </div>
                <span class="text-sm font-bold text-base-content whitespace-nowrap shrink-0">
                    Rp. <span x-text="totalPrice.toLocaleString('id-ID')">{{ number_format($canteen['subtotal'], 0, ',', '.') }}</span>
                </span>
            </div>
        @empty
            <p class="text-sm text-base-content/50 font-medium">Keranjang masih kosong.</p>
        @endforelse
    </div>

    <div class="border-t border-base-content/15 mb-5"></div>

    <div class="mb-6">
        <p class="text-sm font-bold text-base-content/60 mb-1">Total</p>
        <p class="text-2xl sm:text-3xl font-extrabold text-base-content">
            Rp. <span x-text="totalPrice.toLocaleString('id-ID')">{{ number_format($total, 0, ',', '.') }}</span>,00
        </p>
    </div>

    <a href="{{ $checkoutUrl }}"
       :class="totalQty === 0 ? 'btn-disabled opacity-50 pointer-events-none' : ''"
       class="btn bg-fern-700 hover:bg-fern-800 text-white border-none w-full rounded-2xl font-bold text-sm shadow-lg active:scale-95 transition-all">
        Bayar Sekarang
    </a>

</div>

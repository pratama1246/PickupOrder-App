@props([
    'canteens' => [],
    'total' => 0,
    'checkoutUrl' => '/checkout',
    'isSubmit' => false,
])

{{-- 
  Komponen Ringkasan Keranjang Belanja:
  - Menyediakan tampilan panel pembayaran / rekapitulasi belanja per kantin secara ringkas.
  - Berintegrasi dengan fungsi Alpine.js di halaman induk ('getCanteenTotal', 'getGrandTotal') 
    untuk menghitung dan memformat nilai harga secara real-time pada sisi klien.
  - Memanfaatkan reaktivitas Alpine.js untuk menonaktifkan tombol submit secara otomatis 
    ketika grand total bernilai nol (tidak ada item terpilih).
  - Mendukung penyesuaian tipe tombol melalui prop 'isSubmit' (mengirimkan form 'checkout-prepare-form' 
    atau bertindak sebagai tautan redireksi biasa).
--}}

<div
    {{ $attributes->merge(['class' => 'bg-vanilla-custard-50 border border-base-content/20 rounded-3xl p-5 sm:p-6 shadow-sm lg:sticky lg:top-24']) }}>

    <h3 class="text-lg sm:text-xl font-bold text-base-content mb-5">Ringkasan Belanja</h3>

    <div class="space-y-3 mb-5">
        @forelse($canteens as $canteenId => $canteen)
            <div class="flex items-center justify-between gap-3">
                <div class="flex items-center gap-2 min-w-0">
                    <span class="w-1.5 h-1.5 rounded-full bg-base-content/40 shrink-0"></span>
                    <span class="text-sm font-medium text-base-content truncate">
                        {{ $canteen['canteen_name'] }} (<span>{{ count($canteen['items']) }}</span> Item)
                    </span>
                </div>
                <span class="text-sm font-bold text-base-content whitespace-nowrap shrink-0"
                    x-text="'Rp. ' + getCanteenTotal({{ $canteenId }}).toLocaleString('id-ID')">
                    Rp. {{ number_format(array_sum(array_column($canteen['items'], 'subtotal')), 0, ',', '.') }}
                </span>
            </div>
        @empty
            <p class="text-sm text-base-content/50 font-medium">Keranjang masih kosong.</p>
        @endforelse
    </div>

    <div class="border-t border-base-content/15 mb-5"></div>

    <div class="mb-6">
        <p class="text-sm font-bold text-base-content/60 mb-1">Total</p>
        <p class="text-2xl sm:text-3xl font-extrabold text-base-content"
            x-text="'Rp. ' + getGrandTotal().toLocaleString('id-ID') + ',00'">
            Rp. {{ number_format($total, 0, ',', '.') }},00
        </p>
    </div>

    @if ($isSubmit)
        <button type="submit" form="checkout-prepare-form"
            class="{{ $total == 0 ? 'btn-disabled opacity-50 pointer-events-none' : '' }} btn w-full bg-fern-700 hover:bg-fern-800 text-white border-none rounded-xl font-bold text-sm shadow-lg active:scale-95 transition-all h-12 min-h-0"
            :class="getGrandTotal() === 0 ? 'btn-disabled opacity-50 pointer-events-none' : ''">
            Bayar Sekarang
        </button>
    @else
        <a href="{{ $checkoutUrl }}"
            class="{{ $total == 0 ? 'btn-disabled opacity-50 pointer-events-none' : '' }} btn w-full bg-fern-700 hover:bg-fern-800 text-white border-none rounded-xl font-bold text-sm shadow-lg active:scale-95 transition-all flex items-center justify-center h-12 min-h-0"
            :class="getGrandTotal() === 0 ? 'btn-disabled opacity-50 pointer-events-none' : ''">
            Bayar Sekarang
        </a>
    @endif

</div>

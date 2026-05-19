@props([
    'image'       => '',
    'name'        => '',
    'description' => null,
    'price'       => 0,
    'quantity'    => 1,
    'itemId'      => null,
])

<div class="bg-white border border-base-content/20 rounded-2xl p-4 sm:p-5"
     x-data="{ qty: {{ (int) $quantity }}, unitPrice: {{ (int) $price }} }">

    <div class="flex gap-4 items-start">

        {{-- Gambar --}}
        <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-xl overflow-hidden bg-base-200 shrink-0 border border-base-content/10">
            <img
                src="{{ $image }}"
                onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($name) }}&background=random'"
                alt="{{ $name }}"
                class="w-full h-full object-cover"
            />
        </div>

        {{-- Konten kanan gambar --}}
        <div class="flex-1 min-w-0 flex flex-col gap-2">

            {{-- Nama + Deskripsi --}}
            <div>
                <h4 class="font-bold text-sm sm:text-base text-base-content leading-tight">{{ $name }}</h4>
                @if($description)
                    <p class="text-xs text-base-content/60 font-medium mt-0.5 leading-snug">{{ $description }}</p>
                @endif
            </div>

            {{-- Quantity + Delete + Harga sejajar --}}
            <div class="flex items-center justify-between gap-2 flex-wrap">

                {{-- Quantity Control --}}
                <div class="flex items-center gap-3 border border-base-content/25 rounded-full px-3 py-1.5 bg-base-100 w-fit">
                    <button type="button"
                            class="w-5 h-5 flex items-center justify-center text-base-content/70 hover:text-fern-700 transition-colors active:scale-90"
                            x-on:click="qty = Math.max(1, qty - 1)">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4"/>
                        </svg>
                    </button>
                    <span class="w-4 text-center font-bold text-sm text-base-content select-none" x-text="qty">{{ $quantity }}</span>
                    <button type="button"
                            class="w-5 h-5 flex items-center justify-center text-base-content/70 hover:text-fern-700 transition-colors active:scale-90"
                            x-on:click="qty = Math.min(99, qty + 1)">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                        </svg>
                    </button>
                </div>

                {{-- Harga + Delete --}}
                <div class="flex items-center gap-3">
                    <p class="font-bold text-sm sm:text-base text-base-content whitespace-nowrap"
                       x-text="'Rp. ' + (qty * unitPrice).toLocaleString('id-ID') + ',00'">
                        Rp. {{ number_format($price * $quantity, 0, ',', '.') }},00
                    </p>

                    <button type="button"
                            class="w-7 h-7 flex items-center justify-center text-base-content/35 hover:text-red-500 transition-colors active:scale-90"
                            @if($itemId) data-item-id="{{ $itemId }}" @endif>
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>

            </div>

        </div>
    </div>
</div>

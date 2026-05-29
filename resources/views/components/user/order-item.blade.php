@props([
    'image' => '',
    'name' => '',
    'description' => null,
    'price' => '',
    'quantity' => 1,
    'variant' => 'card', // 'card' or 'list'
])

{{-- 
  Komponen Item Detail Pesanan:
  - Menyajikan rincian data menu (gambar, nama, deskripsi, kuantitas order, harga) secara baris tunggal.
  - Mendukung dua varian visual ('card' untuk card mandiri berbingkai, atau 'list' untuk baris daftar tipis dengan pemisah garis bawah).
  - Melakukan kompilasi kelas gaya Tailwind secara otomatis di PHP block berdasarkan varian terpilih.
  - Memanfaatkan indikator loading bar yang otomatis dihapus lewat manipulasi DOM mini ('onload="this.previousElementSibling?.remove()"') 
    ketika gambar asli selesai diunduh oleh peramban.
--}}

@php
    $isCard = $variant === 'card';
    $wrapperClasses = $isCard
        ? 'border border-base-content/30 rounded-xl p-3 mb-3 sm:mb-4'
        : 'border-b border-base-content/10 last:border-0 py-3 mb-2';

    $imageSizeClasses = $isCard
        ? 'w-12 h-12 sm:w-20 sm:h-20 rounded-lg sm:rounded-xl'
        : 'w-12 h-12 sm:w-16 sm:h-16 rounded-lg sm:rounded-xl';

    $titleClasses = $isCard ? 'text-base sm:text-xl font-bold' : 'text-sm sm:text-base font-bold';

    $priceClasses = $isCard
        ? 'text-base sm:text-xl font-bold text-base-content'
        : 'text-xs sm:text-sm font-semibold text-base-content/80';
@endphp

<div class="{{ $wrapperClasses }} flex items-center gap-3 sm:gap-4">
    <div
        class="{{ $imageSizeClasses }} bg-base-200 overflow-hidden shrink-0 shadow-sm border border-base-content/5 relative">
        <div class="absolute inset-0 flex items-center justify-center text-fern-700/40">
            <span class="loading loading-bars loading-sm"></span>
        </div>
        <img src="{{ $image }}" onload="this.previousElementSibling?.remove()"
            onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($name) }}&background=random'; this.onerror=null;"
            alt="{{ $name }}" class="w-full h-full object-cover relative z-10">
    </div>
    <div class="flex-1 min-w-0">
        <h4 class="{{ $titleClasses }} text-base-content leading-tight">
            {{ $name }} @if ($quantity > 1)
                (x{{ $quantity }})
            @endif
        </h4>
        @if ($description)
            <p class="text-xs text-base-content/60 leading-tight mt-1 font-medium line-clamp-1">{{ $description }}</p>
        @endif
        <p class="{{ $priceClasses }} mt-1">{{ $price }}</p>
    </div>
</div>

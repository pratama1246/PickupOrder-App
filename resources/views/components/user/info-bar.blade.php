@props([
    'rating' => '4.7',
    'estimasi' => '10 - 15 Menit',
    'populer' => true,
    'tersedia' => true,
])

{{-- 
  Komponen Info Bar Menu Makanan/Kantin:
  - Menyajikan ringkasan parameter status (rating, estimasi waktu penyajian, popularitas, ketersediaan stok) secara sejajar.
  - Menampilkan ikon penunjuk tren popularitas secara kondisional berdasarkan nilai boolean prop 'populer'.
  - Memetakan warna indikator bulat kecil secara dinamis: hijau untuk stok yang tersedia ('tersedia') 
    dan merah pudar untuk stok habis.
--}}

<div
    {{ $attributes->merge(['class' => 'flex flex-wrap items-center gap-3 sm:gap-4 bg-base-200 rounded-2xl px-4 py-3 text-xs sm:text-sm font-bold text-base-content']) }}>

    <span class="flex items-center gap-1.5">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-yellow-400" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
        </svg>
        {{ $rating }}
    </span>

    <span class="text-base-content/30 font-light">|</span>

    <span class="flex items-center gap-1.5">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-base-content/60" fill="none" viewBox="0 0 24 24"
            stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10" />
            <polyline points="12 6 12 12 16 14" />
        </svg>
        {{ $estimasi }}
    </span>

    @if ($populer)
        <span class="text-base-content/30 font-light">|</span>

        <span class="flex items-center gap-1.5">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-fern-600" fill="none" viewBox="0 0 24 24"
                stroke="currentColor" stroke-width="2">
                <polyline points="23 6 13.5 15.5 8.5 10.5 1 18" />
                <polyline points="17 6 23 6 23 12" />
            </svg>
            Lagi Populer
        </span>
    @endif

    <span class="text-base-content/30 font-light">|</span>

    <span class="flex items-center gap-1.5">
        <span class="w-2.5 h-2.5 rounded-full {{ $tersedia ? 'bg-fern-500' : 'bg-red-400' }}"></span>
        {{ $tersedia ? 'Tersedia' : 'Habis' }}
    </span>

</div>

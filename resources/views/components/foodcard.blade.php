@props([
    'id' => null,
    'name' => 'Menu Name',
    'canteenName' => 'Canteen Name',
    'description' => 'Description here.',
    'price' => 'Rp. 0',
    'image' => null,
    'rating' => '4.7',
    'actionUrl' => '#',
    'stock' => 1,
    'isCanteenOpen' => true,
])

{{-- 
  Komponen Card Menu Makanan Global:
  - Mengimplementasikan tampilan ganda responsive:
    - Mode mobile (lebar layar < 640px): Tampilan berorientasi baris/horizontal yang ringkas.
    - Mode desktop (lebar layar >= 640px): Tampilan berorientasi kolom/vertical card standar.
  - Menggunakan teknik stretched-link ('absolute inset-0 z-10') agar seluruh card dapat berinteraksi 
    dengan link detail menu tanpa merusak aksesibilitas HTML5.
  - Mendukung lazy loading gambar serta penanganan fallback URL otomatis (menggunakan ui-avatars.com) 
    via atribut 'onerror' untuk menjamin kestabilan layout visual jika berkas gambar menu kosong.
--}}

@php
    $imageUrl = $image ? $image : 'https://ui-avatars.com/api/?name=' . urlencode($name) . '&background=random';
@endphp

<div class="card relative bg-base-100 w-full h-full flex flex-col shadow-sm rounded-2xl overflow-hidden border border-base-200 {{ ($stock <= 0 || !$isCanteenOpen) ? 'grayscale opacity-70 border-base-content/10 bg-base-200/30' : '' }}">
    <a href="{{ $actionUrl }}" class="absolute inset-0 z-10" aria-label="Lihat {{ $name }}"></a>

    <div class="flex sm:hidden gap-4 p-4 h-full">

        <div class="w-28 h-28 rounded-2xl overflow-hidden shrink-0 bg-base-200 relative">
            <div class="absolute inset-0 flex items-center justify-center text-fern-700/40">
                <span class="loading loading-bars loading-md"></span>
            </div>
            <img src="{{ $imageUrl }}" alt="{{ $name }}" loading="lazy"
                onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($name) }}&background=random'"
                class="w-full h-full object-cover relative z-0" />
        </div>

        <div class="flex-1 min-w-0 flex flex-col justify-between py-0.5">
            <div>
                <div class="flex items-start justify-between gap-1 mb-0.5">
                    <h2 class="font-bold text-sm text-base-content leading-tight line-clamp-1">
                        {{ $name }}</h2>
                    <div class="flex items-center gap-0.5 bg-base-200 px-1.5 py-0.5 rounded-md shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 text-yellow-400" viewBox="0 0 24 24"
                            fill="currentColor">
                            <path
                                d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                        </svg>
                        <span class="font-semibold text-xs">{{ $rating }}</span>
                    </div>
                </div>

                <p class="text-xs text-base-content/55 font-medium mb-1 line-clamp-1">{{ $canteenName }}</p>

                <p class="text-xs text-base-content/65 leading-snug line-clamp-1">
                    {{ $description }}
                </p>
            </div>

            <div class="flex items-center justify-between mt-2 relative z-20">
                <span class="font-extrabold text-sm text-base-content">{{ $price }}</span>
                @if (isset($action))
                    <div class="relative z-20">{{ $action }}</div>
                @elseif (!$isCanteenOpen)
                    <button disabled
                        class="btn btn-disabled bg-base-content/10 text-base-content/40 border-none h-7 min-h-0 rounded-md font-bold text-xs px-3 shadow-sm flex items-center justify-center relative z-20">
                        Kantin Tutup
                    </button>
                @elseif ($stock <= 0)
                    <button disabled
                        class="btn btn-disabled bg-gray-200 text-gray-400 border-none h-7 min-h-0 rounded-md font-bold text-xs px-3 shadow-sm flex items-center justify-center relative z-20">
                        Stok Habis
                    </button>
                @else
                    <a href="{{ $actionUrl }}"
                        class="btn bg-fern-700 text-white border-none h-7 min-h-0 rounded-md font-bold text-xs px-3 shadow-sm flex items-center justify-center transition-all duration-200 active:scale-95 relative z-20">
                        Pesan
                    </a>
                @endif
            </div>
        </div>
    </div>

    <figure class="hidden sm:block overflow-hidden w-full aspect-video bg-base-200 relative">
        <div class="absolute inset-0 flex items-center justify-center text-fern-700/40">
            <span class="loading loading-bars loading-lg"></span>
        </div>
        <img src="{{ $imageUrl }}" alt="{{ $name }}" loading="lazy"
            onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($name) }}&background=random'"
            class="w-full h-full object-cover relative z-0" />
    </figure>
    <div class="hidden sm:flex flex-col flex-1 card-body p-4">
        <div class="flex justify-between items-start gap-2">
            <h2 class="font-bold text-base text-base-content leading-tight line-clamp-2">
                {{ $name }}</h2>
            <div class="flex items-center gap-0.5 bg-base-200 px-2 py-1 rounded-lg shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-yellow-400" viewBox="0 0 24 24"
                    fill="currentColor">
                    <path
                        d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                </svg>
                <span class="font-semibold text-sm">{{ $rating }}</span>
            </div>
        </div>

        <p class="text-sm text-base-content/60 font-medium mt-1">{{ $canteenName }}</p>

        <p class="text-sm text-base-content/70 mt-1 line-clamp-2 leading-relaxed">
            {{ $description }}
        </p>

        <div class="flex justify-between items-center mt-auto pt-3 relative z-20">
            <span class="font-bold text-lg text-base-content">{{ $price }}</span>
            @if (isset($action))
                <div class="relative z-20">{{ $action }}</div>
            @elseif (!$isCanteenOpen)
                <button disabled
                    class="btn btn-disabled bg-base-content/10 text-base-content/40 border-none btn-sm rounded-xl font-bold shadow-sm flex items-center justify-center relative z-20">
                    Kantin Tutup
                </button>
            @elseif ($stock <= 0)
                <button disabled
                    class="btn btn-disabled bg-gray-200 text-gray-400 border-none btn-sm rounded-xl font-bold shadow-sm flex items-center justify-center relative z-20">
                    Stok Habis
                </button>
            @else
                <a href="{{ $actionUrl }}"
                    class="btn bg-fern-700 text-white border-none btn-sm rounded-xl font-bold shadow-sm flex items-center justify-center transition-all duration-200 active:scale-95 relative z-20">
                    Pesan
                </a>
            @endif
        </div>
    </div>

</div>

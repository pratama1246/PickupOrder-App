@props([
    'id' => 1,
    'name' => 'Kantin 1',
    'rating' => '4.7',
    'description' => 'Menyediakan berbagai pilihan menu lezat dengan harga terjangkau untuk mahasiswa.',
    'menuCount' => '25',
    'time' => '07.00–16.00',
    'image' => null,
    'actionUrl' => route('canteen.index'),
    'actionText' => 'Lihat Menu',
])

{{-- 
  Komponen Card Kantin Global:
  - Menyusun detail informasi kantin (nama, rating, deskripsi, jumlah menu, jam operasional).
  - Menggunakan teknik stretched-link ('absolute inset-0 z-10') agar seluruh permukaan card 
    bisa diklik secara semantik tanpa mengacaukan struktur markup HTML atau merusak aksesibilitas.
  - Mendukung lazy loading gambar serta penanganan fallback URL otomatis (menggunakan ui-avatars.com) 
    via atribut 'onerror' untuk mengantisipasi gambar kantin yang kosong atau bermasalah saat dimuat.
--}}

@php
    $imagePath = $image ? $image : 'https://ui-avatars.com/api/?name=' . urlencode($name) . '&background=random';
@endphp

<div class="card relative bg-base-100 rounded-3xl shadow-sm p-0 md:p-6 flex flex-col md:flex-row gap-0 md:gap-6 border border-base-200 cursor-pointer">
    <a href="{{ $actionUrl }}" class="absolute inset-0 z-10 rounded-3xl" aria-label="Lihat {{ $name }}"></a>

    <figure
        class="w-auto md:w-56 h-48 md:h-auto overflow-hidden rounded-t-3xl md:rounded-2xl shrink-0 bg-base-200 relative">
        <div class="absolute inset-0 flex items-center justify-center text-fern-700/40">
            <span class="loading loading-bars loading-lg"></span>
        </div>
        <img src="{{ $imagePath }}" loading="lazy"
            onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($name) }}&background=random'"
            class="w-full h-full object-cover relative z-0"
            alt="{{ $name }}" />
    </figure>
    <div class="flex flex-col flex-1 card-body p-6 md:p-2">
        <h2 class="card-title font-bold text-lg md:text-xl text-base-content mb-1">
            {{ $name }}
        </h2>
        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-2xl w-fit mb-3 bg-base-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-yellow-400" viewBox="0 0 24 24"
                fill="currentColor">
                <path
                    d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
            </svg>
            <span class="font-semibold text-sm">{{ $rating }}</span>
        </div>
        <p class="mb-4 text-sm text-base-content/70 leading-relaxed font-medium">
            {{ $description }}
        </p>
        <div class="flex flex-wrap items-center gap-4 text-xs md:text-sm mb-5 text-base-content/65 font-medium">
            <span class="flex items-center gap-1.5">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-base-content/60" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                {{ $menuCount }} Menu
            </span>
            <span class="flex items-center gap-1.5">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-base-content/60" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10" />
                    <polyline points="12 6 12 12 16 14" />
                </svg>
                {{ $time }}
            </span>
        </div>
        <div class="mt-auto flex justify-end gap-3 flex-wrap relative z-20">
            @if (isset($buttons))
                {{ $buttons }}
            @else
                <a href="{{ $actionUrl }}"
                    class="btn bg-fern-700 text-white border-none shadow-md rounded-xl w-full md:w-fit transition-all duration-200 active:scale-95 font-bold text-sm px-6 py-2 min-h-0 h-auto inline-flex items-center justify-center">
                    {{ $actionText }}
                </a>
            @endif
        </div>
    </div>
</div>

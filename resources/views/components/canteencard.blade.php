@props([
    'id' => 1,
    'name' => 'Kantin 1',
    'rating' => '4.7',
    'description' => 'Menyediakan berbagai pilihan menu lezat dengan harga terjangkau untuk mahasiswa.',
    'menuCount' => '25',
    'time' => '07.00–16.00',
    'image' => null,
    'actionUrl' => '/kantin',
    'actionText' => 'Lihat Menu'
])

@php
    $imagePath = $image ? $image : asset('assets/food/es teh.jpg');
@endphp

<div class="card bg-base-100 rounded-xl shadow-sm p-0 md:p-6 flex flex-col md:flex-row gap-0 md:gap-6">
    <figure class="w-auto md:w-56 h-auto overflow-hidden rounded-fit md:rounded-2xl">
        <img src="{{ $imagePath }}" class="w-full h-full object-cover" alt="{{ $name }}" />
    </figure>
    <div class="flex flex-col flex-1 card-body p-6 md:p-2">
        <h2 class="card-title mb-2">
            {{ $name }}
        </h2>
        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg w-fit mb-3 bg-base-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-yellow-400" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
            </svg>
            <span class="font-semibold">{{ $rating }}</span>
        </div>
        <p class="mb-4">
            {{ $description }}
        </p>
        <div class="flex flex-wrap items-center gap-4 text-sm mb-5 text-base-content/80">
            <span class="flex items-center gap-1.5">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-base-content/60" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                {{ $menuCount }} Menu
            </span>
            <span class="flex items-center gap-1.5">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-base-content/60" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                </svg>
                {{ $time }}
            </span>
        </div>
        <div class="mt-auto flex justify-end gap-3 flex-wrap">
            @if (isset($buttons))
                {{ $buttons }}
            @else
                <a href="{{ $actionUrl }}" class="btn bg-fern-700 text-white hover:bg-fern-800 border-none shadow-md rounded-2xl w-full md:w-fit transition-all duration-200 active:scale-95 hover:shadow-lg">
                    {{ $actionText }}
                </a>
            @endif
        </div>
    </div>
</div>

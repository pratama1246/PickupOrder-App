@props([
    'id' => null,
    'name' => 'Menu Name',
    'canteenName' => 'Canteen Name',
    'description' => 'Description here.',
    'price' => 'Rp. 0',
    'image' => null,
    'rating' => '4.7',
    'actionUrl' => '#'
])

@php
    $imageUrl = $image ? $image : asset('assets/food/Nasi Rames.jpg');
@endphp

<div class="card bg-base-100 w-full h-full flex flex-col shadow-sm rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-md hover:scale-[1.01] border border-base-200">

    <div class="flex sm:hidden gap-3 p-3 h-full">

        <div class="w-24 h-24 rounded-xl overflow-hidden shrink-0 bg-base-200">
            <img
                src="{{ $imageUrl }}"
                alt="{{ $name }}"
                class="w-full h-full object-cover"
            />
        </div>

        <div class="flex-1 min-w-0 flex flex-col justify-between py-0.5">
            <div>
                <div class="flex items-start justify-between gap-1 mb-0.5">
                    <h2 class="font-bold text-sm text-base-content leading-tight line-clamp-1">{{ $name }}</h2>
                    <div class="flex items-center gap-0.5 bg-base-200 px-1.5 py-0.5 rounded-md shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 text-yellow-400" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                        <span class="font-semibold text-xs">{{ $rating }}</span>
                    </div>
                </div>

                <p class="text-xs text-base-content/55 font-medium mb-1 line-clamp-1">{{ $canteenName }}</p>

                <p class="text-xs text-base-content/65 leading-snug line-clamp-2">
                    {{ $description }}
                </p>
            </div>

            <div class="flex items-center justify-between mt-2">
                <span class="font-extrabold text-sm text-base-content">{{ $price }}</span>
                @if(isset($action))
                    {{ $action }}
                @else
                    <a href="{{ $actionUrl }}" class="btn bg-fern-700 text-white hover:bg-fern-800 border-none h-7 min-h-0 rounded-xl font-bold text-xs px-3 shadow-sm flex items-center justify-center transition-all duration-200 active:scale-95 hover:shadow-md">
                        Pesan
                    </a>
                @endif
            </div>
        </div>
    </div>

    <figure class="hidden sm:block overflow-hidden w-full aspect-video">
        <img
            src="{{ $imageUrl }}"
            alt="{{ $name }}"
            class="w-full h-full object-cover transition-transform duration-500 hover:scale-105"
        />
    </figure>
    <div class="hidden sm:flex flex-col flex-1 card-body p-4">
        <div class="flex justify-between items-start gap-2">
            <h2 class="font-bold text-base text-base-content leading-tight line-clamp-2">{{ $name }}</h2>
            <div class="flex items-center gap-0.5 bg-base-200 px-2 py-1 rounded-lg shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-yellow-400" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                </svg>
                <span class="font-semibold text-sm">{{ $rating }}</span>
            </div>
        </div>

        <p class="text-sm text-base-content/60 font-medium mt-1">{{ $canteenName }}</p>

        <p class="text-sm text-base-content/70 mt-1 line-clamp-2 leading-relaxed">
            {{ $description }}
        </p>

        <div class="flex justify-between items-center mt-auto pt-3">
            <span class="font-bold text-lg text-base-content">{{ $price }}</span>
            @if(isset($action))
                {{ $action }}
            @else
                <a href="{{ $actionUrl }}" class="btn bg-fern-700 text-white hover:bg-fern-800 border-none btn-sm rounded-2xl font-bold shadow-sm flex items-center justify-center transition-all duration-200 active:scale-95 hover:shadow-md">
                    Pesan
                </a>
            @endif
        </div>
    </div>

</div>

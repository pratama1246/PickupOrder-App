@props([
    'links' => [],
    'compact' => false,
    'maxWidth' => 'max-w-8xl',
])

<section {{ $attributes->merge(['class' => $compact ? 'pt-0 pb-4' : 'px-3 sm:px-10 md:px-16 lg:px-24 pt-8 pb-4']) }}>
    <div class="{{ $maxWidth }} mx-auto flex flex-wrap items-center gap-2 sm:gap-3 text-xs sm:text-sm font-bold">
        @foreach($links as $link)
            @if(!$loop->last)
                <a href="{{ $link['url'] ?? '#' }}" class="text-base-content/50 hover:text-base-content transition-colors">
                    {{ $link['label'] }}
                </a>
                <span class="text-base-content/40 text-base sm:text-lg leading-none mb-1">»</span>
            @else
                <span class="text-base-content">{{ $link['label'] }}</span>
            @endif
        @endforeach
    </div>
</section>

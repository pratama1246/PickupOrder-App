@props([
    'links' => [],
    'compact' => false,
    'maxWidth' => 'max-w-8xl',
])

<section {{ $attributes->merge(['class' => $compact ? 'pt-0 pb-4' : 'px-3 sm:px-10 md:px-16 lg:px-24 pt-8 pb-4']) }}>
    <div
        class="{{ $maxWidth }} mx-auto flex flex-nowrap items-center gap-1.5 sm:gap-2.5 text-xs sm:text-sm font-bold overflow-hidden">
        @foreach ($links as $link)
            @if (!$loop->last)
                <a href="{{ $link['url'] ?? '#' }}"
                    class="text-base-content/50 hover:text-base-content transition-colors truncate max-w-[90px] sm:max-w-none min-w-0 shrink-0">
                    {{ $link['label'] }}
                </a>
                <span class="text-base-content/40 text-base sm:text-lg leading-none mb-0.5 shrink-0 select-none">»</span>
            @else
                <span
                    class="text-base-content truncate max-w-[130px] sm:max-w-none min-w-0 shrink-0">{{ $link['label'] }}</span>
            @endif
        @endforeach
    </div>
</section>

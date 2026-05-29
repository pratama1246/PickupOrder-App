@props([
    'label' => '',
    'value' => '',
    'growth' => null,
    'subtext' => null,
    'iconBg' => 'bg-emerald-50 text-fern-700',
    'valueColor' => 'text-base-content',
    'variant' => 'default',
    'reserveMetaSpace' => false,
])

@php
    $variants = [
        'default' => [
            'card' => 'bg-base-100 border-base-200',
            'label' => 'text-base-content/60',
            'value' => $valueColor,
            'subtext' => 'text-base-content/100',
            'growthPositive' => 'bg-emerald-50 text-fern-700',
            'growthNegative' => 'bg-red-50 text-red-600',
            'iconBg' => $iconBg,
        ],
        'highlight' => [
            'card' => 'bg-linear-to-tl from-emerald-800 to-fern-600 border-fern-100',
            'label' => 'text-white/75',
            'value' => 'text-white',
            'subtext' => 'text-white/70',
            'growthPositive' => 'bg-vanilla-custard-100 text-fern-800',
            'growthNegative' => 'bg-red-50 text-red-600',
            'iconBg' => $iconBg === 'bg-emerald-50 text-fern-700' ? 'bg-vanilla-custard-100 text-fern-800' : $iconBg,
        ],
        'emerald' => [
            'card' => 'bg-linear-to-br from-emerald-50 to-base-100 border-base-200',
            'label' => 'text-base-content/60',
            'value' => $valueColor,
            'subtext' => 'text-base-content/100',
            'growthPositive' => 'bg-emerald-50 text-fern-700',
            'growthNegative' => 'bg-red-50 text-red-600',
            'iconBg' => $iconBg,
        ],
        'vanilla' => [
            'card' => 'bg-linear-to-br from-vanilla-custard-50 to-base-100 border-base-200',
            'label' => 'text-base-content/60',
            'value' => $valueColor,
            'subtext' => 'text-base-content/100',
            'growthPositive' => 'bg-emerald-50 text-fern-700',
            'growthNegative' => 'bg-red-50 text-red-600',
            'iconBg' => $iconBg,
        ],
        'spruce' => [
            'card' => 'bg-linear-to-br from-dark-spruce-50 to-base-100 border-base-200',
            'label' => 'text-base-content/60',
            'value' => $valueColor,
            'subtext' => 'text-base-content/100',
            'growthPositive' => 'bg-emerald-50 text-fern-700',
            'growthNegative' => 'bg-red-50 text-red-600',
            'iconBg' => $iconBg,
        ],
    ];

    $style = $variants[$variant] ?? $variants['default'];
@endphp

<div
    class="{{ $style['card'] }} rounded-xl p-5 shadow-sm border flex justify-between items-start gap-4 min-h-[10.75rem]">
    <div class="flex-1 min-w-0 self-stretch flex flex-col justify-between">
        <h3 class="text-sm font-medium {{ $style['label'] }}">{{ $label }}</h3>

        <div class="flex-1 flex items-center">
            <p class="text-2xl sm:text-3xl font-bold {{ $style['value'] }}">{{ $value }}</p>
        </div>

        @if ($growth !== null || $subtext)
            <div class="flex flex-wrap items-center gap-2 mt-3">
                @if ($growth !== null)
                    @php
                        $isPositive = $growth >= 0;
                        $growthClass = $isPositive ? $style['growthPositive'] : $style['growthNegative'];
                    @endphp
                    <span
                        class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-xs font-bold {{ $growthClass }}">
                        @if ($isPositive)
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941" />
                            </svg>
                            +{{ abs($growth) }}%
                        @else
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.25 6L9 12.75l4.286-4.286a11.948 11.948 0 014.306 6.43l.776 2.898m0 0l3.182-5.514m-3.182 5.514l-5.514-3.182" />
                            </svg>
                            {{ abs($growth) }}%
                        @endif
                    </span>
                @endif
                @if ($subtext)
                    <span class="text-xs font-medium {{ $style['subtext'] }}">{{ $subtext }}</span>
                @endif
            </div>
        @elseif($reserveMetaSpace)
            <div class="h-8 mt-3" aria-hidden="true"></div>
        @endif
    </div>

    @if (isset($icon))
        <div
            class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl flex items-center justify-center shrink-0 [&_svg]:w-7 [&_svg]:h-7 sm:[&_svg]:w-8 sm:[&_svg]:h-8 [&_svg]:stroke-[1.75] {{ $style['iconBg'] }}">
            {{ $icon }}
        </div>
    @endif
</div>

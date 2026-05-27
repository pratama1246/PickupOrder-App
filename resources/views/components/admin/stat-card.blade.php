@props([
    'label' => '',
    'value' => '',
    'growth' => null,
    'subtext' => null,
    'iconBg' => 'bg-emerald-50 text-fern-700',
])

<div class="bg-base-100 rounded-3xl p-5 shadow-sm border border-base-200 flex flex-col justify-between">
    <div class="flex justify-between items-start mb-4">
        <div>
            <h3 class="text-sm font-bold text-base-content/60">{{ $label }}</h3>
            <p class="text-3xl font-extrabold text-base-content mt-1">{{ $value }}</p>
        </div>
        @if(isset($icon))
            <div class="w-12 h-12 rounded-2xl flex items-center justify-center shrink-0 {{ $iconBg }}">
                {{ $icon }}
            </div>
        @endif
    </div>
    
    @if($growth !== null || $subtext)
        <div class="flex items-center gap-2 mt-auto">
            @if($growth !== null)
                @php
                    $isPositive = $growth >= 0;
                    $growthClass = $isPositive ? 'bg-emerald-50 text-fern-700' : 'bg-red-50 text-red-600';
                @endphp
                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-xs font-bold {{ $growthClass }}">
                    @if($isPositive)
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941" />
                        </svg>
                        +{{ abs($growth) }}%
                    @else
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6L9 12.75l4.286-4.286a11.948 11.948 0 014.306 6.43l.776 2.898m0 0l3.182-5.514m-3.182 5.514l-5.514-3.182" />
                        </svg>
                        {{ abs($growth) }}%
                    @endif
                </span>
            @endif
            @if($subtext)
                <span class="text-xs font-medium text-base-content/50">{{ $subtext }}</span>
            @endif
        </div>
    @endif
</div>

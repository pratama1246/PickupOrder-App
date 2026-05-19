@props([
    'image' => '',
    'name' => '',
    'description' => null,
    'price' => '',
    'quantity' => 1,
    'variant' => 'card' // 'card' or 'list'
])

@php
    $isCard = $variant === 'card';
    
    // Classes based on variant
    $wrapperClasses = $isCard 
        ? 'border border-base-content/30 rounded-xl p-3 mb-3 sm:mb-4' 
        : 'border-b border-base-content/10 last:border-0 py-3 mb-2';
        
    $imageSizeClasses = $isCard 
        ? 'w-12 h-12 sm:w-20 sm:h-20 rounded-lg sm:rounded-xl' 
        : 'w-12 h-12 sm:w-16 sm:h-16 rounded-lg sm:rounded-xl';
        
    $titleClasses = $isCard 
        ? 'text-base sm:text-xl font-bold' 
        : 'text-base sm:text-lg font-bold';
        
    $priceClasses = $isCard 
        ? 'text-base sm:text-xl font-bold' 
        : 'text-base sm:text-lg font-bold';
@endphp

<div class="{{ $wrapperClasses }} flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div class="flex items-start sm:items-center gap-3 sm:gap-5">
        <div class="{{ $imageSizeClasses }} bg-base-200 overflow-hidden shrink-0 shadow-sm border border-base-content/5">
            <img src="{{ $image }}" 
                 onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($name) }}&background=random'" 
                 alt="{{ $name }}" 
                 class="w-full h-full object-cover">
        </div>
        <div class="flex-1 mt-0.5 sm:mt-0">
            <h4 class="{{ $titleClasses }} text-base-content leading-tight">
                {{ $name }} @if($quantity > 1) (x{{ $quantity }}) @endif
            </h4>
            @if($description)
                <p class="text-xs sm:text-sm text-base-content/60 leading-tight mt-1 sm:mt-1.5 font-medium">{{ $description }}</p>
            @endif
        </div>
    </div>
    <div class="text-right pl-4 mt-1.5 sm:mt-0 mb-1">
        <p class="{{ $priceClasses }} text-base-content">{{ $price }}</p>
    </div>
</div>

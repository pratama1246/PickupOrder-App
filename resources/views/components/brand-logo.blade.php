@props([
    'variant' => 'dark',     // 'dark' (untuk navbar gelap) | 'light' (untuk footer / background terang)
    'size' => 'md',          // 'sm' | 'md' | 'lg'
    'showText' => true,
])

@php
    $sizeConfig = [
        'sm' => [
            'icon' => 'w-8 h-8',
            'full' => 'h-8 sm:h-9 w-auto',
        ],
        'md' => [
            'icon' => 'w-11 h-11',
            'full' => 'h-10 sm:h-11 w-auto',
        ],
        'lg' => [
            'icon' => 'w-14 h-14',
            'full' => 'h-13 sm:h-14 w-auto',
        ],
    ];

    $cfg = $sizeConfig[$size] ?? $sizeConfig['md'];

    $isDark = $variant === 'dark';
    
    // Pick correct SVG colors based on theme variant
    $textPrimaryColor = $isDark ? '#FFFFFF' : '#131720';
    $textSecondaryColor = $isDark ? '#4ADE80' : '#306939';
    $stemColorStart = $isDark ? '#4ADE80' : '#347B42';
    $stemColorEnd = $isDark ? '#16A34A' : '#1E5228';
    $loopColorStart = $isDark ? '#A3E635' : '#84CC16';
    $loopColorEnd = $isDark ? '#65A30D' : '#558B0E';
    $handleStroke = $isDark ? '#16A34A' : '#2E7D32';
@endphp

<div {{ $attributes->merge(['class' => 'inline-flex items-center select-none group shrink-0']) }}>
    @if($showText)
        {{-- Complete 100% Vector SVG Brand Logo Lockup (Icon + Wordmark in SVG) --}}
        <div class="{{ $cfg['full'] }} shrink-0 transition-transform group-hover:scale-[1.02] flex items-center justify-center">
            <svg class="h-full w-auto" viewBox="0 0 540 120" fill="none" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <linearGradient id="bl-stem-{{ $variant }}-{{ $size }}" x1="80" y1="80" x2="200" y2="450" gradientUnits="userSpaceOnUse">
                        <stop offset="0%" stop-color="{{ $stemColorStart }}" />
                        <stop offset="100%" stop-color="{{ $stemColorEnd }}" />
                    </linearGradient>
                    <linearGradient id="bl-loop-{{ $variant }}-{{ $size }}" x1="160" y1="60" x2="420" y2="340" gradientUnits="userSpaceOnUse">
                        <stop offset="0%" stop-color="{{ $loopColorStart }}" />
                        <stop offset="100%" stop-color="{{ $loopColorEnd }}" />
                    </linearGradient>
                    <filter id="bl-shadow-{{ $variant }}-{{ $size }}" x="-20%" y="-20%" width="140%" height="140%">
                        <feDropShadow dx="0" dy="4" stdDeviation="6" flood-color="{{ $isDark ? '#000000' : '#1E5228' }}" flood-opacity="{{ $isDark ? '0.35' : '0.2' }}" />
                    </filter>
                </defs>

                <!-- Left: Master Logo Icon Mark -->
                <g transform="translate(16, 14) scale(0.22)" filter="url(#bl-shadow-{{ $variant }}-{{ $size }})">
                    <!-- Monogram P Vertical Stem -->
                    <rect x="96" y="64" width="88" height="384" rx="44" fill="url(#bl-stem-{{ $variant }}-{{ $size }})" />

                    <!-- Monogram P Forward Loop / Food Bag Shape -->
                    <path d="M140 64H276C353.32 64 416 126.68 416 204C416 281.32 353.32 344 276 344H140V260H276C306.928 260 332 234.928 332 204C332 173.072 306.928 148 276 148H140V64Z" 
                          fill="url(#bl-loop-{{ $variant }}-{{ $size }})" />

                    <!-- Pure White Inner Cutout -->
                    <path d="M220 148H276C306.928 148 332 173.072 332 204C332 234.928 306.928 260 276 260H220V148Z" 
                          fill="#FFFFFF" />

                    <!-- Bag Handle Arc on top of P -->
                    <path d="M236 148V104C236 90.7452 246.745 80 260 80C273.255 80 284 90.7452 284 104V148" 
                          stroke="{{ $handleStroke }}" stroke-width="16" stroke-linecap="round" />

                    <!-- Fast Pickup Checkmark Accent (Warm Gold) -->
                    <circle cx="276" cy="204" r="18" fill="#FFBF4A" />
                </g>

                <!-- Right: Vector SVG Typography "PickupOrder" -->
                <g transform="translate(132, 0)">
                    <text x="0" y="80" font-family="Poppins, 'Segoe UI', system-ui, -apple-system, sans-serif" font-size="58" font-weight="900" letter-spacing="-1.5">
                        <tspan fill="{{ $textPrimaryColor }}">Pickup</tspan><tspan fill="{{ $textSecondaryColor }}">Order</tspan>
                    </text>
                </g>
            </svg>
        </div>
    @else
        {{-- Standalone Icon Only SVG --}}
        <div class="{{ $cfg['icon'] }} shrink-0 transition-transform group-hover:scale-105 drop-shadow-sm flex items-center justify-center">
            <svg class="w-full h-full" viewBox="0 0 512 512" fill="none" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <linearGradient id="bl-icon-stem-{{ $variant }}-{{ $size }}" x1="80" y1="80" x2="200" y2="450" gradientUnits="userSpaceOnUse">
                        <stop offset="0%" stop-color="{{ $stemColorStart }}" />
                        <stop offset="100%" stop-color="{{ $stemColorEnd }}" />
                    </linearGradient>
                    <linearGradient id="bl-icon-loop-{{ $variant }}-{{ $size }}" x1="160" y1="60" x2="420" y2="340" gradientUnits="userSpaceOnUse">
                        <stop offset="0%" stop-color="{{ $loopColorStart }}" />
                        <stop offset="100%" stop-color="{{ $loopColorEnd }}" />
                    </linearGradient>
                </defs>

                <!-- Monogram P Vertical Stem -->
                <rect x="96" y="64" width="88" height="384" rx="44" fill="url(#bl-icon-stem-{{ $variant }}-{{ $size }})" />

                <!-- Monogram P Forward Loop / Food Bag Shape -->
                <path d="M140 64H276C353.32 64 416 126.68 416 204C416 281.32 353.32 344 276 344H140V260H276C306.928 260 332 234.928 332 204C332 173.072 306.928 148 276 148H140V64Z" 
                      fill="url(#bl-icon-loop-{{ $variant }}-{{ $size }})" />

                <!-- Pure White Inner Cutout -->
                <path d="M220 148H276C306.928 148 332 173.072 332 204C332 234.928 306.928 260 276 260H220V148Z" 
                      fill="#FFFFFF" />

                <!-- Bag Handle Arc on top of P -->
                <path d="M236 148V104C236 90.7452 246.745 80 260 80C273.255 80 284 90.7452 284 104V148" 
                      stroke="{{ $handleStroke }}" stroke-width="16" stroke-linecap="round" />

                <!-- Fast Pickup Checkmark Accent (Warm Gold) -->
                <circle cx="276" cy="204" r="18" fill="#FFBF4A" />
            </svg>
        </div>
    @endif
</div>

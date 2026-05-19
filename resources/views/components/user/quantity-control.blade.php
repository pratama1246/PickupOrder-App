@props([
    'quantity' => 1,
    'min' => 1,
    'max' => 99,
    'name' => 'quantity',
])

<div {{ $attributes->merge(['class' => 'flex items-center gap-3']) }}
    x-data="{ count: {{ $quantity }} }"
    x-modelable="count">

    <button
        type="button"
        class="w-8 h-8 flex items-center justify-center rounded-full border border-base-content/30 hover:border-base-content/60 bg-base-100 transition-colors active:scale-95"
        x-on:click="count = Math.max({{ $min }}, count - 1)"
    >
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-base-content" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4" />
        </svg>
    </button>

    <span class="w-6 text-center font-bold text-base-content text-base select-none" x-text="count">{{ $quantity }}</span>

    <input type="hidden" name="{{ $name }}" :value="count">

    <button
        type="button"
        class="w-8 h-8 flex items-center justify-center rounded-full border border-base-content/30 hover:border-base-content/60 bg-base-100 transition-colors active:scale-95"
        x-on:click="count = Math.min({{ $max }}, count + 1)"
    >
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-base-content" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
        </svg>
    </button>
</div>

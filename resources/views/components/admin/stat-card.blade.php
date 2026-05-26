@props([
    'label' => '',
    'value' => '',
    'valueColor' => '',
])

<div class="bg-base-200 rounded-xl p-4">
    <p class="text-xs font-bold text-base-content/50 uppercase mb-2">{{ $label }}</p>
    <p class="text-2xl font-extrabold {{ $valueColor ?: 'text-base-content' }}">{{ $value }}</p>
</div>

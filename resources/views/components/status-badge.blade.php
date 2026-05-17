@props(['status' => ''])

@php
    $statusLower = strtolower($status);
    $config = [
        'diproses' => [
            'bg' => 'bg-[#ffbd59]',
            'text' => 'text-black',
        ],
        'selesai' => [
            'bg' => 'bg-emerald-200',
            'text' => 'text-emerald-900',
        ],
        'dibatalkan' => [
            'bg' => 'bg-red-200',
            'text' => 'text-red-900',
        ],
        'menunggu' => [
            'bg' => 'bg-blue-200',
            'text' => 'text-blue-900',
        ],
    ];

    $style = $config[$statusLower] ?? ['bg' => 'bg-gray-200', 'text' => 'text-gray-900'];
@endphp

<span {{ $attributes->merge(['class' => "{$style['bg']} {$style['text']} text-xs sm:text-sm font-medium px-3 sm:px-6 py-1.5 sm:py-2.5 rounded-md inline-block shadow-sm"]) }}>
    {{ $status }}
</span>

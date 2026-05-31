@props([
    'quantity' => 1,
    'min' => 1,
    'max' => 99,
    'name' => 'quantity',
])

{{-- 
  Komponen Pengontrol Kuantitas (Quantity Selector):
  - Memanfaatkan fitur Alpine.js 'x-modelable="count"' agar state lokal 'count' dapat disinkronisasikan 
    dan dikontrol secara dua arah oleh model dari parent template/scope.
  - Menerapkan batasan nilai minimum ('min') dan maksimum ('max') secara ketat pada tombol penambah/pengurang di sisi klien.
  - Memasang input bertipe tersembunyi (hidden input) dengan nama yang dapat disesuaikan ('name') 
    agar nilai kuantitas terpilih ikut terkirim secara otomatis saat disubmit dalam form HTML biasa.
--}}
<div {{ $attributes->merge(['class' => 'flex items-center gap-3']) }} x-data="{ count: {{ $quantity }} }" x-modelable="count">

    <button type="button"
        class="w-8 h-8 flex items-center justify-center rounded-full border border-base-content/30 hover:border-base-content/60 bg-base-100 transition-colors active:scale-95 disabled:opacity-30 disabled:cursor-not-allowed"
        :disabled="count <= {{ $min }}"
        x-on:click="count = Math.max({{ $min }}, count - 1)">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-base-content" fill="none" viewBox="0 0 24 24"
            stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4" />
        </svg>
    </button>

    <span class="w-6 text-center font-bold text-base-content text-base select-none"
        x-text="count">{{ $quantity }}</span>

    <input type="hidden" name="{{ $name }}" :value="count">

    <button type="button"
        class="w-8 h-8 flex items-center justify-center rounded-full border border-base-content/30 hover:border-base-content/60 bg-base-100 transition-colors active:scale-95 disabled:opacity-30 disabled:cursor-not-allowed"
        :disabled="count >= {{ $max }}"
        x-on:click="count = Math.min({{ $max }}, count + 1)">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-base-content" fill="none" viewBox="0 0 24 24"
            stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
        </svg>
    </button>
</div>

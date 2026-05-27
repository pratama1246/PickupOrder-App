@props([
    'image'       => '',
    'name'        => '',
    'description' => null,
    'price'       => 0,
    'quantity'    => 1,
    'itemId'      => null,
])

<div class="cart-item-card bg-white border border-base-content/20 rounded-2xl p-4 sm:p-5">
    <div class="flex gap-4 items-center justify-between">
        
        {{-- Kiri: Gambar & Info (Nama, Deskripsi, Harga Numpuk) --}}
        <div class="flex items-center gap-3 sm:gap-4 min-w-0">
            <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-xl overflow-hidden bg-base-200 shrink-0 border border-base-content/10">
                <img
                    src="{{ $image }}"
                    onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($name) }}&background=random'"
                    alt="{{ $name }}"
                    class="w-full h-full object-cover"
                />
            </div>

            <div class="min-w-0">
                <h4 class="font-bold text-sm sm:text-base text-base-content leading-tight">{{ $name }}</h4>
                @if($description)
                    <p class="text-xs text-base-content/60 font-medium mt-0.5 leading-snug">{{ $description }}</p>
                @endif
                <p class="text-xs sm:text-sm font-semibold text-base-content/70 mt-1">
                    Rp. {{ number_format($price * $quantity, 0, ',', '.') }},00
                </p>
            </div>
        </div>

        {{-- Kanan: Pengatur Jumlah (Counter) & Tombol Hapus --}}
        <div class="flex items-center gap-2 sm:gap-4 shrink-0">
            
            <!-- Quantity Controls via Form -->
            <form action="{{ route('cart.update', $itemId) }}" method="POST" class="m-0 p-0 flex items-center gap-1.5 sm:gap-2.5">
                @csrf
                @method('PUT')
                <button type="submit" name="quantity" value="{{ max(1, $quantity - 1) }}"
                        class="w-7 h-7 sm:w-8 sm:h-8 flex items-center justify-center rounded-full border border-base-content/30 hover:border-base-content/60 bg-base-100 transition-colors active:scale-95"
                        {{ $quantity <= 1 ? 'disabled' : '' }}>
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-base-content {{ $quantity <= 1 ? 'opacity-30' : '' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4" />
                    </svg>
                </button>
                
                <span class="w-5 text-center font-bold text-base-content text-sm sm:text-base select-none">{{ $quantity }}</span>
                
                <button type="submit" name="quantity" value="{{ min(20, $quantity + 1) }}"
                        class="w-7 h-7 sm:w-8 sm:h-8 flex items-center justify-center rounded-full border border-base-content/30 hover:border-base-content/60 bg-base-100 transition-colors active:scale-95">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-base-content" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                </button>
            </form>

            <!-- Delete Button -->
            <form action="{{ route('cart.destroy', $itemId) }}" method="POST" class="m-0 p-0">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="w-8 h-8 flex items-center justify-center text-base-content/35 hover:text-red-500 transition-colors active:scale-90">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            </form>

        </div>

    </div>
</div>

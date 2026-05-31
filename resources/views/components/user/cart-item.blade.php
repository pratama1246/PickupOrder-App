@props([
    'image' => '',
    'name' => '',
    'description' => null,
    'price' => 0,
    'quantity' => 1,
    'itemId' => null,
    'cartId' => null,
    'stock' => 1,
])

{{-- 
  Komponen Card Item Keranjang Belanja:
  - Berinteraksi secara dua arah dengan state Alpine.js di halaman induk (seperti array 'items' dan fungsi 'changeQty' / 'toggleItem').
  - Mengimplementasikan klik permukaan luar card ('toggleCard') untuk memicu centang checkbox item 
    dengan filter pengabaian event ('e.target.closest') guna menghindari konflik gelembung event (event bubbling) 
    saat mengklik elemen interaktif internal seperti tombol kuantitas, form, input, dan dialog modal.
  - Memanfaatkan reaktivitas Alpine.js untuk menghitung subtotal harga menu secara instan di sisi klien.
--}}
<div class="cart-item-card border transition-all duration-200 rounded-2xl p-4 sm:p-5 cursor-pointer select-none"
     :class="items[{{ $itemId }}] && items[{{ $itemId }}].stock <= 0 ? 'bg-base-200/50 border-base-content/10 opacity-70 cursor-not-allowed' : (items[{{ $itemId }}] && items[{{ $itemId }}].selected ? 'bg-fern-100/80 border-fern-300 shadow-xs' : 'bg-white border-base-content/20')"
     x-data="{
         toggleCard(e) {
             if (e.target.closest('button') || e.target.closest('form') || e.target.closest('input') || e.target.closest('label') || e.target.closest('dialog')) {
                 return;
             }
             if (this.items[{{ $itemId }}].stock <= 0) {
                 return;
             }
             const cb = $el.querySelector('input[name=\'selected_menu_ids[]\']');
             if (cb) {
                 cb.checked = !cb.checked;
                 cb.dispatchEvent(new Event('change'));
             }
         }
     }"
     @click="toggleCard($event)">
    <div class="flex items-start sm:items-center gap-3">
        <label class="hidden sm:flex items-center justify-center shrink-0 cursor-pointer">
            <input
                type="checkbox"
                name="selected_menu_ids[]"
                value="{{ $cartId ?? $itemId }}"
                form="checkout-prepare-form"
                @if($stock <= 0) disabled @endif
                :disabled="items[{{ $itemId }}].stock <= 0"
                :checked="items[{{ $itemId }}].selected"
                class="checkbox checkbox-sm rounded-md border-base-content/30 checked:bg-fern-700 checked:border-fern-700 checked:text-white focus:ring-0 disabled:opacity-30 disabled:cursor-not-allowed"
                @change="toggleItem({{ $cartId ?? $itemId }}, $event.target.checked)"
            >
        </label>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 flex-1 min-w-0">
        <div class="flex items-center gap-3 sm:gap-4 min-w-0">
            <div
                class="w-16 h-16 sm:w-20 sm:h-20 rounded-xl overflow-hidden bg-base-200 shrink-0 border border-base-content/10">
                <img src="{{ $image }}"
                    onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($name) }}&background=random'"
                    alt="{{ $name }}" class="w-full h-full object-cover" />
            </div>

            <div class="min-w-0 flex-1">
                <div class="flex items-center gap-2 flex-wrap">
                    <h4 class="font-bold text-sm sm:text-base text-base-content leading-tight">{{ $name }}</h4>
                    @if($stock <= 0)
                        <span class="badge bg-red-500 text-white border-none font-bold text-[10px] py-1 px-2 rounded-md shadow-sm shrink-0">Stok Habis</span>
                    @endif
                </div>
                @if ($description)
                    <p class="text-xs text-base-content/60 font-medium mt-0.5 leading-snug line-clamp-1">
                        {{ $description }}</p>
                @endif
                <p class="hidden sm:block text-xs sm:text-sm font-semibold text-base-content/70 mt-1">
                    Rp. <span
                        x-text="(items[{{ $itemId }}].qty * items[{{ $itemId }}].price).toLocaleString('id-ID')">{{ number_format($price * $quantity, 0, ',', '.') }}</span>,00
                </p>
            </div>
        </div>

        <div
            class="flex items-center justify-between sm:justify-end gap-3 sm:gap-4 border-t border-base-content/5 pt-3 sm:pt-0 sm:border-none">
            <div class="block sm:hidden">
                <p class="text-[10px] text-base-content/50 font-bold uppercase tracking-wider mb-0.5">Subtotal</p>
                <p class="text-sm font-bold text-base-content">
                    Rp. <span
                        x-text="(items[{{ $itemId }}].qty * items[{{ $itemId }}].price).toLocaleString('id-ID')">{{ number_format($price * $quantity, 0, ',', '.') }}</span>,00
                </p>
            </div>

            <div class="flex items-center gap-2 sm:gap-4 shrink-0">
                <div class="flex items-center gap-1.5 sm:gap-2.5">
                    <button type="button" @click="changeQty({{ $itemId }}, -1)"
                        class="w-7 h-7 sm:w-8 sm:h-8 flex items-center justify-center rounded-full border border-base-content/30 hover:border-base-content/60 bg-base-100 transition-colors active:scale-95 disabled:opacity-30 disabled:cursor-not-allowed"
                        :disabled="items[{{ $itemId }}].qty <= 1 || items[{{ $itemId }}].stock <= 0"
                        @if($stock <= 0) disabled @endif>
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-base-content"
                            :class="items[{{ $itemId }}].qty <= 1 || items[{{ $itemId }}].stock <= 0 ? 'opacity-30' : ''" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4" />
                        </svg>
                    </button>

                    <span class="w-5 text-center font-bold text-base-content text-sm sm:text-base select-none"
                        x-text="items[{{ $itemId }}].qty">{{ $quantity }}</span>

                    <button type="button" @click="changeQty({{ $itemId }}, 1)"
                        class="w-7 h-7 sm:w-8 sm:h-8 flex items-center justify-center rounded-full border border-base-content/30 hover:border-base-content/60 bg-base-100 transition-colors active:scale-95 disabled:opacity-30 disabled:cursor-not-allowed"
                        :disabled="items[{{ $itemId }}].qty >= 20 || items[{{ $itemId }}].qty >= items[{{ $itemId }}].stock"
                        @if($stock <= 0) disabled @endif>
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-base-content"
                            :class="items[{{ $itemId }}].qty >= 20 || items[{{ $itemId }}].qty >= items[{{ $itemId }}].stock ? 'opacity-30' : ''" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                    </button>
                </div>

                <button type="button"
                    onclick="document.getElementById('delete_modal_{{ $itemId }}').showModal()"
                    class="w-8 h-8 flex items-center justify-center text-red-500 hover:text-red-700 transition-colors active:scale-90">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>

                <x-modal id="delete_modal_{{ $itemId }}" type="warning" title="Hapus Menu Dari Keranjang?">
                    Apakah Anda yakin ingin menghapus menu <strong>{{ $name }}</strong> dari keranjang belanja?
                    <x-slot:footer>
                        <button type="button"
                            onclick="document.getElementById('delete_modal_{{ $itemId }}').close()"
                            class="btn btn-ghost rounded-xl font-bold active:scale-95 transition-all">
                            Batal
                        </button>
                        <form action="{{ route('cart.destroy', $itemId) }}" method="POST" class="m-0 p-0 inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="btn bg-red-600 hover:bg-red-700 text-white border-0 rounded-xl font-bold active:scale-95 transition-all">
                                Ya, Hapus
                            </button>
                        </form>
                    </x-slot:footer>
                </x-modal>
            </div>
        </div>
    </div>
    </div>
</div>

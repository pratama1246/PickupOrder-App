@props([
    'group'
])

@php
    $firstOrder = $group->first();
    $totalGroup = $group->sum('total_price');
    $formattedTotalGroup = 'Rp ' . number_format($totalGroup, 0, ',', '.');
    $csrfToken = csrf_token();
    $retryUrl = route('checkout.retry', $firstOrder->payment_code);
@endphp

{{-- 
  Komponen Card Pesanan Terkelompok (Multi-Kantin):
  - Mengelola tampilan transaksi online tertunda (pending) yang melibatkan lebih dari satu kantin 
    dan dikelompokkan berdasarkan kode pembayaran unik ('payment_code').
  - Menyusun rincian pesanan per kantin dengan meloop subkomponen 'x-user.order-item' di dalam pembungkus masing-masing.
  - Menyediakan tombol bayar yang berintegrasi langsung dengan SDK JavaScript Midtrans Snap ('openSnapGroup') 
    menggunakan 'snap_token' transaksi.
  - Membuka modal konfirmasi pembatalan massal ('cancel_group_modal_...') untuk membatalkan seluruh grup pesanan sekaligus.
--}}

<div class="bg-vanilla-custard-50 border border-amber-300 rounded-3xl p-4 sm:p-8 shadow-sm">
    
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 sm:gap-4 mb-6">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-amber-600" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                </svg>
                <span class="text-xs font-bold text-amber-700 uppercase tracking-wide">Menunggu Pembayaran</span>
            </div>
            <h2 class="text-sm sm:text-xl font-bold text-base-content mb-1 whitespace-nowrap truncate max-w-[240px] sm:max-w-none">No. Transaksi: {{ $firstOrder->payment_code }}</h2>
            <p class="text-xs sm:text-sm font-medium text-base-content/70">Waktu Pickup: {{ $firstOrder->pickup_time->format('H:i, d M Y') }}</p>
        </div>
        <div class="shrink-0">
            <span class="inline-flex items-center gap-1.5 bg-amber-100 border border-amber-300 text-amber-800 font-bold text-xs px-4 py-2 rounded-full">
                <span class="w-2 h-2 bg-amber-500 rounded-full animate-pulse inline-block"></span>
                {{ $group->count() }} Kantin
            </span>
        </div>
    </div>
    
    <div class="space-y-4 mb-6">
        @foreach ($group as $groupedOrder)
            <div class="bg-white border border-base-content/20 rounded-2xl p-4 sm:p-5">
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <h3 class="text-base sm:text-lg font-bold text-base-content">{{ $groupedOrder->canteen->name }}</h3>
                        <p class="text-xs text-base-content/50 font-semibold mt-0.5">No. Order: {{ $groupedOrder->order_code }}</p>
                    </div>
                    <span class="text-xs font-bold text-base-content/60 bg-base-100 border border-base-content/20 px-3 py-1 rounded-full">
                        {{ $groupedOrder->items->sum('qty') }} Item
                    </span>
                </div>
                <div class="space-y-3">
                    @foreach ($groupedOrder->items as $item)
                        <x-user.order-item
                            :image="$item->menu && $item->menu->image ? asset('storage/' . $item->menu->image) : 'https://ui-avatars.com/api/?name=' . urlencode($item->menu->name ?? 'Menu') . '&background=random'"
                            :name="$item->menu->name ?? 'Menu Dihapus'"
                            :description="$item->menu->description ?? ''"
                            :price="$item->menu ? $item->menu->formatted_price : 'Rp ' . number_format($item->price, 0, ',', '.')"
                            :quantity="$item->qty"
                            variant="card"
                        />
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pt-5 border-t border-amber-200">
        <div>
            <p class="text-xs text-base-content/60 font-bold uppercase">Total Pembayaran</p>
            <p class="text-xl sm:text-2xl font-black text-fern-700">{{ $formattedTotalGroup }}</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
            <button 
                type="button" 
                onclick="document.getElementById('cancel_group_modal_{{ $firstOrder->payment_code }}').showModal()"
                class="btn bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 w-full sm:w-auto px-6 min-h-0 h-11 rounded-xl font-bold text-sm transition-all duration-200 active:scale-95"
            >
                Batalkan Semua
            </button>

            <x-modal id="cancel_group_modal_{{ $firstOrder->payment_code }}" type="error" title="Batalkan Semua Pesanan">
                Apakah Anda yakin ingin membatalkan seluruh transaksi ini? Semua pesanan dari {{ $group->count() }} kantin akan ikut dibatalkan secara permanen.
                
                <x-slot:footer>
                    <button type="button" onclick="document.getElementById('cancel_group_modal_{{ $firstOrder->payment_code }}').close()" class="btn btn-ghost rounded-xl font-bold active:scale-95 transition-all">Kembali</button>
                    <form action="{{ route('order.cancel-group', $firstOrder->payment_code) }}" method="POST" class="m-0 p-0 inline-block">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn bg-red-600 hover:bg-red-700 text-white border-0 rounded-xl font-bold active:scale-95 transition-all">Ya, Batalkan Semua</button>
                    </form>
                </x-slot:footer>
            </x-modal>

            <button 
                onclick="openSnapGroup('{{ $firstOrder->snap_token }}', '{{ $retryUrl }}', '{{ $csrfToken }}')"
                class="btn bg-fern-700 hover:bg-fern-800 text-white border-none w-full sm:w-auto px-8 min-h-0 h-11 rounded-xl font-bold text-sm flex items-center justify-center gap-2 active:scale-95 transition-all shadow-md"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                </svg>
                Bayar Sekarang
            </button>
        </div>
    </div>

</div>

@props(['order'])

{{-- 
  Komponen Card Ringkasan Pesanan (Sisi Mahasiswa):
  - Menampilkan ringkasan informasi pesanan tunggal (kode pesanan, waktu pengambilan, status, kantin, rincian barang, total biaya).
  - Menggabungkan taktik stretched-link ('absolute inset-0 z-10') dengan lapisan z-index tinggi ('relative z-20') 
    pada elemen form/tombol interaktif internal sehingga pengguna bisa mengklik kartu untuk detail pesanan, 
    tanpa merusak fungsionalitas tombol aksi.
  - Menyediakan cabang logika aksi berdasarkan status pesanan:
    - Status selesai/dibatalkan: Memunculkan form POST untuk "Beli Lagi" (reorder) dan tautan ke halaman detail.
    - Status berjalan (menunggu/proses/siap): Memunculkan tombol utama "Pantau Antrian" untuk pelacakan real-time.
--}}

<div class="bg-vanilla-custard-50 border border-base-content/30 rounded-3xl p-4 sm:p-10 shadow-sm relative">
    <a href="{{ route('order.show', $order->id) }}" class="absolute inset-0 z-10 rounded-3xl"
        aria-label="Detail Pesanan {{ $order->order_code }}"></a>

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 sm:gap-4 mb-6">
        <div class="relative z-20">
            <h2 class="text-sm sm:text-xl md:text-2xl font-bold text-base-content mb-1 whitespace-nowrap truncate max-w-[240px] sm:max-w-none">
                No. Orderan : {{ $order->order_code }}</h2>
            <p class="text-xs sm:text-sm font-medium text-base-content">Waktu Pickup:
                {{ $order->pickup_time->format('H:i, d M Y') }}</p>
        </div>
        <div class="relative z-20">
            <x-status-badge :status="$order->status_label" />
        </div>
    </div>

    <div class="bg-white border border-base-content/30 rounded-2xl sm:rounded-3xl p-4 sm:p-6 mb-6 relative z-20">
        <div class="flex justify-between items-center mb-4 sm:mb-6">
            <h3 class="text-xl sm:text-2xl font-bold text-base-content">{{ $order->canteen->name }}</h3>
            <span class="text-xs sm:text-sm font-bold text-base-content">{{ $order->items->sum('qty') }} Item</span>
        </div>

        @foreach ($order->items as $item)
            <x-user.order-item :image="$item->menu && $item->menu->image
                ? asset('storage/' . $item->menu->image)
                : 'https://ui-avatars.com/api/?name=' . urlencode($item->menu->name ?? 'Menu') . '&background=random'" :name="$item->menu->name ?? 'Menu Dihapus'" :description="$item->menu->description ?? ''" :price="$item->menu ? $item->menu->formatted_price : 'Rp ' . number_format($item->price, 0, ',', '.')" :quantity="$item->qty"
                variant="card" />
        @endforeach
    </div>

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mt-6 sm:mt-8 relative z-20">
        <div>
            <p class="text-xs text-base-content/60 font-bold uppercase">Total Pembayaran</p>
            <p class="text-xl sm:text-2xl font-black text-fern-700">{{ $order->formatted_total }}</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto relative z-20">
            @if (in_array($order->status, ['selesai', 'dibatalkan']))
                <form action="{{ route('order.reorder', $order->id) }}" method="POST" class="w-full sm:w-auto m-0 p-0">
                    @csrf
                    <button type="submit"
                        class="btn bg-fern-700 hover:bg-fern-800 text-white border-none w-full sm:w-auto px-6 min-h-0 h-11 rounded-xl font-bold text-sm flex items-center justify-center gap-2 active:scale-95 transition-all shadow-md">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Beli Lagi
                    </button>
                </form>
                <a href="{{ route('order.show', $order->id) }}"
                    class="btn bg-white hover:bg-base-100 text-base-content border border-base-content/25 w-full sm:w-auto px-6 min-h-0 h-11 rounded-xl font-bold text-sm text-center flex items-center justify-center active:scale-95 transition-all shadow-sm">
                    Detail
                </a>
            @else
                <a href="{{ route('order.show', $order->id) }}"
                    class="btn bg-fern-700 hover:bg-fern-800 text-white border-none w-full sm:w-auto px-8 min-h-0 h-11 rounded-xl font-bold text-sm text-center flex items-center justify-center active:scale-95 transition-all shadow-md">
                    Pantau Antrian
                </a>
            @endif
        </div>
    </div>

</div>

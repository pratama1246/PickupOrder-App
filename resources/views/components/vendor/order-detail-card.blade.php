@props(['order', 'canteen'])

{{-- 
  Komponen Card Detail Pesanan Vendor:
  - Menyajikan informasi detail pesanan untuk pemilik kantin (status pembayaran, catatan pembeli, rincian item, detail pickup).
  - Melindungi pesanan online pending ('midtrans' & 'pending') dengan menonaktifkan tombol pemrosesan 
    dan memunculkan pesan peringatan agar vendor tidak memproses pesanan sebelum dana terkonfirmasi.
  - Berfungsi sebagai pengendali State Machine alur pesanan ('menunggu' -> 'dimasak' -> 'siap_diambil' -> 'selesai'):
  - Khusus untuk pesanan tunai ('cash' & 'pending') di fase 'siap_diambil', label tombol berubah menjadi 
    "Selesai & Terima Uang" guna menginstruksikan vendor untuk menagih uang tunai saat serah terima makanan.
--}}
<div class="bg-vanilla-custard-50 border border-base-content/30 rounded-3xl p-6 sm:p-8 shadow-sm">
    <div
        class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-base-content/20 pb-4 mb-6">
        <div>
            <h2 class="text-xl font-bold text-base-content">{{ $canteen->name }}</h2>
            <p class="text-sm font-medium text-base-content/70 mt-1">No. Order : {{ $order->order_code }}</p>
        </div>
        <x-status-badge :status="$order->status_label" />
    </div>

    <div class="bg-white border border-base-content/20 rounded-2xl p-4 sm:p-6 mb-6 shadow-sm">
        @foreach ($order->items as $item)
            <x-user.order-item :image="$item->menu && $item->menu->image
                ? asset('storage/' . $item->menu->image)
                : 'https://ui-avatars.com/api/?name=' . urlencode($item->menu->name ?? 'Menu') . '&background=random'" :name="$item->menu->name ?? 'Menu Dihapus'" :description="$item->menu->description ?? ''" :price="'Rp ' . number_format($item->price, 0, ',', '.')" :quantity="$item->qty"
                variant="list" />
        @endforeach

        <div class="mt-4 pt-4 border-t border-base-content/10">
            <p class="text-xs font-bold text-base-content/60 uppercase mb-1">Catatan dari Pembeli</p>
            <p class="text-sm font-medium text-base-content">{{ $order->notes ?? 'Tidak ada catatan.' }}</p>
        </div>
    </div>

    <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6 pt-2">
        <div>
            <h3 class="text-lg font-bold text-base-content mb-1">Total Belanja</h3>
            <p class="text-2xl font-extrabold text-fern-700">{{ $order->formatted_total }}</p>
        </div>

        <div class="flex-1 max-w-sm w-full">
            <ul class="text-xs sm:text-sm font-medium text-base-content/80 space-y-1.5 mb-5">
                <li class="flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-base-content/50 shrink-0"></span>
                    Jadwal Pickup: {{ $order->pickup_time->format('H:i, d M Y') }}
                </li>
                <li class="flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-base-content/50 shrink-0"></span>
                    Jumlah Pesanan: {{ $order->items->sum('qty') }} item
                </li>
                <li class="flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-base-content/50 shrink-0"></span>
                    Metode Bayar: {{ $order->payment_method_label }}
                </li>
                <li class="flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-base-content/50 shrink-0"></span>
                    Status Bayar:
                    @php
                        $payBadgeClass = match ($order->payment_status) {
                            'paid' => 'bg-emerald-100 text-emerald-800',
                            'failed' => 'bg-red-100 text-red-800',
                            'expired' => 'bg-red-100 text-red-800',
                            default => 'bg-amber-100 text-amber-800',
                        };
                    @endphp
                    <span class="inline-block text-xs font-bold px-2 py-0.5 rounded {{ $payBadgeClass }}">
                        {{ $order->payment_status_label }}
                    </span>
                </li>
                <li class="flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-base-content/50 shrink-0"></span>
                    Status: {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                </li>
            </ul>

            @if ($order->payment_method === 'midtrans' && $order->payment_status === 'pending')
                <div class="bg-amber-50 border border-amber-300 rounded-xl p-4 mb-4">
                    <div class="flex items-start gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-amber-600 shrink-0 mt-0.5"
                             fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <div>
                            <p class="text-amber-800 font-bold text-sm">Menunggu Pembayaran Online</p>
                            <p class="text-amber-700 text-xs font-medium mt-0.5">Pesanan ini belum dapat diproses.
                                Silakan tunggu konfirmasi pembayaran dari mahasiswa.</p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="flex flex-col sm:flex-row gap-3">
                @if (in_array($order->status, ['menunggu', 'dimasak', 'siap_diambil']))
                    @if ($order->payment_method === 'midtrans' && $order->payment_status === 'pending')
                        <button type="button" disabled
                            class="btn bg-base-300 text-base-content/40 border-none w-full rounded-xl font-bold shadow-sm cursor-not-allowed flex-1">
                            Menunggu Pembayaran
                        </button>
                    @else
                        <form action="{{ route('vendor.order.update', $order->id) }}" method="POST" class="flex-1">
                            @csrf
                            @method('PUT')
                            <button type="submit"
                                class="btn bg-fern-700 hover:bg-fern-800 text-white border-none w-full rounded-xl font-bold shadow-sm active:scale-95 transition-all">
                                @if ($order->status === 'menunggu')
                                    Mulai Masak
                                @elseif ($order->status === 'dimasak')
                                    Siap Diambil
                                @elseif ($order->status === 'siap_diambil')
                                    @if ($order->payment_method === 'cash' && $order->payment_status === 'pending')
                                        Selesai & Terima Uang
                                    @else
                                        Selesaikan Pesanan
                                    @endif
                                @else
                                    Ubah Status
                                @endif
                            </button>
                        </form>
                    @endif
                @endif

                @if (in_array($order->status, ['menunggu', 'dimasak']))
                    <div class="flex-1">
                        <button type="button"
                            onclick="document.getElementById('cancel_order_modal_{{ $order->id }}').showModal()"
                            class="btn bg-red-500 hover:bg-red-600 text-white border-none w-full rounded-xl font-bold shadow-sm active:scale-95 transition-all">
                            Batalkan
                        </button>
                        <x-modal id="cancel_order_modal_{{ $order->id }}" type="error" title="Batalkan Pesanan">
                            Apakah Anda yakin ingin membatalkan pesanan ini? Aksi ini tidak dapat diurungkan.

                            <x-slot:footer>
                                <button type="button"
                                    onclick="document.getElementById('cancel_order_modal_{{ $order->id }}').close()"
                                    class="btn btn-ghost rounded-xl font-bold active:scale-95 transition-all">Batal</button>
                                <form action="{{ route('vendor.order.destroy', $order->id) }}" method="POST"
                                    class="m-0 p-0 inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="btn bg-red-600 hover:bg-red-700 text-white border-0 rounded-xl font-bold active:scale-95 transition-all">Ya,
                                        Batalkan</button>
                                </form>
                            </x-slot:footer>
                        </x-modal>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@props(['order', 'canteen'])

<div class="bg-vanilla-custard-50 border border-base-content/30 rounded-3xl p-6 sm:p-8 shadow-sm">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-base-content/20 pb-4 mb-6">
        <div>
            <h2 class="text-xl font-bold text-base-content">{{ $canteen->name }}</h2>
            <p class="text-sm font-medium text-base-content/70 mt-1">No. Order : {{ $order->order_code }}</p>
        </div>
        <x-status-badge :status="$order->status_label" />
    </div>

    <div class="bg-white border border-base-content/20 rounded-2xl p-4 sm:p-6 mb-6 shadow-sm">
        @foreach ($order->items as $item)
            <x-user.order-item
                :image="$item->menu && $item->menu->image ? asset('storage/' . $item->menu->image) : asset('assets/food/es teh.jpg')"
                :name="$item->menu->name ?? 'Menu Dihapus'"
                :description="$item->menu->description ?? ''"
                :price="'Rp ' . number_format($item->price, 0, ',', '.')"
                :quantity="$item->qty"
                variant="list"
            />
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
                    Status: {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                </li>
            </ul>

            <div class="flex flex-col sm:flex-row gap-3">
                @if (in_array($order->status, ['menunggu', 'dimasak', 'siap_diambil']))
                    <form action="{{ route('vendor.order.update', $order->id) }}" method="POST" class="flex-1">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="btn bg-fern-700 hover:bg-fern-800 text-white border-none w-full rounded-xl font-bold shadow-sm">
                            Ubah Status
                        </button>
                    </form>
                @endif

                @if (in_array($order->status, ['menunggu', 'dimasak']))
                    <div class="flex-1">
                        <button type="button" onclick="document.getElementById('cancel_order_modal_{{ $order->id }}').showModal()" class="btn bg-red-500 hover:bg-red-600 text-white border-none w-full rounded-xl font-bold shadow-sm active:scale-95 transition-all">
                            Batalkan
                        </button>
                        <x-modal id="cancel_order_modal_{{ $order->id }}" type="error" title="Batalkan Pesanan">
                            Apakah Anda yakin ingin membatalkan pesanan ini? Aksi ini tidak dapat diurungkan.
                            
                            <x-slot:footer>
                                <button type="button" onclick="document.getElementById('cancel_order_modal_{{ $order->id }}').close()" class="btn btn-ghost rounded-xl font-bold active:scale-95 transition-all">Batal</button>
                                <form action="{{ route('vendor.order.destroy', $order->id) }}" method="POST" class="m-0 p-0 inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn bg-red-600 hover:bg-red-700 text-white border-0 rounded-xl font-bold active:scale-95 transition-all">Ya, Batalkan</button>
                                </form>
                            </x-slot:footer>
                        </x-modal>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

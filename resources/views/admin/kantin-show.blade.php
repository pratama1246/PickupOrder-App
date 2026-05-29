@extends('layouts.admin')

@section('title', 'Detail Kantin - Admin PNC')

@section('content')

    <div class="max-w-8xl mx-auto space-y-6 pb-10 lg:pb-0" x-data="{ activeTab: 'menu' }" x-cloak>

        {{-- 
          Navigasi tab 'menu' dan 'order' dikontrol di sisi klien menggunakan Alpine.js activeTab 
          untuk performa perpindahan tab yang cepat tanpa memicu reload halaman penuh.
        --}}
        <a href="{{ route('admin.kantin.index') }}"
            class="btn btn-sm btn-ghost gap-1 px-2 mb-2 text-base-content/70 hover:bg-base-200 transition-colors w-fit flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
            Kembali ke Daftar Kantin
        </a>

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center justify-between md:justify-start gap-4 w-full md:w-auto">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-base-content mb-1">Detail Kantin</h1>
                    <p class="text-base-content/70 text-sm sm:text-base font-medium">Informasi lengkap, menu, dan statistik
                        transaksi kantin.</p>
                </div>

                <div class="flex md:hidden items-center gap-2 shrink-0">
                    <button type="button"
                        onclick="document.getElementById('delete_canteen_modal_{{ $canteen->id }}').showModal()"
                        class="btn bg-red-600 hover:bg-red-700 text-white border-none shadow-sm rounded-md transition-colors p-2.5 h-auto min-h-0 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                    <a href="{{ route('admin.kantin.edit', $canteen->id) }}"
                        class="btn bg-fern-700 hover:bg-fern-800 text-white border-none shadow-sm rounded-md transition-colors p-2.5 h-auto min-h-0 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                    </a>
                </div>
            </div>

            <div class="hidden md:flex items-center gap-2 shrink-0">
                <button type="button"
                    onclick="document.getElementById('delete_canteen_modal_{{ $canteen->id }}').showModal()"
                    class="btn bg-red-600 hover:bg-red-700 text-white border-none shadow-sm rounded-xl transition-colors text-sm font-bold px-5">
                    Hapus
                </button>
                <a href="{{ route('admin.kantin.edit', $canteen->id) }}"
                    class="btn bg-fern-700 hover:bg-fern-800 text-white border-none shadow-sm rounded-xl transition-colors text-sm font-bold px-5">
                    Edit
                </a>
            </div>
        </div>

        <div class="bg-white rounded-3xl shadow-sm border border-base-200 overflow-hidden flex flex-col md:flex-row">
            <div class="w-full md:w-1/3 h-48 md:h-auto relative bg-base-200">
                <div class="absolute inset-0 flex items-center justify-center text-fern-700/40">
                    <span class="loading loading-bars loading-lg"></span>
                </div>
                @if ($canteen->image)
                    <img src="{{ asset('storage/' . $canteen->image) }}" alt="{{ $canteen->name }}"
                        class="w-full h-full object-cover relative"
                        onerror="this.src='{{ asset('assets/food/es teh.jpg') }}'" />
                @else
                    <img src="{{ asset('assets/food/es teh.jpg') }}" alt="{{ $canteen->name }}"
                        class="w-full h-full object-cover opacity-80 relative" />
                @endif
                <div class="absolute inset-0 bg-linear-to-t from-black/50 to-transparent md:hidden z-20"></div>
                <div class="absolute bottom-4 left-4 md:hidden z-20">
                    @if ($canteen->is_open)
                        <span
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 shadow-sm border border-emerald-200">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> Buka
                        </span>
                    @else
                        <span
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-rose-100 text-rose-800 shadow-sm border border-rose-200">
                            <span class="w-2 h-2 rounded-full bg-rose-500"></span> Tutup
                        </span>
                    @endif
                </div>
            </div>

            <div class="p-6 md:p-8 flex-1 flex flex-col justify-center">
                <div class="flex items-start justify-between mb-2">
                    <div>
                        <h2 class="text-2xl sm:text-3xl font-extrabold text-base-content mb-2">{{ $canteen->name }}</h2>
                        <p class="text-base-content/70 text-sm sm:text-base font-medium max-w-xl">
                            {{ $canteen->description ?: 'Tidak ada deskripsi.' }}</p>
                    </div>
                    <div class="hidden md:block shrink-0">
                        @if ($canteen->is_open)
                            <span
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 shadow-sm border border-emerald-200">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span> Buka
                            </span>
                        @else
                            <span
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-rose-100 text-rose-800 shadow-sm border border-rose-200">
                                <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span> Tutup
                            </span>
                        @endif
                    </div>
                </div>

                <div class="divider my-4"></div>

                <div class="flex items-center gap-3">
                    {{-- 
                      Menggunakan event 'onerror' untuk menangani kasus di mana avatar pemilik gagal dimuat
                      dengan cara menyembunyikan tag img dan menampilkan inisial teks sebagai avatar fallback.
                    --}}
                    @if (optional($canteen->owner)->avatar)
                        <div
                            class="w-12 h-12 rounded-full bg-base-200 ring-2 ring-fern-200 flex items-center justify-center shrink-0 overflow-hidden">
                            <img src="{{ asset('storage/' . $canteen->owner->avatar) }}"
                                alt="{{ optional($canteen->owner)->name }}" class="w-full h-full object-cover"
                                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';" />
                            <div class="w-full h-full bg-fern-100 text-fern-700 items-center justify-center hidden">
                                <span
                                    class="text-xl font-bold uppercase">{{ substr(optional($canteen->owner)->name ?? 'V', 0, 1) }}</span>
                            </div>
                        </div>
                    @else
                        <div
                            class="w-12 h-12 rounded-full bg-fern-100 text-fern-700 ring-2 ring-fern-200 flex items-center justify-center shrink-0">
                            <span
                                class="text-xl font-bold uppercase">{{ substr(optional($canteen->owner)->name ?? 'V', 0, 1) }}</span>
                        </div>
                    @endif
                    <div>
                        <p class="text-xs font-bold text-base-content/50 uppercase">Pemilik Kantin (Vendor)</p>
                        <p class="text-base-content font-bold flex items-center gap-2">
                            {{ optional($canteen->owner)->name ?? 'Tidak diketahui' }}
                        </p>
                        <a href="mailto:{{ optional($canteen->owner)->email }}"
                            class="text-sm text-fern-600 hover:text-fern-700 font-medium transition-colors">
                            {{ optional($canteen->owner)->email ?? '-' }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <x-stat-card label="Total Menu" value="{{ $canteen->menus_count }} Menu" />
            <x-stat-card label="Total Pesanan" value="{{ $canteen->orders_count }} Pesanan" />
            <x-stat-card label="Pesanan Selesai" value="{{ $canteen->completed_orders_count }} Pesanan"
                valueColor="text-emerald-600" />
            <x-stat-card label="Total Pendapatan" value="Rp{{ number_format($canteen->total_revenue ?? 0, 0, ',', '.') }}"
                valueColor="text-fern-700" />
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-base-200 overflow-hidden mt-8">
            <div
                class="flex items-center gap-6 px-6 border-b border-base-200 overflow-x-auto scrollbar-hide bg-base-100/50">
                <button type="button" @click="activeTab = 'menu'"
                    :class="activeTab === 'menu' ? 'border-fern-700 text-fern-700' :
                        'border-transparent text-base-content/60 hover:text-base-content'"
                    class="py-4 font-bold text-sm sm:text-base border-b-2 transition-colors whitespace-nowrap">
                    Daftar Menu
                </button>
                <button type="button" @click="activeTab = 'order'"
                    :class="activeTab === 'order' ? 'border-fern-700 text-fern-700' :
                        'border-transparent text-base-content/60 hover:text-base-content'"
                    class="py-4 font-bold text-sm sm:text-base border-b-2 transition-colors whitespace-nowrap">
                    Transaksi Terbaru
                </button>
            </div>

            <div class="p-0">
                <div x-show="activeTab === 'menu'" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                    <div class="overflow-x-auto">
                        <table class="table w-full">
                            <thead class="bg-base-200/50 text-xs text-base-content/70">
                                <tr>
                                    <th class="py-4 px-6 font-semibold">Menu</th>
                                    <th class="py-4 px-6 font-semibold">Harga</th>
                                    <th class="py-4 px-6 font-semibold text-center">Stok</th>
                                    <th class="py-4 px-6 font-semibold text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($menus as $menu)
                                    <tr class="hover:bg-base-200/30 transition-colors border-b border-base-content/5">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-4">
                                                <div
                                                    class="w-12 h-12 rounded-xl bg-base-200 overflow-hidden shrink-0 border border-base-content/10">
                                                    <img src="{{ $menu->image ? asset('storage/' . $menu->image) : asset('assets/food/es teh.jpg') }}"
                                                        alt="{{ $menu->name }}" class="w-full h-full object-cover"
                                                        onerror="this.src='{{ asset('assets/food/es teh.jpg') }}'" />
                                                </div>
                                                <div>
                                                    <div class="font-bold text-base-content text-sm sm:text-base">
                                                        {{ $menu->name }}</div>
                                                    <div class="text-xs text-base-content/60 line-clamp-1 max-w-[200px]">
                                                        {{ $menu->description ?: '-' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 font-semibold text-sm">{{ $menu->formatted_price }}</td>
                                        <td class="px-6 py-4 text-center font-medium text-sm">{{ $menu->stock }}</td>
                                        <td class="px-6 py-4 text-center">
                                            @if ($menu->is_available && $menu->stock > 0)
                                                <span
                                                    class="inline-flex px-2.5 py-1 bg-emerald-100 text-emerald-800 rounded-md text-xs font-bold border border-emerald-200 shadow-sm">Tersedia</span>
                                            @else
                                                <span
                                                    class="inline-flex px-2.5 py-1 bg-rose-100 text-rose-800 rounded-md text-xs font-bold border border-rose-200 shadow-sm">Habis</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-10">
                                            <div class="flex flex-col items-center justify-center text-base-content/50">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mb-3"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                                    stroke-width="1.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                                </svg>
                                                <p class="font-medium">Belum ada menu di kantin ini.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if ($menus->hasPages())
                        <div class="p-4 border-t border-base-content/5">
                            {{ $menus->links() }}
                        </div>
                    @endif
                </div>

                <div x-show="activeTab === 'order'" style="display: none;"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                    <div class="overflow-x-auto">
                        <table class="table w-full">
                            <thead class="bg-base-200/50 text-xs text-base-content/70">
                                <tr>
                                    <th class="py-4 px-6 font-semibold">Kode & Waktu</th>
                                    <th class="py-4 px-6 font-semibold">Pemesan</th>
                                    <th class="py-4 px-6 font-semibold text-right">Total Harga</th>
                                    <th class="py-4 px-6 font-semibold text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $order)
                                    <tr class="hover:bg-base-200/30 transition-colors border-b border-base-content/5">
                                        <td class="px-6 py-4">
                                            <div class="font-bold text-sm">{{ $order->order_code }}</div>
                                            <div class="text-xs text-base-content/50">
                                                {{ $order->created_at->format('d M Y, H:i') }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="font-bold text-sm">
                                                {{ optional($order->user)->name ?? 'Tidak diketahui' }}</div>
                                            <div class="text-xs text-base-content/60">Waktu Ambil:
                                                {{ $order->pickup_time->format('H:i') }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-right font-bold text-fern-700 text-sm">
                                            {{ $order->formatted_total }}
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <x-status-badge :status="$order->status_label" />
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-10">
                                            <div class="flex flex-col items-center justify-center text-base-content/50">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mb-3"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                                    stroke-width="1.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                                </svg>
                                                <p class="font-medium">Belum ada transaksi di kantin ini.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if ($orders->hasPages())
                        <div class="p-4 border-t border-base-content/5">
                            {{ $orders->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>


    <x-modal id="delete_canteen_modal_{{ $canteen->id }}" type="error" title="Hapus Kantin">
        Apakah Anda yakin ingin menghapus kantin <strong>{{ $canteen->name }}</strong>? Seluruh data menu dan pesanan
        terkait akan ikut terhapus.

        <x-slot:footer>
            <button type="button" onclick="document.getElementById('delete_canteen_modal_{{ $canteen->id }}').close()"
                class="btn btn-ghost rounded-xl font-bold transition-colors">Batal</button>
            <form action="{{ route('admin.kantin.destroy', $canteen->id) }}" method="POST"
                class="m-0 p-0 inline-block">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="btn bg-red-600 hover:bg-red-700 text-white border-0 rounded-xl font-bold transition-colors">Ya,
                    Hapus</button>
            </form>
        </x-slot:footer>
    </x-modal>

@endsection

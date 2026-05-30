@extends('layouts.vendor')

@section('title', 'Laporan Penjualan - Vendor PNC')

@section('content')
    <div class="max-w-8xl mx-auto space-y-4 sm:space-y-6 pb-10 lg:pb-0">

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 sm:mb-8 print:px-0 print:hidden">
            <div>
                <h1 class="text-2xl sm:text-4xl font-bold text-base-content mb-2">Laporan Penjualan</h1>
                <p class="text-base-content/70 text-sm sm:text-lg font-medium">Ringkasan performa penjualan Kantin
                    {{ $canteen->name }}</p>
            </div>
            <button onclick="window.print()"
                class="btn bg-fern-700 hover:bg-fern-800 text-white border-none rounded-xl font-bold shadow-md active:scale-95 transition-all print:hidden flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0v3.396c0 .616.5 1.114 1.114 1.114h8.272c.614 0 1.114-.498 1.114-1.114V6.273c0-.616-.5-1.114-1.114-1.114H8.344c-.614 0-1.114.498-1.114 1.114v3.396" />
                </svg>
                <span>Cetak Laporan</span>
            </button>
        </div>

        <div class="mb-6 print:hidden">
            <form method="GET" action="{{ route('vendor.report.index') }}"
                class="flex flex-col sm:flex-row items-end gap-4">
                <div class="w-full sm:w-auto">
                    <label class="block text-sm font-bold text-base-content mb-1.5">Dari Tanggal</label>
                    <input type="date" name="start_date" value="{{ $startDate }}"
                        class="input input-bordered w-full rounded-xl border-base-content/25 focus:outline-none focus:border-fern-600 text-sm font-medium bg-white"
                        required>
                </div>
                <div class="w-full sm:w-auto">
                    <label class="block text-sm font-bold text-base-content mb-1.5">Sampai Tanggal</label>
                    <input type="date" name="end_date" value="{{ $endDate }}"
                        class="input input-bordered w-full rounded-xl border-base-content/25 focus:outline-none focus:border-fern-600 text-sm font-medium bg-white"
                        required>
                </div>
                <button type="submit"
                    class="btn bg-base-200 hover:bg-base-300 text-base-content border-none rounded-xl font-bold text-sm shadow-sm px-6 active:scale-95 transition-all w-full sm:w-auto flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-base-content/70" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    Tampilkan Data
                </button>
            </form>
        </div>

        <div class="hidden print:block border-b-2 border-black pb-4">
            <h2 class="text-2xl font-bold">Laporan Penjualan Kantin: {{ $canteen->name }}</h2>
            <p class="text-sm">Periode: {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d M Y') }} -
                {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d M Y') }}</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6">
            <x-stat-card label="Total Pesanan" :value="$totalOrders" subtext="Pesanan berhasil diselesaikan"
                iconBg="bg-emerald-50 text-fern-700">
                <x-slot:icon>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007z" />
                    </svg>
                </x-slot:icon>
            </x-stat-card>

            <x-stat-card label="Total Pendapatan" :value="'Rp ' . number_format($totalRevenue, 0, ',', '.')" subtext="Periode Terpilih"
                iconBg="bg-emerald-50 text-fern-700">
                <x-slot:icon>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </x-slot:icon>
            </x-stat-card>

            <x-stat-card label="Rata-rata Pembelian" :value="'Rp ' . number_format($averageOrderValue, 0, ',', '.')" subtext="Per Transaksi (AOV)"
                iconBg="bg-emerald-50 text-fern-700">
                <x-slot:icon>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                    </svg>
                </x-slot:icon>
            </x-stat-card>
        </div>

        <div class="bg-base-100 rounded-3xl border border-base-200 shadow-sm overflow-hidden flex flex-col">
            <div
                class="p-4 sm:p-5 border-b border-base-200 flex justify-between items-center bg-base-100/50 print:border-none print:bg-transparent">
                <h2 class="text-sm sm:text-base font-bold text-base-content">10 Menu Terlaris</h2>
            </div>
            <div class="overflow-x-auto flex-1 p-0">
                <table class="table table-sm w-full min-w-[500px]">
                    <thead class="bg-base-200/50 text-xs">
                        <tr>
                            <th class="font-medium text-base-content/70 py-3 px-4">No</th>
                            <th class="font-medium text-base-content/70 py-3 px-4">Menu</th>
                            <th class="font-medium text-base-content/70 py-3 px-4 text-center">Total Terjual</th>
                            <th class="font-medium text-base-content/70 py-3 px-4 text-right">Total Pendapatan</th>
                        </tr>
                    </thead>
                    <tbody class="text-xs sm:text-sm">
                        @forelse($topMenus as $index => $item)
                            <tr class="hover:bg-base-200/30 transition-colors border-b border-base-content/5">
                                <td class="px-4 py-3 font-semibold">{{ $index + 1 }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="avatar print:hidden">
                                            <div class="rounded-sm w-10 h-10">
                                                <img src="{{ $item->menu && $item->menu->image ? asset('storage/' . $item->menu->image) : 'https://ui-avatars.com/api/?name=' . urlencode(optional($item->menu)->name ?? 'Menu') . '&background=random' }}"
                                                    onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode(optional($item->menu)->name ?? 'Menu') }}&background=random'"
                                                    alt="{{ optional($item->menu)->name ?? 'Menu Dihapus' }}" />
                                            </div>
                                        </div>
                                        <div>
                                            <div class="font-bold text-sm sm:text-base">
                                                {{ $item->menu->name ?? 'Menu Dihapus' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center font-bold px-4 py-3">
                                    {{ $item->total_qty }}
                                    <span class="text-xs sm:text-sm font-medium text-base-content/60">porsi</span>
                                </td>
                                <td class="text-right font-bold text-fern-700 px-4 py-3">Rp
                                    {{ number_format($item->total_sales, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-8 text-base-content/50 text-xs sm:text-sm">
                                    Tidak ada data penjualan pada periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- 
          Aturan CSS inline khusus media print untuk menyembunyikan navigasi, sidebar, dan tombol cetak 
          agar menghasilkan tata letak cetak dokumen fisik/PDF yang bersih dan rapi secara dinamis.
        --}}
        <style>
            @media print {
                body {
                    background: white !important;
                    -webkit-print-color-adjust: exact;
                    print-color-adjust: exact;
                    overflow: visible !important;
                    height: auto !important;
                }

                /* Sembunyikan elemen dashboard layout yang tidak perlu di cetak */
                .drawer-side,
                .navbar,
                .dock,
                header,
                aside,
                .drawer-toggle,
                .print\:hidden {
                    display: none !important;
                }

                .drawer,
                .drawer-content {
                    display: block !important;
                    overflow: visible !important;
                    height: auto !important;
                }

                main {
                    padding: 0 !important;
                    overflow: visible !important;
                    height: auto !important;
                }

                /* Force semua background ke putih agar hasil cetak bersih */
                .bg-base-100,
                .bg-base-200,
                .bg-emerald-50,
                [class*="bg-vanilla-custard"],
                [class*="bg-linear"],
                [class*="from-"],
                [class*="to-"] {
                    background: white !important;
                    background-image: none !important;
                }

                .bg-white {
                    box-shadow: none !important;
                    border: none !important;
                }

                /* Kompakkan stat card saat cetak */
                .grid > div[class*="rounded-2xl"] {
                    padding: 0.6rem 0.9rem !important;
                    min-height: 0 !important;
                    border: 1px solid #e5e7eb !important;
                    box-shadow: none !important;
                }

                /* Sembunyikan icon stat card, perkecil font value */
                .grid > div[class*="rounded-2xl"] > div:last-child {
                    display: none !important;
                }

                .grid > div[class*="rounded-2xl"] p[class*="text-2xl"],
                .grid > div[class*="rounded-2xl"] p[class*="text-3xl"] {
                    font-size: 1.25rem !important;
                    line-height: 1.4 !important;
                }

                @page {
                    margin: 1cm;
                }
            }
        </style>
    </div>
@endsection

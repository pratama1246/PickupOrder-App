@extends('layouts.app')

@section('title', 'Laporan Penjualan - PNC')

@section('content')
<main class="min-h-screen bg-base-100 pb-12">
    <x-breadcrumb :links="[
        ['label' => 'Dashboard', 'url' => route('vendor.dashboard')],
        ['label' => 'Laporan Penjualan']
    ]" class="print:hidden" />

    <section class="px-4 sm:px-10 md:px-16 lg:px-24 mb-6 print:px-0">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
            <div>
                <h1 class="text-3xl font-bold text-base-content">Laporan Penjualan</h1>
                <p class="text-base-content/70 mt-1">Ringkasan performa penjualan Kantin {{ $canteen->name }}</p>
            </div>
            <button onclick="window.print()" class="btn bg-fern-700 hover:bg-fern-800 text-white border-none rounded-xl font-bold shadow-md active:scale-95 transition-all print:hidden flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0v3.396c0 .616.5 1.114 1.114 1.114h8.272c.614 0 1.114-.498 1.114-1.114V6.273c0-.616-.5-1.114-1.114-1.114H8.344c-.614 0-1.114.498-1.114 1.114v3.396" />
                </svg>
                Cetak Laporan
            </button>
        </div>
    </section>

    <section class="px-4 sm:px-10 md:px-16 lg:px-24 mb-8 print:hidden">
        <div class="max-w-7xl mx-auto bg-vanilla-custard-50 border border-base-content/10 rounded-2xl p-4 sm:p-6 shadow-sm">
            <form method="GET" action="{{ route('vendor.laporan.index') }}" class="flex flex-col sm:flex-row items-end gap-4">
                <div class="w-full sm:w-auto">
                    <label class="block text-sm font-bold text-base-content/70 mb-1">Dari Tanggal</label>
                    <input type="date" name="start_date" value="{{ $startDate }}" class="input input-bordered w-full rounded-xl focus:border-fern-700 bg-white" required>
                </div>
                <div class="w-full sm:w-auto">
                    <label class="block text-sm font-bold text-base-content/70 mb-1">Sampai Tanggal</label>
                    <input type="date" name="end_date" value="{{ $endDate }}" class="input input-bordered w-full rounded-xl focus:border-fern-700 bg-white" required>
                </div>
                <button type="submit" class="btn bg-fern-700 hover:bg-fern-800 text-white border-none rounded-xl font-bold w-full sm:w-auto">Filter Data</button>
            </form>
        </div>
    </section>

    <!-- Ringkasan Cetak -->
    <section class="px-4 sm:px-10 md:px-16 lg:px-24 hidden print:block mb-8">
        <div class="max-w-7xl mx-auto border-b-2 border-black pb-4">
            <h2 class="text-2xl font-bold">Laporan Penjualan Kantin: {{ $canteen->name }}</h2>
            <p class="text-sm">Periode: {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d M Y') }}</p>
        </div>
    </section>

    <section class="px-4 sm:px-10 md:px-16 lg:px-24 mb-8">
        <div class="max-w-7xl mx-auto grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6">
            <!-- Total Pesanan -->
            <x-stat-card 
                title="Total Pesanan" 
                :value="$totalOrders" 
                subtitle="Pesanan berhasil diselesaikan" 
                icon="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" 
                color="blue" 
            />
            <!-- Total Pendapatan -->
            <x-stat-card 
                title="Total Pendapatan" 
                :value="'Rp ' . number_format($totalRevenue, 0, ',', '.')" 
                subtitle="Periode Terpilih" 
                icon="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" 
                color="green" 
            />
            <!-- Rata-rata Nilai Pesanan -->
            <x-stat-card 
                title="Rata-rata Pembelian" 
                :value="'Rp ' . number_format($averageOrderValue, 0, ',', '.')" 
                subtitle="Per Transaksi (AOV)" 
                icon="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" 
                color="purple" 
            />
        </div>
    </section>

    <section class="px-4 sm:px-10 md:px-16 lg:px-24 mb-12">
        <div class="max-w-7xl mx-auto bg-white border border-base-content/10 rounded-2xl p-6 shadow-sm">
            <h3 class="text-xl font-bold text-base-content mb-4 print:hidden">10 Menu Terlaris</h3>
            
            <div class="overflow-x-auto">
                <table class="table w-full border border-base-content/10">
                    <thead class="bg-base-200 text-base-content/70 border-b border-base-content/10">
                        <tr>
                            <th>No</th>
                            <th>Menu</th>
                            <th class="text-center">Total Terjual</th>
                            <th class="text-right">Total Pendapatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topMenus as $index => $item)
                        <tr class="hover border-b border-base-content/10">
                            <th>{{ $index + 1 }}</th>
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="avatar print:hidden">
                                        <div class="mask mask-squircle w-10 h-10">
                                            <img src="{{ $item->menu && $item->menu->image ? asset('storage/' . $item->menu->image) : asset('assets/food/es teh.jpg') }}" alt="{{ $item->menu->name ?? 'Menu Dihapus' }}" />
                                        </div>
                                    </div>
                                    <div>
                                        <div class="font-bold">{{ $item->menu->name ?? 'Menu Dihapus' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center font-bold text-lg">{{ $item->total_qty }} <span class="text-sm font-medium text-base-content/60">porsi</span></td>
                            <td class="text-right font-bold text-fern-700">Rp {{ number_format($item->total_sales, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-8 text-base-content/60">Tidak ada data penjualan pada periode ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- Style khusus untuk cetak halaman -->
    <style>
        @media print {
            body {
                background: white !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .bg-base-100 {
                background-color: transparent !important;
            }
            .bg-white {
                box-shadow: none !important;
                border: none !important;
            }
            @page { margin: 1cm; }
        }
    </style>
</main>
@endsection

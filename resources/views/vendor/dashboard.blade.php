@extends('layouts.vendor')

@section('title', 'Dashboard - Vendor PNC')

@section('content')
    <div class="max-w-8xl mx-auto space-y-4 sm:space-y-6 pb-6 lg:pb-0">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4 mb-6 sm:mb-8">
            <div>
                <h1 class="text-2xl sm:text-4xl font-bold text-base-content mb-2">Dashboard Kantin</h1>
                <p class="text-base-content/70 text-sm sm:text-lg font-medium">Halo, <strong>{{ $canteen->name }}</strong>!
                    Berikut performa hari ini.</p>
            </div>
        </div>

        <!-- Alert Stok Habis -->
        @if ($stats['menu_habis'] > 0)
            <div
                class="alert alert-warning shadow-sm border border-warning/20 bg-warning/10 rounded-2xl p-3 sm:p-4 flex gap-3 text-xs sm:text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-5 w-5 sm:h-6 sm:w-6 text-warning"
                    fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <span>Peringatan: <strong>{{ $stats['menu_habis'] }} menu</strong> stoknya telah habis. Segera perbarui
                    ketersediaan!</span>
            </div>
        @endif

        <!-- Stats Row -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
            <div class="bg-base-200 rounded-xl p-5 shadow-sm border border-base-content/5">
                <p class="text-xs sm:text-sm font-bold text-base-content/70 mb-2">Pesanan Baru</p>
                <p class="text-xl sm:text-2xl font-bold text-base-content">{{ $stats['pesanan_baru'] }} Pesanan</p>
            </div>

            <div class="bg-base-200 rounded-xl p-5 shadow-sm border border-base-content/5">
                <p class="text-xs sm:text-sm font-bold text-base-content/70 mb-2">Sedang Diproses</p>
                <p class="text-xl sm:text-2xl font-bold text-base-content">{{ $stats['sedang_dimasak'] }} Pesanan</p>
            </div>

            <div class="bg-base-200 rounded-xl p-5 shadow-sm border border-base-content/5">
                <p class="text-xs sm:text-sm font-bold text-base-content/70 mb-2">Siap Pickup</p>
                <p class="text-xl sm:text-2xl font-bold text-base-content">{{ $stats['siap_pickup'] }} Pesanan</p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
            <div class="bg-base-200 rounded-xl p-5 shadow-sm border border-base-content/5">
                <p class="text-xs sm:text-sm font-bold text-base-content/70 mb-2">Total Pendapatan</p>
                <p class="text-xl sm:text-2xl font-bold text-base-content">Rp {{ number_format($stats['total_pendapatan'], 0, ',', '.') }}</p>
            </div>

            <div class="bg-base-200 rounded-xl p-5 shadow-sm border border-base-content/5">
                <p class="text-xs sm:text-sm font-bold text-base-content/70 mb-2">Menu Habis</p>
                <p class="text-xl sm:text-2xl font-bold {{ $stats['menu_habis'] > 0 ? 'text-red-500' : 'text-base-content' }}">{{ $stats['menu_habis'] }} Menu</p>
            </div>
        </div>

        <!-- Active Orders Table -->
        <div class="bg-base-100 rounded-2xl border border-base-content/5 shadow-sm overflow-hidden flex flex-col pb-6">
            <div class="p-4 sm:p-5 border-b border-base-content/5 flex justify-between items-center bg-base-100/50">
                <h2 class="text-sm sm:text-base font-bold text-base-content">Pesanan Aktif</h2>
                <a href="{{ route('vendor.order.index') }}"
                    class="text-xs sm:text-sm text-fern-600 hover:text-fern-700 font-medium px-2 py-1 bg-fern-50 rounded-lg">Lihat
                    Semua</a>
            </div>
            <div class="overflow-x-auto flex-1 p-0">
                <table class="table table-sm w-full min-w-[500px]">
                    <thead class="bg-base-200/50 text-xs">
                        <tr>
                            <th class="font-medium text-base-content/70 py-3 px-4">Order Code & Waktu</th>
                            <th class="font-medium text-base-content/70 py-3 px-4">Mahasiswa</th>
                            <th class="font-medium text-base-content/70 py-3 px-4 text-center">Status</th>
                            <th class="font-medium text-base-content/70 py-3 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-xs sm:text-sm">
                        @forelse($activeOrders ?? [] as $order)
                            <tr class="hover:bg-base-200/30 transition-colors border-b border-base-content/5">
                                <td class="px-4 py-3">
                                    <span class="font-bold text-xs">{{ $order->order_code }}</span><br>
                                    <span class="text-[10px] sm:text-[11px] text-base-content/50">Ambil:
                                        {{ optional($order->pickup_time)->format('H:i') ?? '-' }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <span
                                        class="font-medium truncate max-w-[100px] inline-block">{{ optional($order->user)->name ?? '-' }}</span><br>
                                    <span
                                        class="text-[10px] sm:text-[11px] text-base-content/50">{{ $order->created_at->diffForHumans() }}</span>
                                </td>
                                <td class="text-center px-4 py-3">
                                    <x-status-badge :status="$order->status_label" />
                                </td>
                                <td class="text-center px-4 py-3">
                                    <a href="{{ route('vendor.order.show', $order->id) }}"
                                        class="btn btn-xs sm:btn-sm btn-outline rounded-lg text-fern-600 hover:bg-fern-600 hover:text-white hover:border-fern-600">Proses</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-8 text-base-content/50 text-xs sm:text-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="h-8 w-8 sm:h-10 sm:w-10 mx-auto text-base-content/20 mb-3"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                    Hore! Tidak ada pesanan tertunda saat ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

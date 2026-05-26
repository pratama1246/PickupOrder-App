@extends('layouts.admin')

@section('title', 'Dashboard - Admin PNC')

@section('content')
    <div class="max-w-8xl mx-auto space-y-4 sm:space-y-6 pb-6 lg:pb-0">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4 mb-6 sm:mb-8">
            <div>
                <h1 class="text-2xl sm:text-4xl font-bold text-base-content mb-2">Ikhtisar Platform</h1>
                <p class="text-base-content/70 text-sm sm:text-lg font-medium">Pantau performa dan aktivitas kantin secara
                    real-time.</p>
            </div>
        </div>

        <!-- Stats Row -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
            <x-admin.stat-card label="Pengguna"     value="{{ $stats['total_pengguna'] }} Pengguna" />
            <x-admin.stat-card label="Total Kantin" value="{{ $stats['total_kantin'] }} Kantin" />
            <x-admin.stat-card label="Total Order"  value="{{ $stats['total_order'] }} Pesanan" />
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
            <x-admin.stat-card label="Total Transaksi" value="Rp {{ number_format($stats['total_transaksi'], 0, ',', '.') }}" />
            <x-admin.stat-card label="Total Menu"      value="{{ $stats['total_menu'] }} Menu" />
        </div>

        <!-- Tables Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 pb-6 sm:pb-10">
            <!-- Top Canteens -->
            <div class="bg-base-100 rounded-2xl border border-base-content/5 shadow-sm overflow-hidden flex flex-col">
                <div class="p-4 sm:p-5 border-b border-base-content/5 bg-base-100/50">
                    <h2 class="text-sm sm:text-base font-bold text-base-content">Performa Kantin</h2>
                </div>
                <div class="overflow-x-auto flex-1 p-0">
                    <table class="table table-sm w-full min-w-[400px]">
                        <thead class="bg-base-200/50 text-xs">
                            <tr>
                                <th class="font-medium text-base-content/70 py-3 px-4">Nama Kantin</th>
                                <th class="font-medium text-base-content/70 py-3 px-4 text-center">Pesanan Selesai</th>
                                <th class="font-medium text-base-content/70 py-3 px-4 text-right">Pendapatan</th>
                            </tr>
                        </thead>
                        <tbody class="text-xs sm:text-sm">
                            @forelse($topCanteens ?? [] as $canteen)
                                <tr class="hover:bg-base-200/30 transition-colors border-b border-base-content/5">
                                    <td class="font-medium px-4 py-3">{{ $canteen->name }}</td>
                                    <td class="text-center px-4 py-3">{{ number_format($canteen->completed_orders_count) }}
                                        / {{ number_format($canteen->orders_count) }}</td>
                                    <td class="text-right font-semibold text-fern-600 px-4 py-3">
                                        Rp{{ number_format($canteen->total_revenue ?? 0, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-6 text-base-content/50">Belum ada data
                                        performa kantin.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Transactions -->
            <div class="bg-base-100 rounded-2xl border border-base-content/5 shadow-sm overflow-hidden flex flex-col">
                <div class="p-4 sm:p-5 border-b border-base-content/5 bg-base-100/50">
                    <h2 class="text-sm sm:text-base font-bold text-base-content">Transaksi Terbaru</h2>
                </div>
                <div class="overflow-x-auto flex-1 p-0">
                    <table class="table table-sm w-full min-w-[400px]">
                        <thead class="bg-base-200/50 text-xs">
                            <tr>
                                <th class="font-medium text-base-content/70 py-3 px-4">Order Code</th>
                                <th class="font-medium text-base-content/70 py-3 px-4">Pengguna</th>
                                <th class="font-medium text-base-content/70 py-3 px-4 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="text-xs sm:text-sm">
                            @forelse($recentOrders ?? [] as $order)
                                <tr class="hover:bg-base-200/30 transition-colors border-b border-base-content/5">
                                    <td class="px-4 py-3">
                                        <span
                                            class="font-medium text-[11px] sm:text-xs">{{ $order->order_code }}</span><br>
                                        <span
                                            class="text-[10px] sm:text-xs text-base-content/50">{{ $order->created_at->diffForHumans() }}</span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span
                                            class="font-medium truncate max-w-[100px] sm:max-w-none inline-block">{{ optional($order->user)->name ?? '-' }}</span><br>
                                        <span
                                            class="text-[10px] sm:text-xs text-base-content/50">{{ optional($order->canteen)->name ?? '-' }}</span>
                                    </td>
                                    <td class="text-center px-4 py-3">
                                        <x-status-badge :status="$order->status_label" />
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-6 text-base-content/50">Belum ada transaksi
                                        terbaru.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

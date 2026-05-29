@extends('layouts.vendor')

@section('title', 'Dashboard - Vendor PNC')

@section('content')
    @php
        $dailyTarget = $canteen->daily_target ?? 500000;
        $targetPercentageReal = $dailyTarget > 0 ? round(($stats['pendapatan_hari_ini'] / $dailyTarget) * 100) : 0;
        $chartSeries = min(100, $targetPercentageReal);
    @endphp
    <div class="max-w-8xl mx-auto space-y-4 sm:space-y-6 pb-10 lg:pb-0">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 sm:mb-8">
            <div>
                <h1 class="text-2xl sm:text-4xl font-bold text-base-content mb-2">Dashboard Kantin</h1>
                <p class="text-base-content/70 text-sm sm:text-lg font-medium">Halo, <strong>{{ $canteen->name }}</strong>!
                    Berikut performa hari ini.</p>
            </div>

            <div x-data="{
                isOpen: {{ $canteen->is_open ? 'true' : 'false' }},
                isLoading: false,
                async toggleStatus() {
                    this.isLoading = true;
                    try {
                        const response = await fetch('{{ route('vendor.canteen.toggle') }}', {
                            method: 'PATCH',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({ is_open: this.isOpen })
                        });
                        const data = await response.json();
                        if (data.success) {
                            this.isOpen = data.is_open;
                            $dispatch('notify', { message: data.message, type: 'success' });
                        }
                    } catch (error) {
                        console.error('Error toggling status:', error);
                        this.isOpen = !this.isOpen;
                        $dispatch('notify', { message: 'Gagal mengubah status kantin.', type: 'error' });
                    } finally {
                        this.isLoading = false;
                    }
                }
            }"
                class="bg-base-100 rounded-2xl px-4 py-3 flex sm:items-center justify-between gap-4 border border-base-200 shadow-sm w-full sm:w-fit shrink-0">
                <div>
                    <p class="text-xs font-semibold text-base-content/50">Status Kantin</p>
                    <p class="text-sm font-semibold transition-colors" :class="isOpen ? 'text-emerald-700' : 'text-rose-600'"
                        x-text="isOpen ? 'Buka' : 'Tutup'"></p>
                </div>
                <div class="m-0 p-0 flex items-center shrink-0">
                    <input type="checkbox" x-model="isOpen" @change="toggleStatus" :disabled="isLoading"
                        class="toggle transition-colors duration-300"
                        :class="isOpen ? 'bg-emerald-500 border-emerald-600 hover:bg-emerald-600' :
                            'bg-rose-500 border-rose-600 hover:bg-rose-600'"
                        title="Ubah status operasional kantin" />
                </div>
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

        <!-- Daily Stats Row -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
            <x-stat-card label="Pendapatan Hari Ini"
                value="Rp{{ number_format($stats['pendapatan_hari_ini'], 0, ',', '.') }}" :growth="$stats['pendapatan_growth']"
                subtext="vs kemarin" variant="highlight">
                <x-slot:icon>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </x-slot:icon>
            </x-stat-card>

            <x-stat-card label="Pesanan Hari Ini" value="{{ $stats['pesanan_hari_ini'] }}" :growth="$stats['pesanan_growth']"
                subtext="vs kemarin" iconBg="bg-emerald-50 text-fern-700" variant="emerald">
                <x-slot:icon>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007z" />
                    </svg>
                </x-slot:icon>
            </x-stat-card>

            <x-stat-card label="Rata-rata Nilai Pesanan" value="Rp{{ number_format($stats['aov_hari_ini'], 0, ',', '.') }}"
                iconBg="bg-emerald-50 text-fern-700" variant="vanilla">
                <x-slot:icon>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                    </svg>
                </x-slot:icon>
            </x-stat-card>

            <x-stat-card label="Tingkat Penyelesaian" value="{{ $stats['completion_rate'] }}%"
                iconBg="bg-emerald-50 text-fern-700" variant="spruce">
                <x-slot:icon>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </x-slot:icon>
            </x-stat-card>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6 mb-6">
            <!-- Active Orders Table -->
            <div class="lg:col-span-2 bg-base-100 rounded-2xl shadow-sm overflow-hidden flex flex-col">
                <div class="p-4 sm:p-5 border-b border-base-200 flex justify-between items-center bg-vanilla-custard-50">
                    <h2 class="text-md sm:text-lg font-semibold text-base-content">Pesanan Aktif</h2>
                    <a href="{{ route('vendor.order.index') }}"
                        class="text-xs sm:text-sm text-fern-600 hover:text-fern-700 font-medium px-3 py-1.5 rounded-lg hover:bg-fern-200 transition-colors">Lihat
                        Semua</a>
                </div>
                <div class="overflow-auto flex-1 p-0">
                    <table class="table table-sm w-full min-w-[500px] table-pin-rows">
                        <thead class="bg-base-200 text-xs">
                            <tr>
                                <th class="font-medium text-base-content/70 py-3 px-4">No. Order & Waktu</th>
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

            <!-- Status Antrean (Queue Status) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:flex lg:flex-col gap-3 sm:gap-4 h-full">
                <!-- Card Menunggu (Bold / Highlighted) -->
                <a href="{{ route('vendor.order.index', ['status' => 'menunggu']) }}"
                    class="flex-1 bg-linear-to-br from-fern-700 to-fern-900 text-white rounded-2xl p-4 sm:p-5 shadow-sm flex flex-col justify-between transition-all duration-300">
                    <p class="text-sm font-medium text-fern-100">Menunggu</p>
                    <div class="flex items-center justify-between gap-4 my-auto py-2">
                        <p class="text-3xl sm:text-4xl font-semibold text-white">
                            {{ $stats['pesanan_baru'] }}
                        </p>
                        <div class="bg-fern-800 text-fern-100 p-3 rounded-xl">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="pt-3 text-[11px] sm:text-xs text-fern-200 font-medium">
                        Perlu segera diproses
                    </div>
                </a>

                <!-- Card Dimasak -->
                <a href="{{ route('vendor.order.index', ['status' => 'dimasak']) }}"
                    class="flex-1 bg-linear-to-br from-vanilla-custard-50 to-base-100 rounded-2xl p-4 sm:p-5 shadow-sm border border-base-200 flex flex-col justify-between transition-all duration-300">
                    <p class="text-sm font-medium text-base-content/60">Dimasak</p>
                    <div class="flex items-center justify-between gap-4 my-auto py-2">
                        <p class="text-3xl sm:text-4xl font-semibold text-vanilla-custard-700">
                            {{ $stats['sedang_dimasak'] }}
                        </p>
                        <div class="bg-vanilla-custard-100 text-vanilla-custard-700 p-3 rounded-xl">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2 12h20" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 12v8a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-8" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4 8 16-4" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="m8.86 6.78-.45-1.81a2 2 0 0 1 1.45-2.43l1.94-.48a2 2 0 0 1 2.43 1.45l.45 1.81" />
                            </svg>
                        </div>
                    </div>
                    <div class="pt-3 border-t border-base-200/60 text-[11px] sm:text-xs text-base-content/50 font-medium">
                        Sedang dalam proses masak
                    </div>
                </a>

                <!-- Card Siap Pickup -->
                <a href="{{ route('vendor.order.index', ['status' => 'siap_diambil']) }}"
                    class="flex-1 bg-linear-to-br from-emerald-50 to-base-100 rounded-2xl p-4 sm:p-5 shadow-sm border border-base-200 flex flex-col justify-between transition-all duration-300">
                    <p class="text-sm font-medium text-base-content/60">Siap Pickup</p>
                    <div class="flex items-center justify-between gap-4 my-auto py-2">
                        <p class="text-3xl sm:text-4xl font-semibold text-emerald-600">
                            {{ $stats['siap_pickup'] }}
                        </p>
                        <div class="bg-emerald-100 text-emerald-700 p-3 rounded-xl">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z" />
                            </svg>
                        </div>
                    </div>
                    <div class="pt-3 border-t border-base-200/60 text-[11px] sm:text-xs text-base-content/50 font-medium">
                        Siap diambil pelanggan
                    </div>
                </a>

                <!-- Card Dibatalkan -->
                <a href="{{ route('vendor.order.index', ['status' => 'dibatalkan']) }}"
                    class="flex-1 bg-linear-to-br from-rose-50 to-base-100 rounded-2xl p-4 sm:p-5 shadow-sm border border-base-200 flex flex-col justify-between transition-all duration-300">
                    <p class="text-sm font-medium text-base-content/60">Dibatalkan</p>
                    <div class="flex items-center justify-between gap-4 my-auto py-2">
                        <p class="text-3xl sm:text-4xl font-semibold text-rose-700">
                            {{ $stats['pesanan_batal'] }}
                        </p>
                        <div class="bg-rose-100 text-rose-700 p-3 rounded-xl">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                            </svg>
                        </div>
                    </div>
                    <div class="pt-3 border-t border-base-200/60 text-[11px] sm:text-xs text-base-content/50 font-medium">
                        Pesanan berakhir batal
                    </div>
                </a>
            </div>
        </div>

        <!-- Best Sellers & Category Distribution -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6 mb-6">
            <!-- Best Sellers Chart -->
            <div
                class="lg:col-span-2 bg-vanilla-custard-50 rounded-2xl border border-base-200 shadow-sm p-4 sm:p-5 flex flex-col justify-between">
                <h2 class="text-base font-semibold text-base-content mb-4">Top 5 Menu Laris</h2>
                <div id="bestSellerChart" class="w-full h-75"></div>
            </div>

            <!-- Target Pendapatan Harian -->
            <div
                class="lg:col-span-1 bg-base-100 rounded-2xl shadow-sm p-5 sm:p-6 flex flex-col justify-between relative overflow-hidden">
                <!-- Background decoration glow -->
                <div class="absolute -top-12 -right-12 w-32 h-32 bg-vanilla-custard-100/50 rounded-full blur-2xl"></div>
                <div class="absolute -bottom-10 -left-10 w-32 h-32 bg-emerald-50 rounded-full blur-2xl"></div>

                @php
                    $remaining      = max(0, $dailyTarget - $stats['pendapatan_hari_ini']);
                    $aovToday       = $stats['aov_hari_ini'] ?? 0;
                    $ordersNeeded   = ($aovToday > 0 && $remaining > 0) ? (int) ceil($remaining / $aovToday) : null;
                @endphp
                <div class="relative z-10 w-full flex flex-col h-full gap-4">

                    {{-- Header --}}
                    <div class="flex items-start justify-between">
                        <div>
                            <h2 class="text-base font-semibold text-base-content">Target Pendapatan Hari Ini</h2>
                            <p class="text-xs text-base-content/50 font-medium mt-0.5">Progres menuju target harian</p>
                        </div>
                        <button onclick="document.getElementById('editTargetModal').showModal()"
                            class="btn btn-sm btn-circle btn-ghost text-fern-600 hover:text-fern-800 hover:bg-fern-50 transition-colors tooltip tooltip-left shrink-0"
                            data-tip="Ubah Target">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                        </button>
                    </div>

                    {{-- Primary revenue figure --}}
                    <div>
                        <div class="flex items-baseline gap-1.5 flex-wrap">
                            <span class="text-3xl sm:text-4xl font-bold text-fern-700 leading-none">
                                Rp{{ number_format($stats['pendapatan_hari_ini'], 0, ',', '.') }}
                            </span>
                        </div>
                        <p class="text-xs text-base-content/50 font-medium mt-1.5">Pendapatan hari ini</p>
                    </div>

                    {{-- Progress bar + percentage --}}
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-[11px] font-semibold text-base-content/50 tracking-wide">Progres</span>
                            <span class="text-xs font-bold {{ $targetPercentageReal >= 100 ? 'text-emerald-600' : 'text-fern-700' }}">
                                {{ $targetPercentageReal }}%
                            </span>
                        </div>
                        <div class="w-full h-2 bg-base-200 rounded-full overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-500
                                {{ $targetPercentageReal >= 100 ? 'bg-emerald-500' : 'bg-fern-500' }}"
                                style="width: {{ min(100, $targetPercentageReal) }}%">
                            </div>
                        </div>
                    </div>

                    {{-- 3-column breakdown: Target / Tercapai / Sisa --}}
                    <div class="grid grid-cols-2 gap-2 bg-base-200/40 rounded-xl p-3">
                        <div>
                            <p class="text-[10px] font-semibold text-base-content/40 uppercase tracking-wide mb-1">Target</p>
                            <p class="text-xs font-bold text-base-content leading-tight">Rp{{ number_format($dailyTarget, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-semibold text-base-content/40 uppercase tracking-wide mb-1">Sisa</p>
                            <p class="text-xs font-bold {{ $remaining > 0 ? 'text-amber-600' : 'text-emerald-600' }} leading-tight">
                                {{ $remaining > 0 ? 'Rp'.number_format($remaining, 0, ',', '.') : 'Lunas' }}
                            </p>
                        </div>
                    </div>

                    {{-- Contextual orders-needed hint --}}
                    @if ($ordersNeeded !== null)
                        <div class="flex items-center gap-2 px-3 py-2 rounded-md bg-fern-50 border border-fern-100">
                            <p class="text-xs font-medium text-fern-800 leading-snug">
                                Butuh sekitar
                                <span class="font-bold">{{ $ordersNeeded }} pesanan</span>
                                lagi &mdash; rata-rata
                                <span class="font-semibold">Rp{{ number_format($aovToday, 0, ',', '.') }}</span>
                                per pesanan
                            </p>
                        </div>
                    @endif

                    {{-- Status footer: qualitative insight, not a repeat of the Sisa column --}}
                    <div class="mt-auto pt-3 border-t border-base-200/70">
                        @php
                            [$footerDot, $footerText, $footerColor] = match (true) {
                                $targetPercentageReal >= 100 => [
                                    'bg-emerald-500',
                                    'Target hari ini sudah tercapai!',
                                    'text-emerald-600 font-semibold',
                                ],
                                $targetPercentageReal >= 75 => [
                                    'bg-fern-500',
                                    'Tinggal sedikit lagi untuk mencapai target hari ini!',
                                    'text-fern-700 font-medium',
                                ],
                                $targetPercentageReal >= 50 => [
                                    'bg-fern-400',
                                    'Hampir separuh target hari ini sudah tercapai!',
                                    'text-fern-700 font-medium',
                                ],
                                $targetPercentageReal >= 25 => [
                                    'bg-amber-400',
                                    'Progres penjualan berjalan sesuai target.',
                                    'text-base-content/60 font-medium',
                                ],
                                $stats['pesanan_hari_ini'] > 0 => [
                                    'bg-amber-300',
                                    'Masih ada waktu untuk mengejar target hari ini.',
                                    'text-base-content/60 font-medium',
                                ],
                                default => [
                                    'bg-base-content/20',
                                    'Belum ada pesanan selesai hari ini.',
                                    'text-base-content/40 font-medium',
                                ],
                            };
                            $showGrowth = $targetPercentageReal < 100 && ($stats['pendapatan_growth'] ?? 0) > 0;
                        @endphp
                        <div class="flex items-start gap-2">
                            <span class="mt-1 w-2 h-2 rounded-full {{ $footerDot }} shrink-0"></span>
                            <div>
                                <p class="text-xs {{ $footerColor }}">{{ $footerText }}</p>
                                @if ($showGrowth)
                                    <p class="text-[11px] text-fern-600 font-semibold mt-0.5">
                                        +{{ $stats['pendapatan_growth'] }}% lebih baik dari kemarin
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Edit Target (Menggunakan Bawaan Proyek) -->
                <x-modal id="editTargetModal" title="Atur Target Harian" :showFooter="false">
                    <form action="{{ route('vendor.canteen.target') }}" method="POST" class="mt-2">
                        @csrf
                        @method('PATCH')

                        <div class="form-control mb-5">
                            <label class="label px-0 pt-0 pb-2">
                                <span class="label-text font-bold">Nominal Target (Rp)</span>
                            </label>
                            <input type="number" name="daily_target" value="{{ $dailyTarget }}"
                                class="input input-bordered w-full rounded-xl focus:outline-fern-600 focus:border-fern-600"
                                min="1" required />
                            <label class="label px-0 pb-0 pt-2">
                                <span class="label-text-alt text-base-content/50 font-medium">Masukkan tanpa titik. Contoh:
                                    500000</span>
                            </label>
                        </div>

                        <button type="submit"
                            class="btn bg-fern-700 hover:bg-fern-800 text-white w-full rounded-xl border-none">
                            Simpan Perubahan
                        </button>
                    </form>
                </x-modal>
            </div>
        </div>

        <!-- Rating & Trend Penjualan -->
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-4 sm:gap-6 mb-6">
            <!-- Rating Kantin -->
            <div
                class="lg:col-span-2 bg-base-100 rounded-2xl border border-base-200 shadow-sm p-4 sm:p-5 flex flex-col justify-between">
                <div>
                    <h2 class="text-base font-semibold text-base-content mb-4">Performa Ulasan</h2>
                    <div class="flex items-center gap-6 mb-5">
                        <div class="text-center shrink-0">
                            <p class="text-5xl font-semibold text-fern-700">
                                {{ $avgRating > 0 ? number_format($avgRating, 1) : '5.0' }}</p>
                            <div class="flex items-center justify-center gap-0.5 mt-1">
                                @for ($i = 1; $i <= 5; $i++)
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="w-4 h-4 {{ $i <= round($avgRating ?: 5) ? 'text-amber-400' : 'text-base-content/20' }}"
                                        viewBox="0 0 20 20" fill="currentColor">
                                        <path
                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                @endfor
                            </div>
                            <p class="text-xs text-base-content/50 mt-1 font-medium">{{ $totalReviews }} ulasan</p>
                        </div>
                        <div class="flex-1 min-w-0 space-y-2">
                            @foreach ([5, 4, 3, 2, 1] as $star)
                                @php $pct = $totalReviews > 0 ? (int) (\App\Models\Review::whereHas('menu', fn($q) => $q->where('canteen_id', $canteen->id))->where('rating', $star)->count() / $totalReviews * 100) : 0; @endphp
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-bold text-base-content/60 w-3">{{ $star }}</span>
                                    <div class="flex-1 bg-base-200 rounded-full h-2 overflow-hidden">
                                        <div class="h-full bg-amber-400 rounded-full transition-all"
                                            style="width: {{ $pct }}%"></div>
                                    </div>
                                    <span class="text-xs text-base-content/50 w-7 text-right">{{ $pct }}%</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Recent Reviews -->
                <div class="space-y-3 max-h-56 overflow-y-auto pr-1">
                    @forelse($recentReviews as $review)
                        <div class="flex items-start gap-3 p-3 bg-base-200/40 rounded-2xl">
                            <img src="{{ $review->reviewer_avatar }}" class="w-8 h-8 rounded-full object-cover shrink-0"
                                alt="{{ $review->reviewer_name }}">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-2">
                                    <p class="text-xs font-bold text-base-content truncate">{{ $review->reviewer_name }}
                                    </p>
                                    <div class="flex items-center gap-0.5 shrink-0">
                                        @for ($s = 1; $s <= 5; $s++)
                                            <svg class="w-3 h-3 {{ $s <= $review->rating ? 'text-amber-400' : 'text-base-content/20' }}"
                                                viewBox="0 0 20 20" fill="currentColor">
                                                <path
                                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                        @endfor
                                    </div>
                                </div>
                                <p class="text-[11px] text-base-content/50 font-medium">{{ $review->menu->name ?? '-' }}
                                </p>
                                @if ($review->comment)
                                    <p class="text-xs text-base-content/70 mt-0.5 line-clamp-1">{{ $review->comment }}</p>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-base-content/50 text-center py-4 font-medium">Belum ada ulasan.</p>
                    @endforelse
                </div>
            </div>

            <!-- Revenue & Orders Chart -->
            <div
                class="lg:col-span-3 bg-vanilla-custard-50 rounded-2xl shadow-sm p-4 sm:p-5 flex flex-col justify-between">
                <h2 class="text-base font-semibold text-base-content mb-4">Tren Transaksi 7 Hari Terakhir</h2>
                <div id="trendChart" class="w-full h-75"></div>
            </div>
        </div>

    </div>
@endsection


@push('scripts')
    <script>
        window.addEventListener("load", function() {
            // Trend Chart
            const trendOptions = {
                series: [{
                    name: 'Pendapatan',
                    type: 'area',
                    data: @json($trendRevenues)
                }, {
                    name: 'Volume Transaksi',
                    type: 'line',
                    data: @json($trendOrders)
                }],
                chart: {
                    height: 300,
                    type: 'line',
                    toolbar: {
                        show: false
                    },
                    fontFamily: 'Poppins, sans-serif'
                },
                stroke: {
                    curve: 'smooth',
                    width: [2, 3]
                },
                fill: {
                    type: ['gradient', 'solid'],
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.4,
                        opacityTo: 0.05,
                        stops: [0, 90, 100]
                    }
                },
                colors: ['#306939', '#73c780'],
                labels: @json($trendDates),
                xaxis: {
                    tooltip: {
                        enabled: false
                    }
                },
                yaxis: [{
                    title: {
                        text: 'Pendapatan (Rp)',
                        style: {
                            fontWeight: 600,
                            fontFamily: 'Poppins'
                        }
                    },
                    labels: {
                        formatter: (value) => {
                            return "Rp" + value.toLocaleString("id-ID");
                        }
                    }
                }, {
                    opposite: true,
                    title: {
                        text: 'Transaksi',
                        style: {
                            fontWeight: 600,
                            fontFamily: 'Poppins'
                        }
                    }
                }],
                legend: {
                    position: 'top',
                    horizontalAlign: 'right'
                }
            };
            const trendChart = new ApexCharts(document.querySelector("#trendChart"), trendOptions);
            trendChart.render();

            // Best Sellers Chart
            const bestSellerOptions = {
                series: [{
                    name: 'Terjual',
                    data: @json($topMenuSeries)
                }],
                chart: {
                    type: 'bar',
                    height: 300,
                    toolbar: {
                        show: false
                    },
                    fontFamily: 'Poppins, sans-serif'
                },
                plotOptions: {
                    bar: {
                        borderRadius: 4,
                        horizontal: true,
                    }
                },
                colors: ['#306939'],
                dataLabels: {
                    enabled: false
                },
                xaxis: {
                    categories: @json($topMenuLabels),
                    labels: {
                        style: {
                            colors: '#64748b',
                            fontFamily: 'Poppins, sans-serif'
                        }
                    },
                    axisBorder: {
                        show: true,
                        color: 'rgba(0, 0, 0, 0.1)'
                    },
                    axisTicks: {
                        show: false
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: '#64748b',
                            fontFamily: 'Poppins, sans-serif'
                        }
                    }
                },
                grid: {
                    borderColor: 'rgba(0, 0, 0, 0.05)',
                    xaxis: {
                        lines: {
                            show: false
                        }
                    },
                    yaxis: {
                        lines: {
                            show: false
                        }
                    }
                }
            };
            const bestSellerChart = new ApexCharts(document.querySelector("#bestSellerChart"), bestSellerOptions);
            bestSellerChart.render();

        });
    </script>
@endpush

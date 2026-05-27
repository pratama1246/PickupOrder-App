@extends('layouts.vendor')

@section('title', 'Dashboard - Vendor PNC')

@section('content')
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
                    <p class="text-xs font-bold text-base-content/50 uppercase">Status Kantin</p>
                    <p class="text-sm font-bold transition-colors" :class="isOpen ? 'text-emerald-700' : 'text-rose-600'"
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
                subtext="vs kemarin" iconBg="bg-emerald-50 text-fern-700">
                <x-slot:icon>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </x-slot:icon>
            </x-stat-card>

            <x-stat-card label="Pesanan Hari Ini" value="{{ $stats['pesanan_hari_ini'] }}" :growth="$stats['pesanan_growth']"
                subtext="vs kemarin" iconBg="bg-emerald-50 text-fern-700">
                <x-slot:icon>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007z" />
                    </svg>
                </x-slot:icon>
            </x-stat-card>

            <x-stat-card label="Rata-rata Nilai Pesanan"
                value="Rp{{ number_format($stats['aov_hari_ini'], 0, ',', '.') }}" iconBg="bg-emerald-50 text-fern-700">
                <x-slot:icon>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                    </svg>
                </x-slot:icon>
            </x-stat-card>

            <x-stat-card label="Tingkat Penyelesaian" value="{{ $stats['completion_rate'] }}%"
                iconBg="bg-emerald-50 text-fern-700">
                <x-slot:icon>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </x-slot:icon>
            </x-stat-card>
        </div>

        <!-- Queue Status -->
        <div class="grid grid-cols-2 md:grid-cols-3 gap-3 sm:gap-4 mb-6">
            <div class="bg-base-100 rounded-2xl p-4 shadow-sm border border-base-200 text-center">
                <p class="text-xs sm:text-sm font-bold text-base-content/60 mb-1">Menunggu</p>
                <p class="text-xl sm:text-2xl font-extrabold text-fern-600">{{ $stats['pesanan_baru'] }}</p>
            </div>
            <div class="bg-base-100 rounded-2xl p-4 shadow-sm border border-base-200 text-center">
                <p class="text-xs sm:text-sm font-bold text-base-content/60 mb-1">Dimasak</p>
                <p class="text-xl sm:text-2xl font-extrabold text-fern-600">{{ $stats['sedang_dimasak'] }}</p>
            </div>
            <div class="col-span-2 md:col-span-1 bg-base-100 rounded-2xl p-4 shadow-sm border border-base-200 text-center">
                <p class="text-xs sm:text-sm font-bold text-base-content/60 mb-1">Siap Pickup</p>
                <p class="text-xl sm:text-2xl font-extrabold text-fern-600">{{ $stats['siap_pickup'] }}</p>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6 mb-6">
            <!-- Revenue & Orders Chart -->
            <div class="lg:col-span-2 bg-base-100 rounded-3xl border border-base-200 shadow-sm p-4 sm:p-5">
                <h2 class="text-base font-bold text-base-content mb-4">Tren Transaksi 7 Hari Terakhir</h2>
                <div id="trendChart" class="w-full h-[300px]"></div>
            </div>

            <!-- Best Sellers Chart -->
            <div class="bg-base-100 rounded-3xl border border-base-200 shadow-sm p-4 sm:p-5">
                <h2 class="text-base font-bold text-base-content mb-4">Top 5 Menu Laris</h2>
                <div id="bestSellerChart" class="w-full h-[300px]"></div>
            </div>
        </div>

        <!-- Status & Active Orders -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6 mb-6">
            <div class="bg-base-100 rounded-3xl border border-base-200 shadow-sm p-4 sm:p-5">
                <h2 class="text-base font-bold text-base-content mb-4">Distribusi Status Pesanan</h2>
                <div id="statusChart" class="w-full h-[300px]"></div>
            </div>

            <!-- Active Orders Table -->
            <div
                class="lg:col-span-2 bg-base-100 rounded-3xl border border-base-200 shadow-sm overflow-hidden flex flex-col">
                <div class="p-4 sm:p-5 border-b border-base-200 flex justify-between items-center bg-base-100/50">
                    <h2 class="text-sm sm:text-base font-bold text-base-content">Pesanan Aktif</h2>
                    <a href="{{ route('vendor.order.index') }}"
                        class="text-xs sm:text-sm text-fern-600 hover:text-fern-700 font-medium px-3 py-1.5 bg-fern-50 rounded-lg hover:bg-fern-100 transition-colors">Lihat
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
                colors: ['#4d9959'],
                dataLabels: {
                    enabled: false
                },
                xaxis: {
                    categories: @json($topMenuLabels),
                }
            };
            const bestSellerChart = new ApexCharts(document.querySelector("#bestSellerChart"), bestSellerOptions);
            bestSellerChart.render();

            // Status Chart
            const statusOptions = {
                series: @json($statusSeries),
                labels: @json($statusLabels),
                chart: {
                    type: 'donut',
                    height: 300,
                    fontFamily: 'Poppins, sans-serif'
                },
                colors: ['#f59e0b', '#3b82f6', '#10b981', '#4d9959', '#ef4444'],
                stroke: {
                    width: 0
                },
                dataLabels: {
                    enabled: false
                },
                legend: {
                    position: 'bottom'
                },
            };
            const statusChart = new ApexCharts(document.querySelector("#statusChart"), statusOptions);
            statusChart.render();
        });
    </script>
@endpush

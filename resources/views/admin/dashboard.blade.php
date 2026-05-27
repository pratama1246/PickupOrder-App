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
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 mb-6">
            <x-stat-card label="Total Pendapatan" value="Rp{{ number_format($stats['total_pendapatan'], 0, ',', '.') }}"
                :growth="$stats['pendapatan_growth']" subtext="vs 7 hari lalu" iconBg="bg-emerald-50 text-fern-700">
                <x-slot:icon>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </x-slot:icon>
            </x-stat-card>

            <x-stat-card label="Volume Transaksi" value="{{ number_format($stats['volume_transaksi'], 0, ',', '.') }}"
                :growth="$stats['transaksi_growth']" subtext="vs 7 hari lalu" iconBg="bg-emerald-50 text-fern-700">
                <x-slot:icon>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                    </svg>
                </x-slot:icon>
            </x-stat-card>

            <x-stat-card label="Rata-rata Nilai Pesanan" value="Rp{{ number_format($stats['aov'], 0, ',', '.') }}"
                iconBg="bg-emerald-50 text-fern-700">
                <x-slot:icon>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                    </svg>
                </x-slot:icon>
            </x-stat-card>

            <x-stat-card label="Total Pengguna" value="{{ number_format($stats['total_pengguna'], 0, ',', '.') }}"
                iconBg="bg-emerald-50 text-fern-700">
                <x-slot:icon>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                    </svg>
                </x-slot:icon>
            </x-stat-card>

            <x-stat-card label="Total Kantin" value="{{ number_format($stats['total_kantin'], 0, ',', '.') }}"
                iconBg="bg-emerald-50 text-fern-700">
                <x-slot:icon>
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m2 7 4.41-4.41A2 2 0 0 1 7.83 2h8.34a2 2 0 0 1 1.42.59L22 7" />
                        <path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8" />
                        <path d="M15 22v-4a2 2 0 0 0-2-2h-2a2 2 0 0 0-2 2v4" />
                        <path d="M2 7h20" />
                        <path d="M22 7v3a2 2 0 0 1-2 2v0a2 2 0 0 1-2-2V7" />
                        <path d="M18 7v3a2 2 0 0 1-2 2v0a2 2 0 0 1-2-2V7" />
                        <path d="M14 7v3a2 2 0 0 1-2 2v0a2 2 0 0 1-2-2V7" />
                        <path d="M10 7v3a2 2 0 0 1-2 2v0a2 2 0 0 1-2-2V7" />
                        <path d="M6 7v3a2 2 0 0 1-2 2v0a2 2 0 0 1-2-2V7" />
                    </svg>
                </x-slot:icon>
            </x-stat-card>

            <x-stat-card label="Total Menu" value="{{ number_format($stats['total_menu'], 0, ',', '.') }}"
                iconBg="bg-emerald-50 text-fern-700">
                <x-slot:icon>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0ZM3.75 12h.007v.008H3.75V12Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm-.375 5.25h.007v.008H3.75v-.008Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                    </svg>
                </x-slot:icon>
            </x-stat-card>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6 mb-6">
            <!-- Revenue & Orders Chart -->
            <div class="lg:col-span-2 bg-base-100 rounded-3xl border border-base-200 shadow-sm p-4 sm:p-5">
                <h2 class="text-base font-bold text-base-content mb-4">Tren Transaksi 7 Hari Terakhir</h2>
                <div id="trendChart" class="w-full h-[300px]"></div>
            </div>

            <!-- Market Share Chart -->
            <div class="bg-base-100 rounded-3xl border border-base-200 shadow-sm p-4 sm:p-5">
                <h2 class="text-base font-bold text-base-content mb-4">Market Share Kantin</h2>
                <div id="shareChart" class="w-full h-[300px]"></div>
            </div>
        </div>

        <!-- Charts Row 2 (Bar Charts) -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 mb-6">
            <!-- Top Canteens Revenue Bar Chart -->
            <div class="bg-base-100 rounded-3xl border border-base-200 shadow-sm p-4 sm:p-5">
                <h2 class="text-base font-bold text-base-content mb-4">5 Kantin Pendapatan Tertinggi</h2>
                <div id="topCanteensChart" class="w-full h-[300px]"></div>
            </div>

            <!-- Top Menus Bar Chart -->
            <div class="bg-base-100 rounded-3xl border border-base-200 shadow-sm p-4 sm:p-5">
                <h2 class="text-base font-bold text-base-content mb-4">5 Menu Paling Laris</h2>
                <div id="topMenusChart" class="w-full h-[300px]"></div>
            </div>
        </div>

        <!-- Tables Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 pb-6 sm:pb-10">
            <!-- Top Canteens -->
            <div class="bg-base-100 rounded-3xl border border-base-200 shadow-sm overflow-hidden flex flex-col">
                <div class="p-4 sm:p-5 border-b border-base-200">
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
            <div class="bg-base-100 rounded-3xl border border-base-200 shadow-sm overflow-hidden flex flex-col">
                <div class="p-4 sm:p-5 border-b border-base-200">
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
                colors: ['#306939', '#3b82f6'],
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

            // Share Chart
            const shareOptions = {
                series: @json($shareSeries),
                labels: @json($shareLabels),
                chart: {
                    type: 'donut',
                    height: 300,
                    fontFamily: 'Poppins, sans-serif'
                },
                colors: ['#306939', '#4d9959', '#73c780', '#a3e6af', '#d1f4d6', '#cbd5e1'],
                stroke: {
                    width: 0
                },
                dataLabels: {
                    enabled: false
                },
                legend: {
                    position: 'bottom'
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return "Rp" + val.toLocaleString("id-ID")
                        }
                    }
                }
            };

            const shareChart = new ApexCharts(document.querySelector("#shareChart"), shareOptions);
            shareChart.render();

            // Top Canteens Bar Chart
            const topCanteensOptions = {
                series: [{
                    name: 'Pendapatan',
                    data: @json($topCanteenSeries)
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
                        horizontal: false,
                        columnWidth: '55%',
                    }
                },
                colors: ['#306939'],
                dataLabels: {
                    enabled: false
                },
                xaxis: {
                    categories: @json($topCanteenLabels),
                },
                yaxis: {
                    labels: {
                        formatter: (value) => {
                            return "Rp" + (value >= 1000 ? (value / 1000).toLocaleString("id-ID") + "K" :
                                value.toLocaleString("id-ID"));
                        }
                    }
                }
            };
            const topCanteensChart = new ApexCharts(document.querySelector("#topCanteensChart"),
            topCanteensOptions);
            topCanteensChart.render();

            // Top Menus Bar Chart
            const topMenusOptions = {
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
            const topMenusChart = new ApexCharts(document.querySelector("#topMenusChart"), topMenusOptions);
            topMenusChart.render();
        });
    </script>
@endpush

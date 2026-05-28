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

            <x-stat-card label="Rata-rata Rating" value="{{ $stats['avg_rating'] > 0 ? number_format($stats['avg_rating'], 1) : '5.0' }}"
                iconBg="bg-amber-50 text-amber-600">
                <x-slot:icon>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
                        <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.006z" clip-rule="evenodd" />
                    </svg>
                </x-slot:icon>
            </x-stat-card>

            <x-stat-card label="Total Ulasan" value="{{ number_format($stats['total_ulasan'], 0, ',', '.') }}"
                iconBg="bg-amber-50 text-amber-600">
                <x-slot:icon>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" />
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

        <!-- Charts Row 3: Category Distribution -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 mb-6">
            <div class="bg-base-100 rounded-3xl border border-base-200 shadow-sm p-4 sm:p-5">
                <h2 class="text-base font-bold text-base-content mb-4">Distribusi Penjualan per Kategori</h2>
                @if(count($categoryLabels) > 0)
                    <div id="categoryDistChart" class="w-full h-[300px]"></div>
                @else
                    <div class="flex items-center justify-center h-[300px] text-base-content/40">
                        <p class="text-sm font-medium">Belum ada data. Pastikan menu memiliki kategori.</p>
                    </div>
                @endif
            </div>
            <div class="bg-base-100 rounded-3xl border border-base-200 shadow-sm p-4 sm:p-5 flex flex-col justify-center items-center gap-3">
                <p class="text-xs font-bold text-base-content/50 uppercase tracking-wider">Rating Platform</p>
                <p class="text-7xl font-extrabold text-fern-700">{{ $stats['avg_rating'] > 0 ? number_format($stats['avg_rating'], 1) : '5.0' }}</p>
                <div class="flex items-center gap-1">
                    @for($i = 1; $i <= 5; $i++)
                        <svg class="w-6 h-6 {{ $i <= round($stats['avg_rating'] ?: 5) ? 'text-amber-400' : 'text-base-content/20' }}" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    @endfor
                </div>
                <p class="text-base-content/60 text-sm font-medium">dari {{ number_format($stats['total_ulasan']) }} ulasan mahasiswa</p>
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

            // Category Distribution Chart
            @if(count($categoryLabels) > 0)
            const categoryDistOptions = {
                series: @json($categorySeries),
                labels: @json($categoryLabels),
                chart: { type: 'donut', height: 300, fontFamily: 'Poppins, sans-serif' },
                colors: ['#f97316', '#0ea5e9', '#f59e0b', '#4d9959', '#a855f7'],
                stroke: { width: 0 },
                dataLabels: { enabled: true, formatter: (val) => Math.round(val) + '%' },
                legend: { position: 'bottom' },
                tooltip: { y: { formatter: (val) => val + ' porsi' } }
            };
            const categoryDistChart = new ApexCharts(document.querySelector("#categoryDistChart"), categoryDistOptions);
            categoryDistChart.render();
            @endif
        });
    </script>
@endpush


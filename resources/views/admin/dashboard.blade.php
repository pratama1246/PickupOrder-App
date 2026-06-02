@extends('layouts.admin')

@section('title', 'Dashboard - Admin PNC')

@section('content')
    <div class="max-w-8xl mx-auto space-y-4 sm:space-y-6 pb-6 lg:pb-0">

        <div class="mb-6 sm:mb-8">
            <h1 class="text-2xl sm:text-4xl font-bold text-base-content mb-2">Pemantauan Platform</h1>
            <p class="text-base-content/70 text-sm sm:text-lg font-medium">Monitoring performa & aktivitas kantin
                PNC secara real-time.</p>
        </div>

        <div
            class="bg-linear-to-br from-fern-700 to-fern-900 rounded-2xl pt-5 pb-7 sm:pt-6 sm:pb-8 md:pt-7 md:pb-9 px-6 sm:px-8 shadow-sm">
            <div class="mb-4 flex">
                <div
                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-white/10 text-white border border-white/5">
                    {{ now()->locale('id')->translatedFormat('l, d F Y') }}
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 lg:gap-12 items-start">
                <div class="lg:col-span-1 flex flex-col justify-between">
                    <div>
                        <p class="text-sm font-medium text-fern-300 mb-3">Total Pendapatan Platform</p>
                        <p class="text-4xl sm:text-5xl font-semibold text-white leading-none">
                            Rp{{ number_format($stats['total_pendapatan'], 0, ',', '.') }}
                        </p>
                        <div class="flex items-center gap-2 mt-5">
                            @php $isPos = $stats['pendapatan_growth'] >= 0; @endphp
                            <span
                                class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-xs font-bold {{ $isPos ? 'bg-fern-600/50 text-fern-100' : 'bg-rose-500/30 text-rose-200' }}">
                                @if ($isPos)
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                        stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941" />
                                    </svg>
                                    +{{ abs($stats['pendapatan_growth']) }}%
                                @else
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                        stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M2.25 6L9 12.75l4.286-4.286a11.948 11.948 0 014.306 6.43l.776 2.898m0 0l3.182-5.514m-3.182 5.514l-5.514-3.182" />
                                    </svg>
                                    {{ abs($stats['pendapatan_growth']) }}%
                                @endif
                            </span>
                            <span class="text-xs text-fern-300 font-medium">vs 7 hari lalu</span>
                        </div>
                    </div>
                </div>

                <div class="hidden lg:block self-stretch"></div>

                <div class="lg:col-span-1 grid grid-cols-2 gap-4 sm:gap-5">
                    <div>
                        <p class="text-sm font-medium text-fern-300 mb-3">Volume Transaksi</p>
                        <p class="text-3xl sm:text-4xl font-semibold text-white">
                            {{ number_format($stats['volume_transaksi'], 0, ',', '.') }}</p>
                        <div class="flex items-center gap-1.5 mt-3">
                            @php $isPosT = $stats['transaksi_growth'] >= 0; @endphp
                            <span
                                class="text-[11px] font-bold {{ $isPosT ? 'text-fern-300' : 'text-rose-300' }}">{{ $isPosT ? '+' : '' }}{{ $stats['transaksi_growth'] }}%</span>
                            <span class="text-[11px] text-fern-400">vs 7 hari lalu</span>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-fern-300 mb-3">Rata-rata Order</p>
                        <p class="text-3xl sm:text-4xl font-semibold text-white">
                            Rp{{ number_format($stats['aov'], 0, ',', '.') }}</p>
                        <p class="text-[11px] text-fern-400 mt-3 font-medium">per transaksi</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-vanilla-custard-50 rounded-2xl border border-base-200 shadow-sm p-4 sm:p-5">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-sm sm:text-base font-semibold text-base-content">Tren Pendapatan & Transaksi</h2>
                    <p class="text-xs text-base-content/50 font-medium mt-0.5">7 hari terakhir</p>
                </div>
            </div>
            <div id="trendChart" class="w-full h-[280px]"></div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-5">
            <div
                class="lg:col-span-2 bg-base-100 rounded-2xl border border-base-200 shadow-sm overflow-hidden flex flex-col">
                <div class="px-4 sm:px-5 py-4 border-b border-base-200 flex items-center justify-between">
                    <div>
                        <h2 class="text-sm sm:text-base font-semibold text-base-content">Performa Kantin</h2>
                        <p class="text-xs text-base-content/50 font-medium mt-0.5">Berdasarkan pendapatan kumulatif</p>
                    </div>
                </div>
                <div class="overflow-x-auto flex-1">
                    <table class="table table-sm w-full min-w-[480px]">
                        <thead class="bg-base-200/60 text-xs">
                            <tr>
                                <th class="font-semibold text-base-content/60 py-3 px-4 w-8">No.</th>
                                <th class="font-semibold text-base-content/60 py-3 px-4">Nama Kantin</th>
                                <th class="font-semibold text-base-content/60 py-3 px-4 text-center">Selesai / Total</th>
                                <th class="font-semibold text-base-content/60 py-3 px-4 text-right">Pendapatan</th>
                            </tr>
                        </thead>
                        <tbody class="text-xs sm:text-sm divide-y divide-base-content/5">
                            @forelse($topCanteens ?? [] as $i => $canteen)
                                <tr class="hover:bg-base-200/30 transition-colors">
                                    <td class="px-4 py-3 text-base-content/40 font-bold">{{ $i + 1 }}</td>
                                    <td class="px-4 py-3 font-semibold text-base-content">
                                        <div class="flex items-center gap-2.5">
                                            @php
                                                $canteenImageUrl = 'https://ui-avatars.com/api/?name=' . urlencode($canteen->name) . '&background=random';
                                                if ($canteen->image) {
                                                    $canteenImageUrl = str_starts_with($canteen->image, 'assets/') ? asset($canteen->image) : asset('storage/' . $canteen->image);
                                                }
                                            @endphp
                                            <div class="avatar shrink-0">
                                                <div class="w-8 h-8 rounded-lg bg-base-200 overflow-hidden shadow-xs border border-base-content/5">
                                                    <img src="{{ $canteenImageUrl }}" 
                                                         onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($canteen->name) }}&background=random';"
                                                         alt="{{ $canteen->name }}" 
                                                         class="w-full h-full object-cover">
                                                </div>
                                            </div>
                                            <span class="truncate max-w-[120px] sm:max-w-[200px]">{{ $canteen->name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span
                                            class="font-semibold text-fern-700">{{ number_format($canteen->completed_orders_count) }}</span>
                                        <span class="text-base-content/40 mx-0.5">/</span>
                                        <span
                                            class="font-semibold text-base-content/60">{{ number_format($canteen->orders_count) }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <span
                                            class="font-semibold text-fern-700">Rp{{ number_format($canteen->total_revenue ?? 0, 0, ',', '.') }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-10 text-base-content/40 text-xs">Belum ada data
                                        performa kantin.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="flex flex-col gap-3">
                <div class="bg-base-100 rounded-2xl border border-base-200 shadow-sm p-4 sm:p-5">
                    <h2 class="text-sm font-semibold text-base-content mb-4">Ringkasan Platform</h2>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between py-2.5 border-b border-base-200/70">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-8 h-8 rounded-xl bg-fern-50 text-fern-700 flex items-center justify-center shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.75" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                                    </svg>
                                </div>
                                <span class="text-sm font-medium text-base-content/70">Total Pengguna</span>
                            </div>
                            <span
                                class="text-base font-semibold text-base-content">{{ number_format($stats['total_pengguna']) }}</span>
                        </div>
                        <div class="flex items-center justify-between py-2.5 border-b border-base-200/70">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-8 h-8 rounded-xl bg-fern-50 text-fern-700 flex items-center justify-center shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.75" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z" />
                                    </svg>
                                </div>
                                <span class="text-sm font-medium text-base-content/70">Total Kantin</span>
                            </div>
                            <span
                                class="text-base font-semibold text-base-content">{{ number_format($stats['total_kantin']) }}</span>
                        </div>
                        <div class="flex items-center justify-between py-2.5 border-b border-base-200/70">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-8 h-8 rounded-xl bg-fern-50 text-fern-700 flex items-center justify-center shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.75" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0ZM3.75 12h.007v.008H3.75V12Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm-.375 5.25h.007v.008H3.75v-.008Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                                    </svg>
                                </div>
                                <span class="text-sm font-medium text-base-content/70">Total Menu</span>
                            </div>
                            <span
                                class="text-base font-semibold text-base-content">{{ number_format($stats['total_menu']) }}</span>
                        </div>
                        <div class="flex items-center justify-between py-2.5">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-8 h-8 rounded-xl bg-amber-50 text-amber-500 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
                                        <path
                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                </div>
                                <div>
                                    <span class="text-sm font-medium text-base-content/70">Rating Rata-rata</span>
                                    <p class="text-[11px] text-base-content/40 font-medium">
                                        {{ number_format($stats['total_ulasan']) }} ulasan</p>
                                </div>
                            </div>
                            <span class="text-base font-semibold text-base-content">
                                {{ $stats['avg_rating'] > 0 ? number_format($stats['avg_rating'], 1) : '5.0' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 
          Menghitung rasio penyelesaian pesanan vendor peringkat #1 secara dinamis untuk mengukur
          tingkat efisiensi operasional kantin terbaik secara real-time.
        --}}
        @php
            $topVendor = $topCanteens->first();
            $completionRate =
                $topVendor && $topVendor->orders_count > 0
                    ? round(($topVendor->completed_orders_count / $topVendor->orders_count) * 100)
                    : 0;
        @endphp
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-4 sm:gap-5">

            <div class="lg:col-span-2 bg-base-100 rounded-2xl border border-base-200 shadow-sm p-4 sm:p-5 flex flex-col">
                <div class="flex items-center gap-2.5 mb-4">
                    <div>
                        <h2 class="text-sm font-semibold text-base-content">Vendor Terbaik</h2>
                        <p class="text-xs text-base-content/50 font-medium">Berdasarkan total pendapatan</p>
                    </div>
                </div>

                @if ($topVendor)
                    <div class="bg-fern-50 rounded-xl px-4 py-3 mb-4">
                        <p class="text-sm font-bold text-fern-600 mb-0.5">Peringkat #1</p>
                        <p class="text-lg font-bold text-fern-800 leading-tight">{{ $topVendor->name }}</p>
                    </div>

                    <div class="space-y-3 flex-1">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-medium text-base-content/60">Total Pendapatan</span>
                            <span
                                class="text-sm font-bold text-fern-700">Rp{{ number_format($topVendor->total_revenue ?? 0, 0, ',', '.') }}</span>
                        </div>
                        <div class="h-px bg-base-200"></div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-medium text-base-content/60">Persentase Pesanan</span>
                            <div class="flex items-center gap-2">
                                <div class="w-20 h-1.5 bg-base-200 rounded-full overflow-hidden">
                                    <div class="h-full bg-fern-500 rounded-full" style="width: {{ $completionRate }}%">
                                    </div>
                                </div>
                                <span class="text-sm font-bold text-base-content">{{ $completionRate }}%</span>
                            </div>
                        </div>
                        <div class="h-px bg-base-200"></div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-medium text-base-content/60">Total Pesanan</span>
                            <span
                                class="text-sm font-bold text-base-content">{{ number_format($topVendor->orders_count) }}</span>
                        </div>
                        <div class="h-px bg-base-200"></div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-medium text-base-content/60">Pesanan Selesai</span>
                            <span
                                class="text-sm font-bold text-emerald-600">{{ number_format($topVendor->completed_orders_count) }}</span>
                        </div>
                    </div>
                @else
                    <div class="flex-1 flex items-center justify-center">
                        <p class="text-sm text-base-content/40 font-medium">Belum ada data vendor.</p>
                    </div>
                @endif
            </div>

            <div class="lg:col-span-3 bg-vanilla-custard-50 rounded-2xl border border-base-200 shadow-sm p-4 sm:p-5 flex flex-col">
                <div class="mb-3">
                    <h2 class="text-sm sm:text-base font-semibold text-base-content">5 Menu Paling Laris</h2>
                    <p class="text-xs text-base-content/50 font-medium mt-0.5">Berdasarkan total item terjual</p>
                </div>
                <div id="topMenusChart" class="w-full flex-1 min-h-[200px]"></div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-5 gap-4 sm:gap-5 pb-6 lg:pb-2">
            <div class="lg:col-span-3 bg-vanilla-custard-50 rounded-2xl border border-base-200 shadow-sm p-4 sm:p-5">
                <div class="mb-4 flex items-start justify-between gap-3">
                    <div>
                        <h2 class="text-sm sm:text-base font-semibold text-base-content">Tingkat Penyelesaian Pesanan</h2>
                        <p class="text-xs text-base-content/50 font-medium mt-0.5">Perbandingan selesai vs. tidak selesai per kantin</p>
                    </div>
                    <div class="flex items-center gap-3 shrink-0 mt-0.5">
                        <span class="flex items-center gap-1.5 text-[11px] font-semibold text-fern-700">
                            <span class="w-2.5 h-2.5 rounded-sm bg-fern-500 inline-block"></span>Selesai
                        </span>
                        <span class="flex items-center gap-1.5 text-[11px] font-semibold text-base-content/50">
                            <span class="w-2.5 h-2.5 rounded-sm bg-rose-300 inline-block"></span>Belum/Batal
                        </span>
                    </div>
                </div>
                <div id="completionRateChart" class="w-full h-[260px]"></div>
            </div>

            <div
                class="lg:col-span-2 bg-base-100 rounded-2xl border border-base-200 shadow-sm overflow-hidden flex flex-col">
                <div class="px-4 sm:px-5 py-4 border-b border-base-200 bg-vanilla-custard-50">
                    <h2 class="text-sm sm:text-base font-semibold text-base-content">Aktivitas Terbaru</h2>
                    <p class="text-xs text-base-content/50 font-medium mt-0.5">Kejadian terkini di platform</p>
                </div>
                <div class="flex-1 overflow-y-auto max-h-[320px]">
                    @forelse($recentOrders ?? [] as $order)
                        @php
                            $canteenName = optional($order->canteen)->name ?? 'kantin';
                            $userName    = optional($order->user)->name ?? 'Pengguna';
                            $shortCode   = $order->pickup_code ?? substr($order->order_code, -6);

                            [$dotColor, $eventText] = match ($order->status) {
                                'selesai'      => ['bg-fern-500',   "Pesanan selesai diambil di {$canteenName}"],
                                'dimasak'      => ['bg-amber-400',  "Pesanan sedang diproses oleh {$canteenName}"],
                                'siap_diambil' => ['bg-sky-400',    "Pesanan siap diambil di {$canteenName}"],
                                'dibatalkan'   => ['bg-rose-400',   "Pesanan dibatalkan di {$canteenName}"],
                                default        => ['bg-base-content/25', "Pesanan baru masuk ke {$canteenName}"],
                            };
                        @endphp
                        <div class="px-4 py-3.5 flex items-start gap-3 hover:bg-base-200/30 transition-colors border-b border-base-content/5 last:border-0">
                            <span class="mt-1.5 shrink-0 w-2 h-2 rounded-full {{ $dotColor }}"></span>

                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-medium text-base-content leading-snug">
                                    {{ $eventText }}
                                    <span class="inline-flex items-center ml-1 px-1.5 py-0.5 rounded bg-base-200 text-[10px] font-bold text-base-content/50 tracking-wide align-middle">#{{ $shortCode }}</span>
                                </p>
                                <p class="text-[11px] text-base-content/40 font-medium mt-1">
                                    oleh {{ $userName }}
                                    <span class="mx-1 text-base-content/20">&bull;</span>
                                    {{ $order->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <div class="flex flex-col items-center justify-center py-12 gap-2 text-base-content/30">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                            <p class="text-sm font-medium">Belum ada aktivitas terbaru.</p>
                        </div>
                    @endforelse
                </div>
                @if(($recentOrders ?? collect())->count() > 0)
                    <div class="px-4 py-2.5 border-t border-base-200 bg-base-50">
                        <p class="text-[11px] text-base-content/40 font-medium text-center">
                            Menampilkan {{ ($recentOrders ?? collect())->count() }} aktivitas terkini
                        </p>
                    </div>
                @endif
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script>
        window.addEventListener("load", function() {

            // Membuat grafik tren gabungan: Pendapatan (skala kiri, area/rupiah) & Volume Transaksi (skala kanan, garis/jumlah).
            // Pemisahan sumbu Y ganda ini karena keduanya menggunakan satuan ukuran yang berbeda (Rupiah vs Frekuensi).
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
                    height: 280,
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
                        opacityFrom: 0.35,
                        opacityTo: 0.03,
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
                        formatter: (v) => "Rp" + v.toLocaleString("id-ID")
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
            new ApexCharts(document.querySelector("#trendChart"), trendOptions).render();

            // Top Menus Chart
            const topMenusOptions = {
                series: [{
                    name: 'Terjual',
                    data: @json($topMenuSeries)
                }],
                chart: {
                    type: 'bar',
                    height: 260,
                    toolbar: {
                        show: false
                    },
                    fontFamily: 'Poppins, sans-serif'
                },
                plotOptions: {
                    bar: {
                        borderRadius: 4,
                        horizontal: true
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
                        color: 'rgba(0,0,0,0.1)'
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
                    borderColor: 'rgba(0,0,0,0.05)',
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
            new ApexCharts(document.querySelector("#topMenusChart"), topMenusOptions).render();

            // Menggunakan tipe baris bertumpuk (stacked 100%) agar admin dapat melihat rasio efisiensi 
            // penyelesaian order antar kantin secara langsung dengan membandingkan persentase selesai vs gagal.
            @php
                $completedSeries = $topCanteens->map(fn($c) => (int) $c->completed_orders_count)->toArray();
                $incompleteSeries = $topCanteens->map(fn($c) => max(0, (int) $c->orders_count - (int) $c->completed_orders_count))->toArray();
                $completionLabels = $topCanteens->pluck('name')->toArray();
            @endphp
            const completionRateOptions = {
                series: [
                    { name: 'Selesai', data: @json($completedSeries) },
                    { name: 'Belum/Batal', data: @json($incompleteSeries) }
                ],
                chart: {
                    type: 'bar',
                    height: 260,
                    stacked: true,
                    stackType: '100%',
                    toolbar: { show: false },
                    fontFamily: 'Poppins, sans-serif'
                },
                plotOptions: {
                    bar: {
                        borderRadius: 4,
                        horizontal: true,
                        barHeight: '55%'
                    }
                },
                colors: ['#306939', '#fca5a5'],
                dataLabels: {
                    enabled: true,
                    formatter: (val) => Math.round(val) + '%',
                    style: {
                        fontSize: '11px',
                        fontFamily: 'Poppins, sans-serif',
                        fontWeight: '600'
                    },
                    dropShadow: { enabled: false }
                },
                stroke: { width: 0 },
                xaxis: {
                    categories: @json($completionLabels),
                    labels: {
                        formatter: (val) => Math.round(val) + '%',
                        style: { colors: '#94a3b8', fontFamily: 'Poppins, sans-serif', fontSize: '11px' }
                    },
                    axisBorder: { show: false },
                    axisTicks: { show: false }
                },
                yaxis: {
                    labels: {
                        style: { colors: '#64748b', fontFamily: 'Poppins, sans-serif', fontSize: '11px' },
                        maxWidth: 140
                    }
                },
                tooltip: {
                    y: {
                        formatter: (val, { seriesIndex, dataPointIndex, w }) => {
                            const total = w.globals.stackedSeriesTotals[dataPointIndex];
                            const pct = total > 0 ? Math.round((val / total) * 100) : 0;
                            return val + ' pesanan (' + pct + '%)';
                        }
                    }
                },
                legend: { show: false },
                grid: {
                    borderColor: 'rgba(0,0,0,0.05)',
                    xaxis: { lines: { show: true } },
                    yaxis: { lines: { show: false } }
                }
            };
            new ApexCharts(document.querySelector("#completionRateChart"), completionRateOptions).render();

        });
    </script>
@endpush

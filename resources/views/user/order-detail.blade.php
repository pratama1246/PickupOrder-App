@extends('layouts.app')

@section('title', 'Detail Pesanan - PNC')

@section('content')
<main class="min-h-screen bg-base-100 pb-12" x-data="{
    init() {
        // Simple polling to refresh the page every 30 seconds if order is active
        if ({{ in_array($order->status, ['menunggu', 'dimasak']) ? 'true' : 'false' }}) {
            setInterval(() => {
                window.location.reload();
            }, 30000);
        }
    }
}">
    
    <x-breadcrumb 
        class="pt-8 pb-4"
        maxWidth="max-w-7xl"
        :links="[
            ['label' => 'Beranda', 'url' => '/'],
            ['label' => 'Riwayat', 'url' => '/riwayat'],
            ['label' => 'Order No. ' . $order->order_code]
        ]" 
    />

    @php
        $isMenunggu = $order->status === 'menunggu';
        $isDimasak = $order->status === 'dimasak';
        $isSiap = $order->status === 'siap_diambil';
        $isSelesai = $order->status === 'selesai';
        $isBatal = $order->status === 'dibatalkan';

        $step = 1;
        if ($isBatal) {
            $step = 0;
        } elseif ($isSelesai || $isSiap) {
            $step = 4;
        } elseif ($isDimasak) {
            $step = $order->queue_position > 1 ? 2 : 3;
        }
    @endphp

    <section class="px-4 sm:px-10 md:px-16 lg:px-24">
        <div class="max-w-7xl mx-auto">
            
            @if(!$isBatal)
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8 mt-4">
                <div>
                    <h1 class="text-2xl sm:text-4xl font-bold text-base-content mb-2">
                        {{ $step <= 3 ? 'Pesananmu Dalam Antrian' : ($isSelesai ? 'Pesanan Selesai' : 'Pesananmu Siap Diambil') }}
                    </h1>
                    <p class="text-base-content/70 text-sm sm:text-lg font-medium">
                        {{ $step == 1 ? 'Pesananmu sedang menunggu konfirmasi dari kantin.' : 
                           ($step == 2 ? 'Pesananmu sudah diterima dan menunggu giliran untuk dimasak.' : 
                           ($step == 3 ? 'Pesananmu sedang dimasak oleh kantin.' : 
                           ($isSelesai ? 'Pesanan ini telah selesai dan sudah diambil.' : 'Pesananmu sudah siap untuk diambil di kantin.'))) }}
                    </p>
                </div>
                @if($step <= 3)
                <div class="md:text-right bg-vanilla-custard-50 px-4 py-2 rounded-xl border border-vanilla-custard-200">
                    <p class="text-sm font-medium text-base-content/70">Estimasi Waktu</p>
                    <p class="text-lg font-bold text-fern-700">{{ $order->estimated_time }} Menit</p>
                </div>
                @endif
            </div>

            <div class="w-full overflow-x-auto pb-8 mb-8">
                <div class="relative flex justify-between items-start w-full min-w-[600px] max-w-4xl mx-auto pt-6 px-4">
                    
                    <!-- Connecting Lines (behind circles) -->
                    <div class="absolute top-[64px] left-16 right-16 h-1 bg-gray-300 z-0"></div>
                    <div class="absolute top-[64px] left-16 h-1 bg-fern-700 z-0 transition-all duration-500" style="width: calc({{ $step > 1 ? ($step - 1) * 33.33 : 0 }}% - 32px)"></div>

                    <!-- Step 1 -->
                    <div class="relative z-10 flex flex-col items-center w-32">
                        <div class="w-20 h-20 rounded-full flex items-center justify-center {{ $step > 1 ? 'bg-fern-700 text-white' : ($step == 1 ? 'bg-vanilla-custard-300 text-base-content' : 'bg-gray-300 text-base-content/70') }} shadow-sm transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m3.75 9v6m3-3H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                            </svg>
                        </div>
                        <p class="mt-4 text-center font-bold text-sm sm:text-base {{ $step >= 1 ? 'text-base-content' : 'text-base-content/50' }}">Menunggu<br>Konfirmasi</p>
                    </div>

                    <!-- Step 2 -->
                    <div class="relative z-10 flex flex-col items-center w-32">
                        <div class="w-20 h-20 rounded-full flex items-center justify-center {{ $step > 2 ? 'bg-fern-700 text-white' : ($step == 2 ? 'bg-vanilla-custard-300 text-base-content' : 'bg-gray-300 text-base-content/70') }} shadow-sm transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                            </svg>
                        </div>
                        <p class="mt-4 text-center font-bold text-sm sm:text-base {{ $step >= 2 ? 'text-base-content' : 'text-base-content/50' }}">Dalam<br>Antrian</p>
                    </div>

                    <!-- Step 3 -->
                    <div class="relative z-10 flex flex-col items-center w-32">
                        <div class="w-20 h-20 rounded-full flex items-center justify-center {{ $step > 3 ? 'bg-fern-700 text-white' : ($step == 3 ? 'bg-vanilla-custard-300 text-base-content' : 'bg-gray-300 text-base-content/70') }} shadow-sm transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.362 5.214A8.252 8.252 0 0 1 12 21 8.25 8.25 0 0 1 6.038 7.047 8.287 8.287 0 0 0 9 9.601a8.983 8.983 0 0 1 3.361-6.867 8.21 8.21 0 0 0 3 2.48Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 18a3.75 3.75 0 0 0 .495-7.468 5.99 5.99 0 0 0-1.925 3.547 5.975 5.975 0 0 1-2.133-1.001A3.75 3.75 0 0 0 12 18Z" />
                            </svg>
                        </div>
                        <p class="mt-4 text-center font-bold text-sm sm:text-base {{ $step >= 3 ? 'text-base-content' : 'text-base-content/50' }}">Sedang Di<br>masak</p>
                    </div>

                    <!-- Step 4 -->
                    <div class="relative z-10 flex flex-col items-center w-32">
                        <div class="w-20 h-20 rounded-full flex items-center justify-center {{ $step > 4 ? 'bg-fern-700 text-white' : ($step == 4 ? 'bg-vanilla-custard-300 text-base-content' : 'bg-gray-300 text-base-content/70') }} shadow-sm transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z" />
                            </svg>
                        </div>
                        <p class="mt-4 text-center font-bold text-sm sm:text-base {{ $step >= 4 ? 'text-base-content' : 'text-base-content/50' }}">Siap Di<br>Ambil</p>
                    </div>
                    
                </div>
            </div>
            @endif

            <h2 class="text-xl font-bold text-base-content mb-4">Detail Order</h2>

            <div class="flex flex-col lg:flex-row gap-6 lg:gap-8 items-start">
                <!-- Kiri: Detail Items -->
                <div class="w-full lg:flex-1 min-w-0">
                    <div class="bg-vanilla-custard-50 border border-base-content/20 rounded-3xl p-4 sm:p-6 shadow-sm">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                            <div>
                                <h3 class="text-base sm:text-lg font-bold text-base-content mb-1">No. Order : {{ $order->order_code }}</h3>
                                <p class="font-bold text-xl sm:text-2xl text-base-content">{{ $order->canteen->name }}</p>
                                <div class="text-xs sm:text-sm text-base-content/70 mt-2 space-y-1 font-medium">
                                    <p class="flex items-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 text-fern-700"><path fill-rule="evenodd" d="M9.69 18.933l.003.001C9.89 19.02 10 19 10 19s.11 0 .308-.066l.002-.001.006-.003.018-.008a5.741 5.741 0 00.281-.14c.186-.096.446-.24.757-.433.62-.384 1.445-.966 2.274-1.765C15.302 14.988 17 12.493 17 9A7 7 0 103 9c0 3.492 1.698 5.988 3.355 7.584a13.731 13.731 0 002.273 1.765 11.842 11.842 0 00.976.544l.062.029.018.008.006.003zM10 11.25a2.25 2.25 0 100-4.5 2.25 2.25 0 000 4.5z" clip-rule="evenodd" /></svg>
                                        Lokasi Kantin : {{ $order->canteen->name }}
                                    </p>
                                    @if(in_array($order->status, ['menunggu', 'dimasak']))
                                    <p class="flex items-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 text-fern-700"><path d="M10 9a3 3 0 100-6 3 3 0 000 6zM6 8a2 2 0 11-4 0 2 2 0 014 0zM1.49 15.326a.78.78 0 01-.358-.442 3 3 0 014.308-3.516 6.484 6.484 0 00-1.905 3.959c-.023.222-.014.442.025.654a4.97 4.97 0 01-2.07-.655zM16.44 15.98a4.97 4.97 0 002.07-.654.78.78 0 00.357-.442 3 3 0 00-4.308-3.517 6.484 6.484 0 011.907 3.96 2.32 2.32 0 01-.026.654zM7.25 12.02c.033-.12.068-.239.106-.356A4.98 4.98 0 0112 8a4.98 4.98 0 014.644 3.664c.038.117.073.236.106.356a.75.75 0 00.14.316 4.996 4.996 0 01-1.352 5.093c-.456.402-1.01.714-1.613.917A8.995 8.995 0 0112 19a8.995 8.995 0 01-2.025-.66A4.998 4.998 0 016.5 13.25c0-1.12.368-2.152.99-3.003a.75.75 0 00.14-.316c-.033-.117-.068-.236-.106-.356z" /></svg>
                                        Antrian ke-{{ $order->queue_position }} dari {{ \App\Models\Order::where('canteen_id', $order->canteen_id)->whereIn('status', ['menunggu', 'dimasak'])->count() }} Pesanan
                                    </p>
                                    @endif
                                </div>
                            </div>
                            <x-status-badge :status="$order->status_label" />
                        </div>

                        <div class="space-y-4 mb-6">
                            @foreach ($order->items as $item)
                                <div class="bg-white rounded-2xl p-4 border border-base-content/10 shadow-sm flex items-center gap-4">
                                    <img src="{{ $item->menu && $item->menu->image ? asset('storage/' . $item->menu->image) : asset('assets/food/es teh.jpg') }}" 
                                         alt="{{ $item->menu->name ?? 'Menu Dihapus' }}" 
                                         class="w-16 h-16 sm:w-20 sm:h-20 rounded-xl object-cover"
                                         onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($item->menu->name ?? 'Menu') }}&color=7F9CF5&background=EBF4FF'">
                                    <div class="flex-1">
                                        <h4 class="font-bold text-base-content sm:text-lg">{{ $item->menu->name ?? 'Menu Dihapus' }}</h4>
                                        <p class="text-xs sm:text-sm text-base-content/50 font-medium">{{ $item->qty }}x Porsi</p>
                                    </div>
                                    <div class="font-bold text-base-content text-right sm:text-lg">
                                        {{ $item->menu ? $item->menu->formatted_price : 'Rp ' . number_format($item->price, 0, ',', '.') }}
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div>
                            <div class="bg-white border border-base-content/10 rounded-xl p-4 text-sm font-medium text-base-content/70">
                                {{ $order->notes ?? 'Tidak ada catatan untuk kantin (Opsional)' }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kanan: Ringkasan & Aksi -->
                <div class="w-full lg:w-80 xl:w-96 shrink-0">
                    <div class="bg-vanilla-custard-50 border border-base-content/20 rounded-3xl p-6 shadow-sm sticky top-24">
                        <h3 class="text-lg font-bold text-base-content mb-2">Total Belanja</h3>
                        <p class="text-3xl font-extrabold text-base-content mb-6">{{ $order->formatted_total }}</p>
                        
                        <div class="space-y-3 text-xs sm:text-sm font-medium text-base-content/80 border-t border-base-content/10 pt-6 mb-8">
                            <div class="flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 text-fern-700"><path fill-rule="evenodd" d="M2.5 4A1.5 1.5 0 001 5.5V6h18v-.5A1.5 1.5 0 0017.5 4h-15zM19 8.5H1v6A1.5 1.5 0 002.5 16h15a1.5 1.5 0 001.5-1.5v-6zM3 13.25a.75.75 0 01.75-.75h1.5a.75.75 0 010 1.5h-1.5a.75.75 0 01-.75-.75zm4.75-.75a.75.75 0 000 1.5h3.5a.75.75 0 000-1.5h-3.5z" clip-rule="evenodd" /></svg>
                                <span>Metode Pembayaran : Tunai / QRIS</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 text-fern-700"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.75-13a.75.75 0 00-1.5 0v5c0 .414.336.75.75.75h4a.75.75 0 000-1.5h-3.25V5z" clip-rule="evenodd" /></svg>
                                <span>Waktu Pickup : {{ $order->pickup_time->format('H.i') }} - {{ $order->pickup_time->copy()->addMinutes(30)->format('H.i') }}</span>
                            </div>
                        </div>

                        <button 
                            class="w-full py-4 rounded-xl font-bold text-lg transition-all 
                            {{ $step == 4 ? 'bg-fern-700 hover:bg-fern-800 text-white shadow-md active:scale-95' : 'bg-gray-300 text-gray-500 cursor-not-allowed' }}"
                            {{ $step != 4 ? 'disabled' : '' }}
                        >
                            {{ $step == 4 ? 'Ambil Sekarang' : 'Belum Siap' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
@endsection

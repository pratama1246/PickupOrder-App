@extends('layouts.app')

@section('title', 'Detail Pesanan - PNC')

@section('content')
    <main class="min-h-screen bg-base-100 pb-12" x-data="{
        init() {
            // Melakukan polling berkala setiap 30 detik untuk mendeteksi perubahan status pesanan di database
            // secara otomatis, sehingga status antrian / proses masak ter-update tanpa memerlukan koneksi WebSocket.
            if ({{ in_array($order->status, ['menunggu', 'dimasak']) ? 'true' : 'false' }}) {
                setInterval(() => {
                    window.location.reload();
                }, 30000);
            }
        }
    }">

        <x-breadcrumb class="pt-8 pb-4" maxWidth="max-w-7xl" :links="[
            ['label' => 'Beranda', 'url' => '/'],
            ['label' => 'Riwayat', 'url' => route('order.index')],
            ['label' => 'Order No. ' . $order->order_code],
        ]" />

        @php
            $isBatal = $order->status === 'dibatalkan';

            $step = 1;
            if ($isBatal) {
                $step = 0;
            } else {
                if ($order->payment_method === 'midtrans' && $order->payment_status === 'pending') {
                    $step = 1;
                } elseif ($order->status === 'menunggu') {
                    $step = 2;
                } elseif ($order->status === 'dimasak') {
                    $step = $order->queue_position > 1 ? 3 : 4;
                } elseif ($order->status === 'siap_diambil') {
                    $step = 5;
                } elseif ($order->status === 'selesai') {
                    $step = 6;
                }
            }
        @endphp

        <section class="px-3 sm:px-10 md:px-16 lg:px-24">
            <div class="max-w-7xl mx-auto">

                @if (!$isBatal)
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8 mt-4">
                        <div>
                            <h1 class="text-2xl sm:text-4xl font-bold text-base-content mb-2">
                                {{ $step == 1
                                    ? 'Selesaikan Pembayaran'
                                    : ($step <= 4
                                        ? 'Pesananmu Dalam Proses'
                                        : ($step == 5
                                            ? 'Pesananmu Siap Diambil'
                                            : 'Pesanan Selesai')) }}
                            </h1>
                            <p class="text-base-content/70 text-sm sm:text-lg font-medium">
                                {{ $step == 1
                                    ? 'Selesaikan pembayaran agar pesananmu dapat diproses oleh kantin.'
                                    : ($step == 2
                                        ? 'Pesananmu sedang menunggu konfirmasi dari kantin.'
                                        : ($step == 3
                                            ? 'Pesananmu sudah diterima dan menunggu giliran untuk dimasak.'
                                            : ($step == 4
                                                ? 'Pesananmu sedang dimasak oleh kantin.'
                                                : ($step == 5
                                                    ? 'Pesananmu sudah siap untuk diambil di kantin.'
                                                    : 'Pesanan ini telah selesai dan sudah diambil.')))) }}
                            </p>
                        </div>
                        @if ($step > 1 && $step <= 4)
                            <div class="md:text-right px-4 py-2">
                                <p class="text-sm font-medium text-base-content/70">Estimasi Waktu</p>
                                <p class="text-lg font-bold text-fern-700">{{ $order->estimated_time }} Menit</p>
                            </div>
                        @endif
                    </div>

                    <div class="block md:hidden pb-8 mb-8">
                        <div class="relative flex flex-col gap-6 pl-6">
                            <div class="absolute top-6 bottom-6 left-[46px] w-1 bg-gray-300 z-0">
                                <div class="w-full bg-fern-700 transition-all duration-500"
                                    style="height: {{ $step > 1 ? ($step - 1) * 20 : 0 }}%"></div>
                            </div>

                            <div class="relative z-10 flex items-center gap-4">
                                <div
                                    class="w-12 h-12 rounded-full flex items-center justify-center shrink-0 {{ $step >= 1 ? ($step > 1 ? 'bg-fern-700 text-white' : 'bg-vanilla-custard-300 text-base-content') : 'bg-gray-300 text-base-content/70' }} shadow-sm transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                                    </svg>
                                </div>
                                <div>
                                    <p
                                        class="font-bold text-sm sm:text-base {{ $step >= 1 ? 'text-base-content' : 'text-base-content/50' }}">
                                        Belum Dibayar</p>
                                    @if ($step == 1)
                                        <span
                                            class="text-xs text-fern-700 font-semibold bg-fern-50 px-2.5 py-1 rounded-full mt-1 inline-block border border-fern-200">Selesaikan
                                            pembayaran</span>
                                    @endif
                                </div>
                            </div>

                            <div class="relative z-10 flex items-center gap-4">
                                <div
                                    class="w-12 h-12 rounded-full flex items-center justify-center shrink-0 {{ $step >= 2 ? ($step > 2 ? 'bg-fern-700 text-white' : 'bg-vanilla-custard-300 text-base-content') : 'bg-gray-300 text-base-content/70' }} shadow-sm transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m3.75 9v6m3-3H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                    </svg>
                                </div>
                                <div>
                                    <p
                                        class="font-bold text-sm sm:text-base {{ $step >= 2 ? 'text-base-content' : 'text-base-content/50' }}">
                                        Menunggu Konfirmasi</p>
                                    @if ($step == 2)
                                        <span
                                            class="text-xs text-fern-700 font-semibold bg-fern-50 px-2.5 py-1 rounded-full mt-1 inline-block border border-fern-200">Menunggu
                                            persetujuan kantin</span>
                                    @endif
                                </div>
                            </div>

                            <div class="relative z-10 flex items-center gap-4">
                                <div
                                    class="w-12 h-12 rounded-full flex items-center justify-center shrink-0 {{ $step >= 3 ? ($step > 3 ? 'bg-fern-700 text-white' : 'bg-vanilla-custard-300 text-base-content') : 'bg-gray-300 text-base-content/70' }} shadow-sm transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                                    </svg>
                                </div>
                                <div>
                                    <p
                                        class="font-bold text-sm sm:text-base {{ $step >= 3 ? 'text-base-content' : 'text-base-content/50' }}">
                                        Dalam Antrian</p>
                                    @if ($step == 3)
                                        <span
                                            class="text-xs text-fern-700 font-semibold bg-fern-50 px-2.5 py-1 rounded-full mt-1 inline-block border border-fern-200">Antrean
                                            ke-{{ $order->queue_position }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="relative z-10 flex items-center gap-4">
                                <div
                                    class="w-12 h-12 rounded-full flex items-center justify-center shrink-0 {{ $step >= 4 ? ($step > 4 ? 'bg-fern-700 text-white' : 'bg-vanilla-custard-300 text-base-content') : 'bg-gray-300 text-base-content/70' }} shadow-sm transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2 12h20" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 12v8a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-8" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m4 8 16-4" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.86 6.78-.45-1.81a2 2 0 0 1 1.45-2.43l1.94-.48a2 2 0 0 1 2.43 1.45l.45 1.81" />
                                    </svg>
                                </div>
                                <div>
                                    <p
                                        class="font-bold text-sm sm:text-base {{ $step >= 4 ? 'text-base-content' : 'text-base-content/50' }}">
                                        Sedang Dimasak</p>
                                    @if ($step == 4)
                                        <span
                                            class="text-xs text-fern-700 font-semibold bg-fern-50 px-2.5 py-1 rounded-full mt-1 inline-block border border-fern-200">Makanan
                                            sedang disiapkan</span>
                                    @endif
                                </div>
                            </div>

                            <div class="relative z-10 flex items-center gap-4">
                                <div
                                    class="w-12 h-12 rounded-full flex items-center justify-center shrink-0 {{ $step >= 5 ? ($step > 5 ? 'bg-fern-700 text-white' : 'bg-vanilla-custard-300 text-base-content') : 'bg-gray-300 text-base-content/70' }} shadow-sm transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z" />
                                    </svg>
                                </div>
                                <div>
                                    <p
                                        class="font-bold text-sm sm:text-base {{ $step >= 5 ? 'text-base-content' : 'text-base-content/50' }}">
                                        Siap Diambil</p>
                                    @if ($step == 5)
                                        <span
                                            class="text-xs text-fern-700 font-semibold bg-fern-50 px-2.5 py-1 rounded-full mt-1 inline-block border border-fern-200">Silakan
                                            ambil di kantin</span>
                                    @endif
                                </div>
                            </div>

                            <div class="relative z-10 flex items-center gap-4">
                                <div
                                    class="w-12 h-12 rounded-full flex items-center justify-center shrink-0 {{ $step >= 6 ? 'bg-fern-700 text-white' : 'bg-gray-300 text-base-content/70' }} shadow-sm transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                    </svg>
                                </div>
                                <div>
                                    <p
                                        class="font-bold text-sm sm:text-base {{ $step >= 6 ? 'text-base-content' : 'text-base-content/50' }}">
                                        Selesai</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="hidden md:block w-full overflow-x-auto pb-8 mb-8">
                        <div
                            class="relative flex justify-between items-start w-full min-w-[800px] max-w-5xl mx-auto pt-6 px-4">


                            <div class="absolute top-[64px] left-16 right-16 h-1 bg-gray-300 z-0">
                                <div class="h-full bg-fern-700 transition-all duration-500"
                                    style="width: {{ $step > 1 ? ($step - 1) * 20 : 0 }}%"></div>
                            </div>

                            <div class="relative z-10 flex flex-col items-center w-32">
                                <div
                                    class="w-20 h-20 rounded-full flex items-center justify-center {{ $step >= 1 ? ($step > 1 ? 'bg-fern-700 text-white' : 'bg-vanilla-custard-300 text-base-content') : 'bg-gray-300 text-base-content/70' }} shadow-sm transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-10 h-10">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                                    </svg>
                                </div>
                                <p
                                    class="mt-4 text-center font-bold text-sm sm:text-base {{ $step >= 1 ? 'text-base-content' : 'text-base-content/50' }}">
                                    Belum<br>Dibayar</p>
                            </div>

                            <div class="relative z-10 flex flex-col items-center w-32">
                                <div
                                    class="w-20 h-20 rounded-full flex items-center justify-center {{ $step >= 2 ? ($step > 2 ? 'bg-fern-700 text-white' : 'bg-vanilla-custard-300 text-base-content') : 'bg-gray-300 text-base-content/70' }} shadow-sm transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-10 h-10">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m3.75 9v6m3-3H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                    </svg>
                                </div>
                                <p
                                    class="mt-4 text-center font-bold text-sm sm:text-base {{ $step >= 2 ? 'text-base-content' : 'text-base-content/50' }}">
                                    Menunggu<br>Konfirmasi</p>
                            </div>

                            <div class="relative z-10 flex flex-col items-center w-32">
                                <div
                                    class="w-20 h-20 rounded-full flex items-center justify-center {{ $step >= 3 ? ($step > 3 ? 'bg-fern-700 text-white' : 'bg-vanilla-custard-300 text-base-content') : 'bg-gray-300 text-base-content/70' }} shadow-sm transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-10 h-10">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                                    </svg>
                                </div>
                                <p
                                    class="mt-4 text-center font-bold text-sm sm:text-base {{ $step >= 3 ? 'text-base-content' : 'text-base-content/50' }}">
                                    Dalam<br>Antrian</p>
                            </div>

                            <div class="relative z-10 flex flex-col items-center w-32">
                                <div
                                    class="w-20 h-20 rounded-full flex items-center justify-center {{ $step >= 4 ? ($step > 4 ? 'bg-fern-700 text-white' : 'bg-vanilla-custard-300 text-base-content') : 'bg-gray-300 text-base-content/70' }} shadow-sm transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-10 h-10">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2 12h20" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 12v8a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-8" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m4 8 16-4" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.86 6.78-.45-1.81a2 2 0 0 1 1.45-2.43l1.94-.48a2 2 0 0 1 2.43 1.45l.45 1.81" />
                                    </svg>
                                </div>
                                <p
                                    class="mt-4 text-center font-bold text-sm sm:text-base {{ $step >= 4 ? 'text-base-content' : 'text-base-content/50' }}">
                                    Sedang Di<br>masak</p>
                            </div>

                            <div class="relative z-10 flex flex-col items-center w-32">
                                <div
                                    class="w-20 h-20 rounded-full flex items-center justify-center {{ $step >= 5 ? ($step > 5 ? 'bg-fern-700 text-white' : 'bg-vanilla-custard-300 text-base-content') : 'bg-gray-300 text-base-content/70' }} shadow-sm transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-10 h-10">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z" />
                                    </svg>
                                </div>
                                <p
                                    class="mt-4 text-center font-bold text-sm sm:text-base {{ $step >= 5 ? 'text-base-content' : 'text-base-content/50' }}">
                                    Siap Di<br>Ambil</p>
                            </div>

                            <div class="relative z-10 flex flex-col items-center w-32">
                                <div
                                    class="w-20 h-20 rounded-full flex items-center justify-center {{ $step >= 6 ? 'bg-fern-700 text-white' : 'bg-gray-300 text-base-content/70' }} shadow-sm transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-10 h-10">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                    </svg>
                                </div>
                                <p
                                    class="mt-4 text-center font-bold text-sm sm:text-base {{ $step >= 6 ? 'text-base-content' : 'text-base-content/50' }}">
                                    Selesai</p>
                            </div>

                        </div>
                    </div>
                @endif

                <h2 class="text-xl font-bold text-base-content mb-4">Detail Order</h2>

                <div class="flex flex-col lg:flex-row gap-6 lg:gap-8 items-start">
                    <div class="w-full lg:flex-1 min-w-0 space-y-6">

                        {{-- Informasi: Pesanan pending diarahkan ke halaman Riwayat untuk membayar --}}
                        @if ($order->payment_method === 'midtrans' && $order->payment_status === 'pending')
                            <div
                                class="bg-amber-50 border border-amber-300 rounded-2xl p-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-sm">
                                <div>
                                    <p class="font-bold text-amber-800 text-sm mb-1">Pembayaran belum selesai</p>
                                    <p class="text-amber-700 text-xs font-medium">Selesaikan pembayaran dari halaman
                                        Riwayat Pesanan untuk melanjutkan semua pesanan dalam transaksi ini.</p>
                                </div>
                                <a href="{{ route('order.index') }}"
                                    class="btn bg-fern-700 hover:bg-fern-800 text-white border-none rounded-xl font-bold shadow-md active:scale-95 transition-all shrink-0 h-12 min-h-0 px-4 sm:px-6">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-1.5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                    </svg>
                                    Ke Riwayat
                                </a>
                            </div>
                        @endif

                        @if ($order->payment_method === 'qris_manual' && $order->payment_status === 'pending')
                            <div
                                class="bg-amber-50 border border-amber-300 rounded-2xl p-5 flex items-start gap-4 shadow-sm mb-6">
                                <div class="p-2.5 rounded-xl bg-amber-100 text-amber-700 shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="2.5" stroke="currentColor" class="w-6 h-6">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-bold text-amber-900 text-sm mb-0.5">Menunggu Verifikasi Pembayaran</p>
                                    <p class="text-amber-800 text-xs font-semibold leading-relaxed">Bukti transfer Anda telah terkirim. Kantin akan segera memverifikasi pembayaran Anda sebelum mulai memasak pesanan.</p>
                                </div>
                            </div>
                        @endif

                        {{-- Informasi: Pesanan Siap Diambil dengan Pembayaran Tunai (Cash) Belum Lunas --}}
                        @if ($order->status === 'siap_diambil' && $order->payment_method === 'cash' && $order->payment_status === 'pending')
                            <div
                                class="bg-indigo-50 border border-indigo-200 rounded-2xl p-5 flex items-start gap-4 shadow-sm">
                                <div class="p-2.5 rounded-xl bg-indigo-100 text-indigo-700 shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="2.5" stroke="currentColor" class="w-6 h-6">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-bold text-indigo-900 text-sm mb-0.5">Pesanan Siap Diambil & Bayar di
                                        Kantin</p>
                                    <p class="text-indigo-800 text-xs font-medium leading-relaxed">Pesananmu sudah siap!
                                        Silakan siapkan uang tunai sebesar <span
                                            class="font-extrabold">{{ $order->formatted_total }}</span> untuk dibayarkan
                                        langsung di kasir <span class="font-bold">{{ $order->canteen->name }}</span> saat
                                        mengambil makanan.</p>
                                </div>
                            </div>
                        @endif

                        <div class="bg-white border border-base-content/20 rounded-3xl p-5 sm:p-6 shadow-sm">
                            <div
                                class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-base-content/10 pb-4 mb-4">
                                <div>
                                    <h3 class="text-sm sm:text-lg font-bold text-base-content mb-1 whitespace-nowrap truncate">No. Order :
                                        {{ $order->order_code }}</h3>
                                    <p class="font-bold text-xl sm:text-2xl text-base-content">{{ $order->canteen->name }}
                                    </p>
                                    <div class="text-xs sm:text-sm text-base-content/70 mt-2 space-y-1 font-medium">
                                        <p class="flex items-center gap-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                                fill="currentColor" class="w-4 h-4 text-fern-700">
                                                <path fill-rule="evenodd"
                                                    d="M9.69 18.933l.003.001C9.89 19.02 10 19 10 19s.11 0 .308-.066l.002-.001.006-.003.018-.008a5.741 5.741 0 00.281-.14c.186-.096.446-.24.757-.433.62-.384 1.445-.966 2.274-1.765C15.302 14.988 17 12.493 17 9A7 7 0 103 9c0 3.492 1.698 5.988 3.355 7.584a13.731 13.731 0 002.273 1.765 11.842 11.842 0 00.976.544l.062.029.018.008.006.003zM10 11.25a2.25 2.25 0 100-4.5 2.25 2.25 0 000 4.5z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            Lokasi Kantin : {{ $order->canteen->name }}
                                        </p>
                                        @if ($step > 1 && $step <= 4)
                                            <p class="flex items-center gap-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                                    fill="currentColor" class="w-4 h-4 text-fern-700">
                                                    <path
                                                        d="M10 9a3 3 0 100-6 3 3 0 000 6zM6 8a2 2 0 11-4 0 2 2 0 014 0zM1.49 15.326a.78.78 0 01-.358-.442 3 3 0 014.308-3.516 6.484 6.484 0 00-1.905 3.959c-.023.222-.014.442.025.654a4.97 4.97 0 01-2.07-.655zM16.44 15.98a4.97 4.97 0 002.07-.654.78.78 0 00.357-.442 3 3 0 00-4.308-3.517 6.484 6.484 0 011.907 3.96 2.32 2.32 0 01-.026.654zM7.25 12.02c.033-.12.068-.239.106-.356A4.98 4.98 0 0112 8a4.98 4.98 0 014.644 3.664c.038.117.073.236.106.356a.75.75 0 00.14.316 4.996 4.996 0 01-1.352 5.093c-.456.402-1.01.714-1.613.917A8.995 8.995 0 0112 19a8.995 8.995 0 01-2.025-.66A4.998 4.998 0 016.5 13.25c0-1.12.368-2.152.99-3.003a.75.75 0 00.14-.316c-.033-.117-.068-.236-.106-.356z" />
                                                </svg>
                                                Antrian ke-{{ $order->queue_position }} dari
                                                {{ \App\Models\Order::where('canteen_id', $order->canteen_id)->whereIn('status', ['menunggu', 'dimasak'])->count() }}
                                                Pesanan
                                            </p>
                                        @endif
                                    </div>
                                </div>
                                <x-status-badge :status="$order->status_label" />
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="bg-base-100 rounded-2xl p-4 border border-base-content/10 shadow-sm">
                                    <p class="text-xs text-base-content/50 uppercase font-bold mb-1">Metode Pembayaran</p>
                                    <p class="font-bold text-base-content text-sm">{{ $order->payment_method_label }}</p>
                                </div>
                                <div class="bg-base-100 rounded-2xl p-4 border border-base-content/10 shadow-sm">
                                    <p class="text-xs text-base-content/50 uppercase font-bold mb-1">Status Pembayaran</p>
                                    @php
                                        $payBadgeClass = match ($order->payment_status) {
                                            'paid' => 'bg-emerald-100 text-emerald-800',
                                            'failed' => 'bg-red-100 text-red-800',
                                            'expired' => 'bg-red-100 text-red-800',
                                            default => 'bg-amber-100 text-amber-800',
                                        };
                                    @endphp
                                    <span
                                        class="inline-block text-xs font-bold px-3 py-1 rounded-md {{ $payBadgeClass }}">
                                        {{ $order->payment_status_label }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white border border-base-content/20 rounded-3xl p-5 sm:p-6 shadow-sm">
                            <div class="flex justify-between items-center border-b border-base-content/10 pb-4 mb-4">
                                <h3 class="text-lg sm:text-xl font-bold text-base-content">Detail Item</h3>
                                <span
                                    class="text-xs sm:text-sm font-extrabold text-base-content">{{ $order->items->sum('qty') }}
                                    Item</span>
                            </div>

                            <div class="space-y-4">
                                @foreach ($order->items as $item)
                                    <x-user.order-item :image="$item->menu && $item->menu->image
                                        ? asset('storage/' . $item->menu->image)
                                        : 'https://ui-avatars.com/api/?name=' . urlencode($item->menu->name ?? 'Menu') . '&background=random'" :name="$item->menu->name ?? 'Menu Dihapus'" :description="$item->menu->description ?? ''"
                                        :price="$item->menu
                                            ? $item->menu->formatted_price
                                            : 'Rp ' . number_format($item->price, 0, ',', '.')" :quantity="$item->qty" variant="list" />
                                @endforeach
                            </div>
                        </div>

                        <div>
                            <p class="text-sm font-bold text-base-content mb-2">Catatan untuk kantin</p>
                            <div
                                class="bg-white border border-base-content/20 rounded-xl p-4 text-sm text-base-content/70">
                                {{ $order->notes ?? 'Tidak ada catatan.' }}
                            </div>
                        </div>
                    </div>

                    <div class="w-full lg:w-80 xl:w-96 shrink-0">
                        <div class="space-y-6 sticky top-24">
                            @if ($step == 5)
                                <div
                                    class="bg-white border border-base-content/15 rounded-3xl p-5 text-center shadow-sm">
                                    <p class="text-xs font-bold text-base-content/70 uppercase mb-3">Kode Pengambilan
                                    </p>
                                    <canvas id="qr-code" class="mx-auto rounded-xl"></canvas>
                                    <p class="text-3xl font-black text-base-content tracking-widest mt-4">
                                        {{ $order->pickup_code }}</p>
                                    <p class="text-xs text-base-content/50 mt-2 font-medium">Tunjukkan ke kasir untuk
                                        verifikasi pesanan</p>
                                </div>
                            @endif

                            <div class="bg-white border border-base-content/20 rounded-3xl p-5 sm:p-6 shadow-sm">
                                <h3 class="text-lg font-bold text-base-content mb-2">Total Belanja</h3>
                                <p class="text-3xl font-extrabold text-base-content mb-6">{{ $order->formatted_total }}
                                </p>

                                <div
                                    class="space-y-3 text-xs sm:text-sm font-medium text-base-content/80 border-t border-base-content/10 pt-6 mb-8">
                                    <div class="flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                            class="w-5 h-5 text-fern-700">
                                            <path fill-rule="evenodd"
                                                d="M2.5 4A1.5 1.5 0 001 5.5V6h18v-.5A1.5 1.5 0 0017.5 4h-15zM19 8.5H1v6A1.5 1.5 0 002.5 16h15a1.5 1.5 0 001.5-1.5v-6zM3 13.25a.75.75 0 01.75-.75h1.5a.75.75 0 010 1.5h-1.5a.75.75 0 01-.75-.75zm4.75-.75a.75.75 0 000 1.5h3.5a.75.75 0 000-1.5h-3.5z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <span>Metode: {{ $order->payment_method_label }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                            class="w-5 h-5 text-fern-700">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm.75-13a.75.75 0 00-1.5 0v5c0 .414.336.75.75.75h4a.75.75 0 000-1.5h-3.25V5z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <span>Waktu Pickup: {{ $order->pickup_time->format('H:i, d M Y') }}</span>
                                    </div>
                                </div>

                                @if (!$isBatal)
                                    @if ($step == 6 && $order->reviews->isEmpty())
                                        <button type="button" onclick="document.getElementById('review_modal').showModal()"
                                            class="btn bg-amber-500 hover:bg-amber-600 text-white border-none w-full h-12 min-h-0 rounded-xl font-bold text-base shadow-md active:scale-95 transition-all flex items-center justify-center gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                                class="w-5 h-5">
                                                <path fill-rule="evenodd"
                                                    d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            Beri Ulasan
                                        </button>
                                    @else
                                        <button
                                            class="btn w-full h-12 min-h-0 rounded-xl font-bold text-base transition-all 
                                    {{ $step == 5 ? 'bg-fern-700 hover:bg-fern-800 text-white shadow-md active:scale-95' : 'bg-gray-300 text-gray-500 cursor-not-allowed border-none' }}"
                                            {{ $step != 5 ? 'disabled' : '' }}>
                                            {{ $step == 5 ? 'Ambil Sekarang' : ($step == 6 ? 'Selesai' : 'Belum Siap') }}
                                        </button>
                                    @endif
                                @endif

                                @if ($order->payment_status === 'pending' && $order->status === 'menunggu')
                                    <div class="mt-3">
                                        <button type="button"
                                            onclick="document.getElementById('cancel_user_order_modal').showModal()"
                                            class="btn bg-red-500 hover:bg-red-600 text-white border-none w-full h-12 min-h-0 rounded-xl font-bold text-base shadow-md active:scale-95 transition-all text-center flex items-center justify-center">
                                            Batalkan Pesanan
                                        </button>

                                        <x-modal id="cancel_user_order_modal" type="error" title="Batalkan Pesanan">
                                            Apakah Anda yakin ingin membatalkan pesanan ini? Aksi ini tidak dapat
                                            diurungkan.

                                            <x-slot:footer>
                                                <button type="button"
                                                    onclick="document.getElementById('cancel_user_order_modal').close()"
                                                    class="btn btn-ghost rounded-xl font-bold active:scale-95 transition-all">Batal</button>
                                                <form action="{{ route('order.destroy', $order->id) }}" method="POST"
                                                    class="m-0 p-0 inline-block">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="btn bg-red-600 hover:bg-red-700 text-white border-0 rounded-xl font-bold active:scale-95 transition-all">Ya,
                                                        Batalkan</button>
                                                </form>
                                            </x-slot:footer>
                                        </x-modal>
                                    </div>
                                @endif

                                @if (in_array($order->status, ['selesai', 'dibatalkan']))
                                    <form action="{{ route('order.reorder', $order->id) }}" method="POST"
                                        class="m-0 p-0 mt-3">
                                        @csrf
                                        <button type="submit"
                                            class="btn bg-fern-700 hover:bg-fern-800 text-white border-none w-full h-12 min-h-0 rounded-xl font-bold text-base active:scale-95 transition-all flex items-center justify-center gap-2 shadow-md">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                            </svg>
                                            Beli Lagi
                                        </button>
                                    </form>
                                @endif
                            </div>

                            @if ($step == 6 && $order->reviews->isNotEmpty())
                                <div class="mt-8">
                                    <h3 class="font-bold text-base-content mb-4 text-sm flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                            class="w-5 h-5 text-fern-700">
                                            <path fill-rule="evenodd"
                                                d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm13.36-1.814a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l4.5-6.3z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        Ulasan Anda
                                    </h3>
                                    <div class="space-y-3">
                                        @foreach ($order->reviews as $review)
                                            <div class="bg-white rounded-2xl p-4 border border-base-content/10">
                                                <div class="flex items-center justify-between mb-2">
                                                    <p class="font-bold text-sm text-base-content line-clamp-1">
                                                        {{ $review->menu->name ?? 'Menu' }}</p>
                                                    <div
                                                        class="flex items-center gap-1 bg-vanilla-custard-50 px-2 py-0.5 rounded-md">
                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                            class="w-3.5 h-3.5 text-amber-400" viewBox="0 0 20 20"
                                                            fill="currentColor">
                                                            <path
                                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                        </svg>
                                                        <span
                                                            class="font-bold text-xs text-base-content">{{ $review->rating }}</span>
                                                    </div>
                                                </div>
                                                @if ($review->comment)
                                                    <p class="text-xs text-base-content/80 mt-1.5 leading-relaxed">
                                                        {{ $review->comment }}</p>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>
        @if ($step == 6 && $order->reviews->isEmpty())
            <x-modal id="review_modal" title="Beri Penilaian & Ulasan" :showFooter="false">
                <form action="{{ route('order.review', $order->id) }}" method="POST">
                    @csrf
                    <div class="space-y-6">
                        @foreach ($order->items as $index => $item)
                            @if ($item->menu)
                                @php
                                    $menuName = $item->menu->name ?? 'Menu';
                                    $menuImage =
                                        $item->menu && $item->menu->image
                                            ? asset('storage/' . $item->menu->image)
                                            : 'https://ui-avatars.com/api/?name=' .
                                                urlencode($menuName) .
                                                '&background=random';
                                @endphp
                                <div class="mb-4">
                                    <input type="hidden" name="reviews[{{ $index }}][menu_id]"
                                        value="{{ $item->menu_id }}">
                                    <div class="flex items-center gap-3 mb-3">
                                        <div class="w-12 h-12 rounded-lg bg-base-200 overflow-hidden shrink-0 relative">
                                            <div
                                                class="absolute inset-0 flex items-center justify-center text-fern-700/40">
                                                <span class="loading loading-bars loading-xs"></span>
                                            </div>
                                            <img src="{{ $menuImage }}" onload="this.previousElementSibling?.remove()"
                                                onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($menuName) }}&background=random'; this.onerror=null;"
                                                class="w-full h-full object-cover relative z-10"
                                                alt="{{ $menuName }}">
                                        </div>
                                        <div>
                                            <p class="font-bold text-base-content">{{ $item->menu->name }}</p>
                                            <p class="text-xs text-base-content/70">Berikan rating untuk menu ini</p>
                                        </div>
                                    </div>

                                    <div class="rating rating-md mb-3">
                                        <input type="radio" name="reviews[{{ $index }}][rating]" value="1"
                                            class="mask mask-star-2 bg-amber-400" />
                                        <input type="radio" name="reviews[{{ $index }}][rating]" value="2"
                                            class="mask mask-star-2 bg-amber-400" />
                                        <input type="radio" name="reviews[{{ $index }}][rating]" value="3"
                                            class="mask mask-star-2 bg-amber-400" />
                                        <input type="radio" name="reviews[{{ $index }}][rating]" value="4"
                                            class="mask mask-star-2 bg-amber-400" />
                                        <input type="radio" name="reviews[{{ $index }}][rating]" value="5"
                                            class="mask mask-star-2 bg-amber-400" checked />
                                    </div>

                                    <textarea name="reviews[{{ $index }}][comment]"
                                        class="textarea textarea-bordered w-full rounded-xl bg-white focus:outline-fern-700 resize-none"
                                        placeholder="Tulis ulasan Anda (opsional)..." rows="2"></textarea>
                                        
                                    <div class="mt-3">
                                        <label class="label cursor-pointer p-0 gap-3 justify-start inline-flex">
                                            <input type="checkbox" name="reviews[{{ $index }}][is_anonymous]"
                                                value="1" class="checkbox checkbox-sm rounded-md border-base-content/30 checked:bg-fern-700 checked:border-fern-700 checked:text-white focus:ring-0" />
                                            <span
                                                class="label-text text-xs sm:text-sm font-medium text-base-content/80">Sembunyikan
                                                nama</span>
                                        </label>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button" class="btn btn-ghost rounded-xl font-bold"
                            onclick="document.getElementById('review_modal').close()">Batal</button>
                        <button type="submit"
                            class="btn bg-fern-700 hover:bg-fern-800 text-white border-none rounded-xl font-bold shadow-md active:scale-95 transition-all">Kirim
                            Ulasan</button>
                    </div>
                </form>
            </x-modal>
        @endif
    </main>
@endsection

@push('scripts')
    {{-- 
      Menghasilkan gambar QR Code berdasarkan kode pengambilan menggunakan QRious. 
      Tingkat koreksi kesalahan (level) diatur ke 'H' agar tetap mudah terbaca oleh kamera vendor 
      meskipun kecerahan layar ponsel pembeli rendah atau memiliki goresan fisik.
    --}}
    @if ($step == 5)
        <script src="https://cdnjs.cloudflare.com/ajax/libs/qrious/4.0.2/qrious.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const qrCanvas = document.getElementById('qr-code');
                if (qrCanvas) {
                    new QRious({
                        element: qrCanvas,
                        value: '{{ $order->pickup_code }}',
                        size: 180,
                        level: 'H',
                        foreground: '#1F2937',
                        background: '#ffffff'
                    });
                }
            });
        </script>
    @endif
@endpush

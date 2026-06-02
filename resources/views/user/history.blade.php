@extends('layouts.app')

@section('title', 'Riwayat Pesanan - PNC')

@section('content')
    {{-- 
      Membuat indeks pencarian statis ('items') menggunakan objek JSON yang dirender langsung dari Blade ke state Alpine.js. 
      Hal ini memungkinkan penyaringan status pesanan dan pencarian kata kunci berjalan secara instan di sisi klien 
      tanpa membebani server dengan kueri basis data berulang.
    --}}
    <main class="min-h-screen bg-base-100 pb-12" x-data="{
        selectedStatus: 'Semua Status',
        searchQuery: '',
        items: [
            @foreach ($pendingOnlineGroups as $paymentCode => $group)
                  @php
                      $firstOrder = $group->first();
                      $statusStr = 'Menunggu';
                      $searchStr = strtolower(($firstOrder->canteen->name ?? '') . ' ' . $firstOrder->order_code);
                  @endphp
                  { id: 'group-{{ $paymentCode }}', status: '{{ $statusStr }}', search: '{{ addslashes($searchStr) }}' }, @endforeach
            @foreach ($orders as $order)
                  @php
                      $statusText = match ($order->status) {
                          'menunggu' => 'Menunggu',
                          'dimasak' => 'Diproses',
                          'siap_diambil' => 'Siap Diambil',
                          'selesai' => 'Selesai',
                          'dibatalkan' => 'Dibatalkan',
                          default => 'Menunggu',
                      };
                      $searchStr = strtolower(($order->canteen->name ?? '') . ' ' . $order->order_code);
                  @endphp
                  { id: 'order-{{ $order->id }}', status: '{{ $statusText }}', search: '{{ addslashes($searchStr) }}' }, @endforeach
        ],
        get hasVisibleItems() {
            return this.items.some(item => {
                const statusMatch = this.selectedStatus === 'Semua Status' || item.status === this.selectedStatus;
                const searchMatch = this.searchQuery === '' || item.search.includes(this.searchQuery.toLowerCase());
                return statusMatch && searchMatch;
            });
        }
    }">

        <x-breadcrumb class="pt-8 pb-4" maxWidth="max-w-7xl" :links="[['label' => 'Beranda', 'url' => '/'], ['label' => 'Riwayat']]" />

        <section class="px-3 sm:px-10 md:px-16 lg:px-24 pb-6">
            <div class="max-w-7xl mx-auto">
                <h1 class="text-2xl sm:text-4xl font-bold text-base-content mb-2">Riwayat Pesanan</h1>
                <p class="text-base-content/70 text-sm sm:text-lg font-medium">Menampilkan daftar pesanan yang pernah dibuat
                    pengguna.</p>
            </div>
        </section>

        <section class="px-3 sm:px-10 md:px-16 lg:px-24 mb-8">
            <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-stretch sm:items-center gap-3 sm:gap-4">
                <div class="flex items-center gap-3 w-full sm:w-auto">
                    <select x-model="selectedStatus"
                        class="select select-bordered select-md rounded-full border-base-content/40 w-full sm:w-auto min-w-48 focus:outline-none text-sm sm:text-base">
                        <option value="Semua Status">Semua Status</option>
                        <option value="Menunggu">Menunggu</option>
                        <option value="Diproses">Diproses</option>
                        <option value="Siap Diambil">Siap Diambil</option>
                        <option value="Selesai">Selesai</option>
                        <option value="Dibatalkan">Dibatalkan</option>
                    </select>
                </div>

                <form action="" method="GET" @submit.prevent class="w-full sm:max-w-md relative">
                    <label
                        class="input input-bordered flex items-center w-full shadow-sm rounded-3xl border-base-content/40 focus-within:border-base-content input-md pr-12">
                        <input type="text" x-model="searchQuery" class="grow text-sm sm:text-base font-medium pl-2"
                            placeholder="Cari riwayat pesanan..." />
                    </label>
                    <button type="button"
                        class="absolute right-2 top-1/2 -translate-y-1/2 btn btn-circle btn-sm bg-fern-700 hover:bg-fern-800 text-white border-none min-h-0 w-8 h-8 transition-all duration-200 active:scale-95 flex items-center justify-center cursor-pointer"
                        title="Cari">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <circle cx="11" cy="11" r="8" />
                            <path d="m21 21-4.35-4.35" />
                        </svg>
                    </button>
                </form>
            </div>
        </section>

        <section class="px-3 sm:px-10 md:px-16 lg:px-24">
            <div class="max-w-7xl mx-auto">
                <div class="max-w-4xl space-y-6">

                    @if ($pendingOnlineGroups->isNotEmpty())
                        @foreach ($pendingOnlineGroups as $paymentCode => $group)
                            @php
                                $firstOrder = $group->first();
                                $statusStr = 'Menunggu';
                                $searchStr = strtolower(
                                    ($firstOrder->canteen->name ?? '') . ' ' . $firstOrder->order_code,
                                );
                            @endphp
                            <div x-show="(selectedStatus === 'Semua Status' || '{{ $statusStr }}' === selectedStatus) && ('{{ addslashes($searchStr) }}'.includes(searchQuery.toLowerCase()) || searchQuery === '')"
                                x-transition>
                                <x-user.grouped-order-card :group="$group" />
                            </div>
                        @endforeach
                    @endif

                    @foreach ($orders as $order)
                        @php
                            $statusText = match ($order->status) {
                                'menunggu' => 'Menunggu',
                                'dimasak' => 'Diproses',
                                'siap_diambil' => 'Siap Diambil',
                                'selesai' => 'Selesai',
                                'dibatalkan' => 'Dibatalkan',
                                default => 'Menunggu',
                            };
                            $searchStr = strtolower(($order->canteen->name ?? '') . ' ' . $order->order_code);
                        @endphp
                        <div x-show="(selectedStatus === 'Semua Status' || '{{ $statusText }}' === selectedStatus) && ('{{ addslashes($searchStr) }}'.includes(searchQuery.toLowerCase()) || searchQuery === '')"
                            x-transition>
                            <x-user.order-card :order="$order" />
                        </div>
                    @endforeach

                    <div x-show="items.length === 0"
                        class="p-8 text-center bg-vanilla-custard-50 border border-base-content/25 rounded-3xl">
                        <p class="text-base-content/60 font-medium">Belum ada riwayat pesanan.</p>
                    </div>

                    <div x-show="items.length > 0 && !hasVisibleItems" x-cloak
                        class="p-8 text-center bg-vanilla-custard-50 border border-base-content/25 rounded-3xl">
                        <p class="text-base-content/60 font-medium">Tidak ditemukan pesanan dengan status atau kata kunci
                            tersebut.</p>
                    </div>

                    <div class="pt-4">
                        {{ $orders->links() }}
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection

@push('scripts')
    {{-- Load Midtrans Snap JS jika ada pending online group --}}
    @if ($pendingOnlineGroups->isNotEmpty())
        <script src="{{ config('services.midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}"
            data-client-key="{{ config('services.midtrans.client_key') }}"></script>
        <script>
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

            /**
             * Buka popup Snap Midtrans dari halaman riwayat.
             * snapToken: token dari pesanan pertama dalam grup.
             * retryUrl: URL untuk mendapatkan token baru jika expired.
             */
            function openSnapGroup(snapToken, retryUrl, csrf) {
                const token = csrf || csrfToken;

                if (!snapToken) {
                    fetchNewGroupToken(retryUrl, token);
                    return;
                }
                payWithGroupToken(snapToken, retryUrl, token);
            }

            function payWithGroupToken(token, retryUrl, csrf) {
                window.snap.pay(token, {
                    onSuccess: function(result) {
                        window.location.reload();
                    },
                    onPending: function(result) {
                        // VA belum ditransfer, biarkan polling server-side (Midtrans webhook)
                    },
                    onError: function(result) {
                        console.error('Snap error:', result);
                    },
                    onClose: function() {
                        // User tutup popup, tidak perlu aksi
                    }
                });
            }

            function fetchNewGroupToken(retryUrl, csrf) {
                fetch(retryUrl, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrf,
                            'Accept': 'application/json',
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success && data.snap_token) {
                            payWithGroupToken(data.snap_token, retryUrl, csrf);
                        } else {
                            alert(data.message ?? 'Gagal memperbarui token pembayaran. Silakan coba lagi.');
                        }
                    })
                    .catch(() => alert('Terjadi kesalahan jaringan. Silakan coba lagi.'));
            }
        </script>
    @endif
@endpush

@extends('layouts.vendor')

@section('title', 'Daftar Transaksi - Vendor PNC')

@section('content')

    <div class="max-w-4xl pb-10 lg:pb-0">
        <div class="mb-6 sm:mb-4 flex flex-col md:flex-row md:items-start justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-4xl font-bold text-base-content mb-2">Daftar Transaksi</h1>
                <p class="text-base-content/70 text-sm sm:text-lg font-medium">Kelola dan pantau seluruh pesanan pelanggan.
                </p>
            </div>
        </div>

        <div class="mb-6 flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
            <div class="relative flex-1 sm:max-w-sm">
                <label
                    class="input input-bordered flex items-center w-full shadow-sm rounded-3xl border-base-content/40 focus-within:border-base-content input-md pr-12">
                    <input type="text" id="manual_code_input" placeholder="Masukkan kode pesanan"
                        class="grow text-sm sm:text-base font-bold pl-2 placeholder:font-medium placeholder:text-sm"
                        maxlength="6">
                </label>
                <button type="button" onclick="searchManualCode()"
                    class="absolute right-2 top-1/2 -translate-y-1/2 btn btn-circle btn-sm bg-fern-700 hover:bg-fern-800 text-white border-none min-h-0 w-8 h-8 transition-all duration-200 active:scale-95 flex items-center justify-center cursor-pointer"
                    title="Cari">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <circle cx="11" cy="11" r="8" />
                        <path d="m21 21-4.35-4.35" />
                    </svg>
                </button>
            </div>

            <div class="flex items-center gap-3 w-full sm:w-auto my-1 sm:my-0">
                <div class="h-px bg-base-content/15 grow sm:hidden"></div>
                <span class="text-xs font-bold text-base-content/30 shrink-0">Atau</span>
                <div class="h-px bg-base-content/15 grow sm:hidden"></div>
            </div>

            <button type="button" onclick="document.getElementById('scan_qr_modal').showModal(); startScanner();"
                class="btn bg-fern-700 hover:bg-fern-800 text-white border-none rounded-3xl font-bold shadow-sm active:scale-95 transition-all flex items-center justify-center gap-2 w-full sm:w-auto">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 3.75 9.375v-4.5ZM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 0 1-1.125-1.125v-4.5ZM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 13.5 9.375v-4.5Z" />
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M6.75 6.75h.75v.75h-.75v-.75ZM6.75 16.5h.75v.75h-.75v-.75ZM16.5 6.75h.75v.75h-.75v-.75ZM13.5 13.5h.75v.75h-.75v-.75ZM13.5 19.5h.75v.75h-.75v-.75ZM19.5 13.5h.75v.75h-.75v-.75ZM19.5 19.5h.75v.75h-.75v-.75ZM16.5 16.5h.75v.75h-.75v-.75Z" />
                </svg>
                Scan QR Code
            </button>
        </div>


        <div class="mb-6 flex items-center gap-3 w-full sm:w-auto">
            <select onchange="location = this.value;"
                class="select select-bordered select-md rounded-full border-base-content/40 w-full sm:w-auto min-w-48 focus:outline-none text-sm sm:text-base">
                <option value="{{ route('vendor.order.index') }}" {{ !request('status') ? 'selected' : '' }}>Semua Status
                </option>
                <option value="{{ route('vendor.order.index', ['status' => 'menunggu']) }}"
                    {{ request('status') === 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                <option value="{{ route('vendor.order.index', ['status' => 'dimasak']) }}"
                    {{ request('status') === 'dimasak' ? 'selected' : '' }}>Dimasak</option>
                <option value="{{ route('vendor.order.index', ['status' => 'siap_diambil']) }}"
                    {{ request('status') === 'siap_diambil' ? 'selected' : '' }}>Siap Diambil</option>
                <option value="{{ route('vendor.order.index', ['status' => 'selesai']) }}"
                    {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                <option value="{{ route('vendor.order.index', ['status' => 'dibatalkan']) }}"
                    {{ request('status') === 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
            </select>
        </div>

        <div class="flex flex-col gap-4">

            @forelse($orders as $order)
                <div
                    class="bg-vanilla-custard-50 border border-base-content/30 rounded-2xl p-4 sm:p-6 shadow-sm flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 relative group cursor-pointer hover:border-fern-300 hover:shadow-md transition-all">
                    <a href="{{ route('vendor.order.show', $order->id) }}" class="absolute inset-0 z-10 rounded-2xl"
                        aria-label="Detail Pesanan {{ $order->order_code }}"></a>

                    <div>
                        <h2
                            class="text-base sm:text-lg font-bold text-base-content mb-1 group-hover:text-fern-700 transition-colors">
                            No. Orderan : {{ $order->order_code }}</h2>
                        <p class="text-xs sm:text-sm font-medium text-base-content mb-3">Jenis Pesanan :
                            {{ $order->items->sum('qty') }} item</p>
                        <div class="relative z-20 w-fit">
                            <x-status-badge :status="$order->status_label" />
                        </div>
                    </div>

                    <a href="{{ route('vendor.order.show', $order->id) }}"
                        class="btn bg-fern-700 hover:bg-fern-800 text-white border-none w-full sm:w-auto px-6 shadow-sm font-bold rounded-xl relative z-20 active:scale-95 transition-all">
                        Lihat Detail
                    </a>
                </div>
            @empty
                <div class="bg-vanilla-custard-50 border border-base-content/30 rounded-2xl p-8 text-center shadow-sm">
                    @if (request('status'))
                        <p class="text-base-content/60 font-medium">Tidak ada transaksi dengan status
                            "{{ request('status') === 'siap_diambil' ? 'Siap Diambil' : ucfirst(request('status')) }}".</p>
                    @else
                        <p class="text-base-content/60 font-medium">Belum ada transaksi masuk.</p>
                    @endif
                </div>
            @endforelse

        </div>

        <div class="mt-6">
            {{ $orders->links() }}
        </div>
    </div>

    <x-modal id="scan_qr_modal" title="Scan QR Code Pengambilan">
        <div class="flex flex-col items-center py-4">
            <p class="text-sm font-medium text-base-content/70 mb-4 text-center">Arahkan kamera ke QR Code milik mahasiswa
                untuk memverifikasi pesanan.</p>
            <div id="reader" class="w-full max-w-sm rounded-2xl overflow-hidden border-2 border-fern-700 bg-gray-100">
            </div>
        </div>
        <x-slot:footer>
            <button type="button" onclick="stopScanner(); document.getElementById('scan_qr_modal').close()"
                class="btn btn-ghost rounded-xl font-bold active:scale-95 transition-all w-full">Tutup Kamera</button>
        </x-slot:footer>
    </x-modal>

@endsection

@push('scripts')
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script>
        let html5QrCode = null;

        function searchManualCode() {
            const code = document.getElementById('manual_code_input').value.trim();
            if (code.length === 0) {
                return;
            }
            if (code.length === 6) {
                window.location.href = "{{ url('/vendor/order/scan') }}/" + code;
            } else {
                window.dispatchEvent(new CustomEvent('notify', {
                    detail: {
                        message: 'Kode pesanan harus 6 karakter alphanumeric!',
                        type: 'error'
                    }
                }));
            }
        }

        // Mengaktifkan fitur pencarian cepat saat pengguna menekan tombol Enter pada keyboard
        document.getElementById('manual_code_input')?.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                searchManualCode();
            }
        });

        function startScanner() {
            if (!html5QrCode) {
                html5QrCode = new Html5Qrcode("reader");
            }

            // Memberikan jeda agar transisi modal selesai dimuat sepenuhnya oleh browser sebelum kamera diakses
            setTimeout(() => {
                html5QrCode.start({
                        facingMode: "environment"
                    }, {
                        fps: 10,
                        qrbox: {
                            width: 250,
                            height: 250
                        }
                    },
                    onScanSuccess,
                    onScanFailure
                ).catch(err => {
                    console.error("Gagal memulai kamera", err);
                    alert(
                        "Gagal mengakses kamera! Pastikan Anda mengizinkan akses kamera (Allow) dan membuka website lewat jalur HTTPS."
                        );
                });
            }, 300);
        }

        function stopScanner() {
            if (html5QrCode && html5QrCode.isScanning) {
                html5QrCode.stop().then(() => {
                    html5QrCode.clear();
                }).catch(error => {
                    console.error("Failed to stop scanner. ", error);
                });
            }
        }

        function onScanSuccess(decodedText, decodedResult) {
            // Menghentikan kamera pemindai agar tidak terjadi pengulangan redirect/pemanggilan berulang
            stopScanner();
            document.getElementById('scan_qr_modal').close();

            // Arahkan ke endpoint scan dengan kode hasil pemindaian
            window.location.href = "{{ url('/vendor/order/scan') }}/" + decodedText;
        }

        function onScanFailure(error) {
            // Mengabaikan log error deteksi per frame agar tidak membebani performa browser
        }

        // Pastikan kamera selalu dimatikan jika modal ditutup dengan cara apapun (klik backdrop, ESC, dll)
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('scan_qr_modal');
            if (modal) {
                modal.addEventListener('close', function() {
                    stopScanner();
                });
            }
        });
    </script>
@endpush

@extends('layouts.vendor')

@section('title', 'Daftar Transaksi - Vendor PNC')

@section('content')

<div class="max-w-4xl pb-10 lg:pb-0">
    <div class="mb-6 sm:mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4 border-b border-base-content/10 pb-6">
        <div>
            <h1 class="text-2xl sm:text-4xl font-bold text-base-content mb-2">Daftar Transaksi</h1>
            <p class="text-base-content/70 text-sm sm:text-lg font-medium">Kelola dan pantau seluruh pesanan pelanggan.</p>
        </div>
        
        <div class="flex flex-col sm:flex-row items-center gap-3 w-full md:w-auto">
            <!-- Form Cari/Input Manual 6 Digit -->
            <div class="relative w-full sm:w-64">
                <input type="text" id="manual_code_input" placeholder="6 Digit Kode (cth: AB12CD)" class="input bg-white border-base-content/20 rounded-xl w-full font-bold uppercase tracking-widest text-center shadow-sm" maxlength="6">
                <button type="button" onclick="searchManualCode()" class="absolute right-2 top-2 bottom-2 bg-fern-700 hover:bg-fern-800 text-white px-3 rounded-lg text-xs font-bold transition-colors">
                    Cari
                </button>
            </div>
            
            <p class="text-xs font-bold text-base-content/40 uppercase hidden sm:block">ATAU</p>
            
            <!-- Tombol Scan QR -->
            <button type="button" onclick="document.getElementById('scan_qr_modal').showModal(); startScanner();" class="btn bg-fern-700 hover:bg-fern-800 text-white border-none rounded-xl font-bold shadow-sm active:scale-95 transition-all w-full sm:w-auto">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 3.75 9.375v-4.5ZM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 0 1-1.125-1.125v-4.5ZM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 13.5 9.375v-4.5Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 6.75h.75v.75h-.75v-.75ZM6.75 16.5h.75v.75h-.75v-.75ZM16.5 6.75h.75v.75h-.75v-.75ZM13.5 13.5h.75v.75h-.75v-.75ZM13.5 19.5h.75v.75h-.75v-.75ZM19.5 13.5h.75v.75h-.75v-.75ZM19.5 19.5h.75v.75h-.75v-.75ZM16.5 16.5h.75v.75h-.75v-.75Z" />
                </svg>
                Scan QR Code
            </button>
        </div>
    </div>

    <div class="flex flex-col gap-4">

        @forelse($orders as $order)
            <div class="bg-vanilla-custard-50 border border-base-content/30 rounded-2xl p-4 sm:p-6 shadow-sm flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h2 class="text-base sm:text-lg font-bold text-base-content mb-1">No. Orderan : {{ $order->order_code }}</h2>
                    <p class="text-xs sm:text-sm font-medium text-base-content mb-3">Jenis Pesanan : {{ $order->items->sum('qty') }} item</p>
                    <x-status-badge :status="$order->status_label" />
                </div>

                <a href="{{ route('vendor.order.show', $order->id) }}" class="btn bg-base-300 hover:bg-base-400 text-base-content border-none w-full sm:w-auto px-6 shadow-sm font-bold rounded-xl">
                    Detail
                </a>
            </div>
        @empty
            <div class="bg-vanilla-custard-50 border border-base-content/30 rounded-2xl p-8 text-center shadow-sm">
                <p class="text-base-content/60 font-medium">Belum ada ditransaksi masuk.</p>
            </div>
        @endforelse

    </div>

    <div class="mt-6">
        {{ $orders->links() }}
    </div>
</div>

<x-modal id="scan_qr_modal" title="Scan QR Code Pengambilan">
    <div class="flex flex-col items-center py-4">
        <p class="text-sm font-medium text-base-content/70 mb-4 text-center">Arahkan kamera ke QR Code milik mahasiswa untuk memverifikasi pesanan.</p>
        <div id="reader" class="w-full max-w-sm rounded-2xl overflow-hidden border-2 border-fern-700 bg-gray-100"></div>
    </div>
    <x-slot:footer>
        <button type="button" onclick="stopScanner(); document.getElementById('scan_qr_modal').close()" class="btn btn-ghost rounded-xl font-bold active:scale-95 transition-all w-full">Tutup Kamera</button>
    </x-slot:footer>
</x-modal>

@endsection

@push('scripts')
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
    let html5QrCode = null;

    function searchManualCode() {
        const code = document.getElementById('manual_code_input').value.trim();
        if(code.length === 6) {
            window.location.href = "{{ url('/vendor/order/scan') }}/" + code;
        } else {
            alert('Kode pesanan harus 6 karakter alphanumeric!');
        }
    }

    // Biar bisa tekan enter di input manual
    document.getElementById('manual_code_input')?.addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            searchManualCode();
        }
    });

    function startScanner() {
        if(!html5QrCode) {
            html5QrCode = new Html5Qrcode("reader");
        }
        
        // Beri sedikit delay agar animasi modal selesai dan DOM siap
        setTimeout(() => {
            html5QrCode.start(
                { facingMode: "environment" }, // Paksa gunakan kamera belakang
                { fps: 10, qrbox: { width: 250, height: 250 } },
                onScanSuccess,
                onScanFailure
            ).catch(err => {
                console.error("Gagal memulai kamera", err);
                alert("Gagal mengakses kamera! Pastikan Anda mengizinkan akses kamera (Allow) dan membuka website lewat jalur HTTPS.");
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
        // Hentikan scan agar tidak memicu redirect berulang kali
        stopScanner();
        document.getElementById('scan_qr_modal').close();
        
        // Arahkan ke endpoint scan
        window.location.href = "{{ url('/vendor/order/scan') }}/" + decodedText;
    }

    function onScanFailure(error) {
        // Abaikan error pembacaan per frame (normal saat belum ada QR code)
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

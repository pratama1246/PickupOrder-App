@extends('layouts.app')

@section('title', 'Checkout Pesanan - PNC')

@section('content')
<main class="min-h-screen bg-base-100 pb-16">

    <x-breadcrumb
        class="pt-8 pb-4"
        maxWidth="max-w-7xl"
        :links="[
            ['label' => 'Beranda', 'url' => '/'],
            ['label' => 'Keranjang Belanja', 'url' => '/keranjang'],
            ['label' => 'Checkout']
        ]"
    />

    <section class="px-3 sm:px-10 md:px-16 lg:px-24 pb-6">
        <div class="max-w-7xl mx-auto">
            <h1 class="text-2xl sm:text-4xl font-bold text-base-content mb-1">Checkout Pesanan</h1>
            <p class="text-base-content/70 text-sm sm:text-base font-medium">Selesaikan pembayaran untuk melanjutkan pesananmu</p>
        </div>
    </section>

    <section class="px-3 sm:px-10 md:px-16 lg:px-24">
        {{-- Form dengan id agar bisa dicegat oleh JS AJAX --}}
        <form id="checkout-form" action="{{ route('checkout.store') }}" method="POST" class="max-w-7xl mx-auto flex flex-col lg:flex-row gap-6 lg:gap-8 items-start">
            @csrf

            {{-- Kolom Kiri --}}
            <div class="w-full lg:flex-1 min-w-0 space-y-6">
                
                <div class="bg-vanilla-custard-50 border border-base-content/20 rounded-3xl p-5 sm:p-6 shadow-sm" 
                     x-data="{ selectedTime: 'now', customTime: '' }"
                     @click.outside="if (selectedTime === 'custom' && !customTime) selectedTime = 'now'">
                    <h2 class="text-lg sm:text-xl font-bold text-base-content mb-5">Pilih Jam Pengambilan</h2>
                    
                    <div class="grid grid-cols-2 auto-rows-fr gap-4">

                        <label class="relative cursor-pointer flex flex-col h-full">
                            <input type="radio" name="pickup_time" value="now" class="sr-only" x-model="selectedTime">
                            <div class="rounded-2xl border-2 border-base-content/10 bg-base-100 p-4 text-center hover:bg-base-200 transition-all h-full flex flex-col justify-center items-center" :class="selectedTime === 'now' ? 'bg-fern-700 border-fern-700 text-white shadow-md' : 'text-base-content'">
                                <h3 class="font-bold text-lg">Sekarang</h3>
                                <p class="text-xs sm:text-sm font-medium opacity-80 mt-1">Siap dalam 3-15 menit</p>
                            </div>
                        </label>

                        <label class="relative cursor-pointer flex flex-col h-full">
                            <input type="radio" name="pickup_time" value="09.20" class="sr-only" x-model="selectedTime">
                            <div class="rounded-2xl border-2 border-base-content/10 bg-base-100 p-4 text-center hover:bg-base-200 transition-all h-full flex flex-col justify-center items-center" :class="selectedTime === '09.20' ? 'bg-fern-700 border-fern-700 text-white shadow-md' : 'text-base-content'">
                                <h3 class="font-bold text-lg">09.20</h3>
                                <p class="text-xs sm:text-sm font-medium opacity-80 mt-1">Istirahat Pertama</p>
                            </div>
                        </label>

                        <label class="relative cursor-pointer flex flex-col h-full">
                            <input type="radio" name="pickup_time" value="11.30" class="sr-only" x-model="selectedTime">
                            <div class="rounded-2xl border-2 border-base-content/10 bg-base-100 p-4 text-center hover:bg-base-200 transition-all h-full flex flex-col justify-center items-center" :class="selectedTime === '11.30' ? 'bg-fern-700 border-fern-700 text-white shadow-md' : 'text-base-content'">
                                <h3 class="font-bold text-lg">11.30</h3>
                                <p class="text-xs sm:text-sm font-medium opacity-80 mt-1">Istirahat Kedua</p>
                            </div>
                        </label>

                        <label class="relative cursor-pointer flex flex-col h-full">
                            <input type="radio" name="pickup_time" value="custom" class="sr-only" x-model="selectedTime">
                            <div class="rounded-2xl border-2 border-base-content/10 bg-base-100 p-4 text-center hover:bg-base-200 transition-all h-full flex flex-col justify-center items-center" :class="selectedTime === 'custom' ? 'bg-fern-700 border-fern-700 text-white shadow-md' : 'text-base-content'">
                                <h3 class="font-bold text-lg">Atur Jam Lainnya</h3>
                            </div>
                        </label>
                    </div>

                    {{-- Custom Time Picker Input --}}
                    <div class="mt-4" x-show="selectedTime === 'custom'" x-transition>
                        <label class="block text-sm font-bold text-base-content mb-2">Tentukan Jam Pengambilan</label>
                        <input type="time" name="custom_time" x-model="customTime" class="input input-bordered w-full rounded-2xl border-base-content/20 bg-white focus:outline-none focus:border-fern-700 text-base-content" :required="selectedTime === 'custom'">
                    </div>

                    <div id="error-pickup_time" class="mt-3 text-sm font-medium text-red-600 hidden"></div>
                    @error('pickup_time')
                        <p class="mt-3 text-sm font-medium text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Pilih Metode Pembayaran --}}
                <div class="bg-vanilla-custard-50 border border-base-content/20 rounded-3xl p-5 sm:p-6 shadow-sm">
                    <h2 class="text-lg sm:text-xl font-bold text-base-content mb-5">Pilih Metode Pembayaran</h2>
                    
                    <div class="space-y-4">
                        {{-- Online Payment (Midtrans) --}}
                        <label class="relative flex items-center gap-4 cursor-pointer p-4 rounded-2xl border-2 border-base-content/10 bg-base-100 hover:bg-base-200 transition-all has-[:checked]:bg-fern-50/50 has-[:checked]:border-fern-700">
                            <input type="radio" name="payment_method" value="qris" class="radio radio-success radio-sm" checked>
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-base-content/80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="2" y="5" width="20" height="14" rx="2" ry="2"></rect>
                                        <line x1="2" y1="10" x2="22" y2="10"></line>
                                    </svg>
                                    <h3 class="font-bold text-base text-base-content">Bayar Online</h3>
                                </div>
                                <p class="text-xs sm:text-sm text-base-content/60 font-medium mt-1">Bayar menggunakan QRIS, e-Wallet, atau Transfer Bank</p>
                            </div>
                        </label>

                        {{-- Cash / Offline --}}
                        <label class="relative flex items-center gap-4 cursor-pointer p-4 rounded-2xl border-2 border-base-content/10 bg-base-100 hover:bg-base-200 transition-all has-[:checked]:bg-fern-50/50 has-[:checked]:border-fern-700">
                            <input type="radio" name="payment_method" value="bayar_di_warung" class="radio radio-success radio-sm">
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-base-content/80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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
                                    <h3 class="font-bold text-base text-base-content">Bayar Di Warung</h3>
                                </div>
                                <p class="text-xs sm:text-sm text-base-content/60 font-medium mt-1">Bayar langsung saat mengambil pesanan</p>
                            </div>
                        </label>
                    </div>

                    <div id="error-payment_method" class="mt-3 text-sm font-medium text-red-600 hidden"></div>
                    @error('payment_method')
                        <p class="mt-3 text-sm font-medium text-red-600">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            {{-- Kolom Kanan (Order Summary & Action) --}}
            <div class="w-full lg:w-[450px] shrink-0 space-y-6">
                
                <div class="bg-vanilla-custard-50 border border-base-content/20 rounded-3xl p-5 sm:p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-5">
                        <h2 class="text-lg sm:text-xl font-bold text-base-content">Detail Order</h2>
                    </div>

                    @foreach ($grouped as $canteenId => $data)
                        <div class="bg-white border border-base-content/20 rounded-2xl p-4 mb-4 last:mb-0">
                            <div class="flex items-center justify-between mb-4 border-b border-base-content/10 pb-3">
                                <h3 class="font-bold text-base-content">{{ $data['canteen_name'] }}</h3>
                                <span class="text-xs font-bold text-base-content/60">{{ count($data['items']) }} Pesanan</span>
                            </div>

                            <div class="space-y-1 mb-2">
                                @foreach ($data['items'] as $item)
                                    <x-user.order-item
                                        :image="$item['image'] ? asset('storage/' . $item['image']) : asset('assets/food/' . strtolower(str_replace(' ', '-', $item['name']))) . '.jpg'"
                                        :name="$item['name']"
                                        :description="$item['description'] ?? null"
                                        :price="'Rp. ' . number_format($item['price'], 0, ',', '.')"
                                        :quantity="$item['quantity']"
                                        variant="list"
                                    />
                                @endforeach
                            </div>

                            {{-- Catatan dari session keranjang --}}
                            @if(!empty($notes[$canteenId]))
                                <div class="mt-2 text-xs text-base-content/70 bg-base-100 px-3 py-2 rounded-lg border border-base-content/10 flex items-start gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 mt-0.5 text-base-content/50 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                                    </svg>
                                    <span class="italic">Catatan: "{{ $notes[$canteenId] }}"</span>
                                </div>
                            @endif
                            <input type="hidden" name="notes[{{ $canteenId }}]" value="{{ $notes[$canteenId] ?? '' }}">
                        </div>
                    @endforeach
                </div>

                <div class="bg-vanilla-custard-50 border border-base-content/20 rounded-3xl p-5 sm:p-6 shadow-sm">
                    <div class="mb-6">
                        <h3 class="text-base text-base-content/70 font-medium mb-1">Total Belanja</h3>
                        <p class="text-2xl sm:text-3xl font-extrabold text-base-content">Rp. {{ number_format($total, 0, ',', '.') }},00</p>
                    </div>

                    <div class="flex gap-3">
                        <a href="/keranjang" class="btn bg-red-500 hover:bg-red-600 text-white border-none flex-1 rounded-xl font-bold shadow-md active:scale-95 transition-all h-12 min-h-0 text-center flex items-center justify-center">
                            Batalkan
                        </a>
                        <button id="checkout-submit-btn" type="submit" class="btn bg-fern-700 hover:bg-fern-800 text-white border-none flex-1 rounded-xl font-bold shadow-md active:scale-95 transition-all h-12 min-h-0 text-center flex items-center justify-center">
                            Konfirmasi
                        </button>
                    </div>
                </div>

            </div>

        </form>
    </section>

</main>

{{-- Midtrans Snap JS -- hanya dimuat di halaman checkout --}}
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
<script>
    (function () {
        const form      = document.getElementById('checkout-form');
        const submitBtn = document.getElementById('checkout-submit-btn');

        if (!form) return;

        form.addEventListener('submit', function (e) {
            e.preventDefault();

            // Loading state
            submitBtn.disabled     = true;
            submitBtn.innerHTML    = '<span class="loading loading-bars loading-md"></span>';

            // Bersihkan pesan error sebelumnya
            document.querySelectorAll('[id^="error-"]').forEach(el => el.classList.add('hidden'));

            const formData = new FormData(form);

            fetch(form.action, {
                method:  'POST',
                body:    formData,
                headers: {
                    'Accept':           'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            })
            .then(function (res) {
                if (!res.ok) {
                    return res.json().then(function (err) { throw err; });
                }
                return res.json();
            })
            .then(function (data) {
                if (!data.success) {
                    showError(data.message || 'Terjadi kesalahan. Silakan coba lagi.');
                    resetBtn();
                    return;
                }

                if (data.snap_token) {
                    // Pembayaran Online: tampilkan popup Midtrans Snap di atas halaman checkout
                    window.snap.pay(data.snap_token, {
                        onSuccess: function () {
                            window.location.href = data.redirect;
                        },
                        onPending: function () {
                            // Pesanan tersimpan, user bisa bayar nanti dari riwayat
                            window.location.href = data.redirect;
                        },
                        onError: function () {
                            window.location.href = data.redirect;
                        },
                        onClose: function () {
                            // User menutup popup tanpa bayar - pesanan tetap tersimpan (status: pending)
                            // Arahkan ke riwayat agar bisa bayar ulang dari sana
                            window.location.href = data.redirect;
                        },
                    });
                } else {
                    // Pembayaran Cash: langsung redirect ke riwayat
                    window.location.href = data.redirect;
                }
            })
            .catch(function (err) {
                console.error('Checkout error:', err);

                // Tampilkan error validasi per field jika ada
                if (err && err.errors) {
                    Object.keys(err.errors).forEach(function (field) {
                        const el = document.getElementById('error-' + field);
                        if (el) {
                            el.textContent = err.errors[field][0];
                            el.classList.remove('hidden');
                        }
                    });
                } else {
                    showError((err && err.message) || 'Terjadi kesalahan saat memproses pesanan.');
                }

                resetBtn();
            });
        });

        function resetBtn() {
            submitBtn.disabled  = false;
            submitBtn.innerHTML = 'Konfirmasi';
        }

        function showError(msg) {
            const el = document.getElementById('error-payment_method');
            if (el) {
                el.textContent = msg;
                el.classList.remove('hidden');
            }
        }
    })();
</script>
@endsection

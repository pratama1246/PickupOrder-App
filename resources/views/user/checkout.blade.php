@extends('layouts.app')

@section('title', 'Checkout Pesanan - PNC')

@section('content')
    <main class="min-h-screen bg-base-100 pb-16">

        <x-breadcrumb class="pt-8 pb-4" maxWidth="max-w-7xl" :links="[
            ['label' => 'Beranda', 'url' => '/'],
            ['label' => 'Keranjang Belanja', 'url' => route('cart.index')],
            ['label' => 'Checkout'],
        ]" />

        <section class="px-3 sm:px-10 md:px-16 lg:px-24 pb-6">
            <div class="max-w-7xl mx-auto">
                <h1 class="text-2xl sm:text-4xl font-bold text-base-content mb-1">Checkout Pesanan</h1>
                <p class="text-base-content/70 text-sm sm:text-base font-medium">Selesaikan pembayaran untuk melanjutkan
                    pesananmu</p>
            </div>
        </section>

        <section class="px-3 sm:px-10 md:px-16 lg:px-24">
            <form id="checkout-form" action="{{ route('checkout.store') }}" method="POST" enctype="multipart/form-data"
                class="max-w-7xl mx-auto flex flex-col lg:flex-row gap-6 lg:gap-8 items-start">
                @csrf
                <input type="file" name="payment_proof" id="real_payment_proof_input" class="hidden" />

                <div class="w-full lg:flex-1 min-w-0 space-y-6">

                    {{-- 
                      Menggunakan Alpine.js untuk mengelola pilihan waktu pickup. 
                      Jika pengguna memilih opsi custom lalu mengklik di luar area tanpa mengisi waktu secara spesifik, 
                      maka pilihan akan direset otomatis kembali ke 'now' untuk mencegah pengiriman data waktu kosong.
                    --}}
                    <div class="bg-vanilla-custard-50 border border-base-content/20 rounded-3xl p-5 sm:p-6 shadow-sm"
                        x-data="{ selectedTime: 'now', customTime: '' }"
                        @click.outside="if (selectedTime === 'custom' && !customTime) selectedTime = 'now'">
                        <h2 class="text-lg sm:text-xl font-bold text-base-content mb-5">Pilih Jam Pengambilan</h2>

                        <div class="grid grid-cols-2 auto-rows-fr gap-4">

                            <label class="relative cursor-pointer flex flex-col h-full">
                                <input type="radio" name="pickup_time" value="now" class="sr-only"
                                    x-model="selectedTime">
                                <div class="rounded-2xl border-2 border-base-content/10 bg-base-100 p-4 text-center hover:bg-base-200 transition-all h-full flex flex-col justify-center items-center"
                                    :class="selectedTime === 'now' ? 'bg-fern-700 border-fern-700 text-white shadow-md' :
                                        'text-base-content'">
                                    <h3 class="font-bold text-lg">Sekarang</h3>
                                    <p class="text-xs sm:text-sm font-medium opacity-80 mt-1">Siap dalam 3-15 menit</p>
                                </div>
                            </label>

                            <label class="relative cursor-pointer flex flex-col h-full">
                                <input type="radio" name="pickup_time" value="09.20" class="sr-only"
                                    x-model="selectedTime">
                                <div class="rounded-2xl border-2 border-base-content/10 bg-base-100 p-4 text-center hover:bg-base-200 transition-all h-full flex flex-col justify-center items-center"
                                    :class="selectedTime === '09.20' ? 'bg-fern-700 border-fern-700 text-white shadow-md' :
                                        'text-base-content'">
                                    <h3 class="font-bold text-lg">09.20</h3>
                                    <p class="text-xs sm:text-sm font-medium opacity-80 mt-1">Istirahat Pertama</p>
                                </div>
                            </label>

                            <label class="relative cursor-pointer flex flex-col h-full">
                                <input type="radio" name="pickup_time" value="11.30" class="sr-only"
                                    x-model="selectedTime">
                                <div class="rounded-2xl border-2 border-base-content/10 bg-base-100 p-4 text-center hover:bg-base-200 transition-all h-full flex flex-col justify-center items-center"
                                    :class="selectedTime === '11.30' ? 'bg-fern-700 border-fern-700 text-white shadow-md' :
                                        'text-base-content'">
                                    <h3 class="font-bold text-lg">11.30</h3>
                                    <p class="text-xs sm:text-sm font-medium opacity-80 mt-1">Istirahat Kedua</p>
                                </div>
                            </label>

                            <label class="relative cursor-pointer flex flex-col h-full">
                                <input type="radio" name="pickup_time" value="custom" class="sr-only"
                                    x-model="selectedTime">
                                <div class="rounded-2xl border-2 border-base-content/10 bg-base-100 p-4 text-center hover:bg-base-200 transition-all h-full flex flex-col justify-center items-center"
                                    :class="selectedTime === 'custom' ? 'bg-fern-700 border-fern-700 text-white shadow-md' :
                                        'text-base-content'">
                                    <h3 class="font-bold text-lg">Atur Jam Lainnya</h3>
                                </div>
                            </label>
                        </div>

                        <div class="mt-4" x-show="selectedTime === 'custom'" x-transition>
                            <label class="block text-sm font-bold text-base-content mb-2">Tentukan Jam Pengambilan</label>
                            <input type="time" name="custom_time" x-model="customTime"
                                class="input input-bordered w-full rounded-2xl border-base-content/20 bg-white focus:outline-none focus:border-fern-700 text-base-content"
                                :required="selectedTime === 'custom'">
                        </div>

                        <div id="error-pickup_time" class="mt-3 text-sm font-medium text-red-600 hidden"></div>
                        @error('pickup_time')
                            <p class="mt-3 text-sm font-medium text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    @php
                        $canteenData = reset($grouped);
                        $qrisImage = $canteenData['qris_image'] ?? null;
                    @endphp
                    <div class="bg-vanilla-custard-50 border border-base-content/20 rounded-3xl p-5 sm:p-6 shadow-sm"
                        x-data="{ paymentMethod: 'qris' }">
                        <h2 class="text-lg sm:text-xl font-bold text-base-content mb-5">Pilih Metode Pembayaran</h2>

                        <div class="space-y-4">
                            {{-- Online Payment (Midtrans) --}}
                            <label
                                class="relative flex items-center gap-4 cursor-pointer p-4 rounded-2xl border-2 border-base-content/10 bg-base-100 hover:bg-base-200 transition-all has-checked:bg-fern-50/50 has-checked:border-fern-700">
                                <input type="radio" name="payment_method" value="qris"
                                    class="radio radio-success radio-sm" x-model="paymentMethod">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-base-content/80"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="2" y="5" width="20" height="14" rx="2" ry="2">
                                            </rect>
                                            <line x1="2" y1="10" x2="22" y2="10"></line>
                                        </svg>
                                        <h3 class="font-bold text-base text-base-content">Bayar Online</h3>
                                    </div>
                                    <p class="text-xs sm:text-sm text-base-content/60 font-medium mt-1">Bayar menggunakan
                                        QRIS, e-Wallet, atau Transfer Bank</p>
                                </div>
                            </label>

                            {{-- QRIS Manual (Kantin) --}}
                            @if ($qrisImage)
                            <label
                                class="relative flex items-center gap-4 cursor-pointer p-4 rounded-2xl border-2 border-base-content/10 bg-base-100 hover:bg-base-200 transition-all has-checked:bg-fern-50/50 has-checked:border-fern-700">
                                <input type="radio" name="payment_method" value="qris_manual"
                                    class="radio radio-success radio-sm" x-model="paymentMethod">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-base-content/80"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                            <rect x="7" y="7" width="3" height="3"></rect>
                                            <rect x="14" y="7" width="3" height="3"></rect>
                                            <rect x="7" y="14" width="3" height="3"></rect>
                                            <rect x="14" y="14" width="3" height="3"></rect>
                                        </svg>
                                        <h3 class="font-bold text-base text-base-content">Transfer QRIS Kantin (Manual)</h3>
                                    </div>
                                    <p class="text-xs sm:text-sm text-base-content/60 font-medium mt-1">Scan QRIS kantin, transfer, dan unggah bukti transfer</p>
                                </div>
                            </label>
                            @endif

                            {{-- Bayar Di Warung --}}
                            <label
                                class="relative flex items-center gap-4 cursor-pointer p-4 rounded-2xl border-2 border-base-content/10 bg-base-100 hover:bg-base-200 transition-all has-checked:bg-fern-50/50 has-checked:border-fern-700">
                                <input type="radio" name="payment_method" value="bayar_di_warung"
                                    class="radio radio-success radio-sm" x-model="paymentMethod">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-base-content/80"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round">
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
                                    <p class="text-xs sm:text-sm text-base-content/60 font-medium mt-1">Bayar langsung saat
                                        mengambil pesanan</p>
                                </div>
                            </label>
                        </div>

                        {{-- Panel Scan QRIS dan Upload Bukti --}}
                        @if ($qrisImage)
                        <div class="mt-5 pt-5 border-t border-base-content/10 space-y-5" x-show="paymentMethod === 'qris_manual'" x-transition>
                            <div class="flex flex-col items-center bg-white border border-base-content/20 rounded-2xl p-5 shadow-xs">
                                <h3 class="font-bold text-base-content mb-3 text-center text-sm sm:text-base">QRIS {{ $canteenData['canteen_name'] }}</h3>
                                <div class="w-48 h-48 bg-base-200 rounded-2xl overflow-hidden mb-3 border border-base-content/10 relative shadow-inner">
                                    <img src="{{ asset('storage/' . $qrisImage) }}" alt="QRIS Kantin" class="w-full h-full object-contain p-1" />
                                </div>
                                <a href="{{ asset('storage/' . $qrisImage) }}" download="QRIS-{{ Str::slug($canteenData['canteen_name']) }}.png" class="btn btn-outline border-fern-700 text-fern-700 hover:bg-fern-700 hover:text-white rounded-xl btn-sm font-bold min-h-0 h-9 px-4 transition-colors">
                                    Unduh QR Code
                                </a>
                            </div>

                            <div class="space-y-2" x-data="{ proofPreview: '' }">
                                <label class="block text-sm font-bold text-base-content">Unggah Bukti Pembayaran</label>
                                
                                <label
                                    class="relative flex flex-col items-center justify-center w-full h-40 border-2 border-dashed border-base-content/25 rounded-2xl cursor-pointer hover:bg-base-content/5 hover:border-fern-700 transition-colors group overflow-hidden bg-white shadow-xs">
                                    <img x-show="proofPreview" :src="proofPreview" class="absolute inset-0 w-full h-full object-contain p-2"
                                        style="display: none;" />
                                    <div class="flex flex-col items-center justify-center pb-5 pt-4 px-4 text-center z-10"
                                        :class="proofPreview ?
                                            'absolute inset-0 bg-black/50 text-white opacity-0 hover:opacity-100 transition-opacity duration-200' :
                                            'text-base-content/60'">
                                        <svg class="w-8 h-8 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <p class="mb-1 text-xs"><span class="font-bold">Klik untuk mengunggah</span> bukti transfer</p>
                                        <p class="text-xxs opacity-75">Format foto bukti transfer (JPG, PNG, WEBP)</p>
                                    </div>
                                    <input type="file" accept="image/*" class="hidden"
                                        @change="if ($event.target.files.length) { proofPreview = URL.createObjectURL($event.target.files[0]); handleImageUpload($event.target.files[0]); }" />
                                </label>
                            </div>
                        </div>
                        @endif

                        <div id="error-payment_method" class="mt-3 text-sm font-medium text-red-600 hidden"></div>
                        @error('payment_method')
                            <p class="mt-3 text-sm font-medium text-red-600">{{ $message }}</p>
                        @enderror
                        <div id="error-payment_proof" class="mt-3 text-sm font-medium text-red-600 hidden"></div>
                        @error('payment_proof')
                            <p class="mt-3 text-sm font-medium text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                </div>

                <div class="w-full lg:w-[450px] shrink-0 space-y-6">

                    <div class="bg-vanilla-custard-50 border border-base-content/20 rounded-3xl p-5 sm:p-6 shadow-sm">
                        <div class="flex items-center justify-between mb-5">
                            <h2 class="text-lg sm:text-xl font-bold text-base-content">Detail Order</h2>
                        </div>

                        @foreach ($grouped as $canteenId => $data)
                            <div class="bg-white border border-base-content/20 rounded-2xl p-4 mb-4 last:mb-0">
                                <div class="flex items-center justify-between mb-4 border-b border-base-content/10 pb-3">
                                    <h3 class="font-bold text-base-content">{{ $data['canteen_name'] }}</h3>
                                    <span class="text-xs font-bold text-base-content/60">{{ count($data['items']) }}
                                        Pesanan</span>
                                </div>

                                <div class="space-y-1 mb-2">
                                    @foreach ($data['items'] as $item)
                                        <x-user.order-item :image="$item['image']
                                            ? asset('storage/' . $item['image'])
                                            : 'https://ui-avatars.com/api/?name=' . urlencode($item['name']) . '&background=random'" :name="$item['name']" :description="$item['description'] ?? null"
                                            :price="'Rp. ' . number_format($item['price'], 0, ',', '.')" :quantity="$item['quantity']" variant="list" />
                                    @endforeach
                                </div>

                                @if (!empty($notes[$canteenId]))
                                    <div
                                        class="mt-2 text-xs text-base-content/70 bg-base-100 px-3 py-2 rounded-lg border border-base-content/10 flex items-start gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="w-3.5 h-3.5 mt-0.5 text-base-content/50 shrink-0" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                                        </svg>
                                        <span class="italic">Catatan: "{{ $notes[$canteenId] }}"</span>
                                    </div>
                                @endif
                                <input type="hidden" name="notes[{{ $canteenId }}]"
                                    value="{{ $notes[$canteenId] ?? '' }}">
                            </div>
                        @endforeach
                    </div>

                    <div class="bg-vanilla-custard-50 border border-base-content/20 rounded-3xl p-5 sm:p-6 shadow-sm">
                        <div class="mb-6">
                            <h3 class="text-base text-base-content/70 font-medium mb-1">Total Belanja</h3>
                            <p class="text-2xl sm:text-3xl font-extrabold text-base-content">Rp.
                                {{ number_format($total, 0, ',', '.') }},00</p>
                        </div>

                        <div class="flex gap-3">
                            <a href="{{ route('cart.index') }}" id="checkout-cancel-btn"
                                class="btn bg-red-500 hover:bg-red-600 text-white border-none flex-1 rounded-xl font-bold shadow-md active:scale-95 transition-all h-12 min-h-0 text-center flex items-center justify-center">
                                Batalkan
                            </a>
                            <button id="checkout-submit-btn" type="submit"
                                class="btn bg-fern-700 hover:bg-fern-800 text-white border-none flex-1 rounded-xl font-bold shadow-md active:scale-95 transition-all h-12 min-h-0 text-center flex items-center justify-center">
                                Konfirmasi
                            </button>
                        </div>
                    </div>

                </div>

            </form>
        </section>

    </main>

    <script src="{{ config('services.midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}"
        data-client-key="{{ config('services.midtrans.client_key') }}"></script>
    <script>
        window.handleImageUpload = function(file) {
            if (!file) return;

            const reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onload = function(event) {
                const img = new Image();
                img.src = event.target.result;
                img.onload = function() {
                    const canvas = document.createElement('canvas');
                    let width = img.width;
                    let height = img.height;

                    const MAX_WIDTH = 1000;
                    const MAX_HEIGHT = 1000;

                    if (width > height) {
                        if (width > MAX_WIDTH) {
                            height *= MAX_WIDTH / width;
                            width = MAX_WIDTH;
                        }
                    } else {
                        if (height > MAX_HEIGHT) {
                            width *= MAX_HEIGHT / height;
                            height = MAX_HEIGHT;
                        }
                    }

                    canvas.width = width;
                    canvas.height = height;

                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0, width, height);

                    canvas.toBlob(function(blob) {
                        const compressedFile = new File([blob], file.name.substring(0, file.name.lastIndexOf('.')) + '_compressed.jpg', {
                            type: 'image/jpeg',
                            lastModified: Date.now()
                        });

                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(compressedFile);

                        const realInput = document.getElementById('real_payment_proof_input');
                        if (realInput) {
                            realInput.files = dataTransfer.files;
                            console.log('File compressed to: ' + (compressedFile.size / 1024).toFixed(2) + ' KB');
                        }
                    }, 'image/jpeg', 0.7); // compress to JPEG with 70% quality
                };
            };
        };

        (function() {
            const form = document.getElementById('checkout-form');
            const submitBtn = document.getElementById('checkout-submit-btn');
            let isSubmitting = false;

            if (!form) return;

            form.addEventListener('submit', function(e) {
                e.preventDefault();
                // Menghentikan propagasi event untuk memblokir pemicu loading-spinner global di app.js, 
                // karena halaman checkout memiliki penanganan loading dan popup pembayaran online sendiri (Midtrans Snap).
                e.stopPropagation();

                if (isSubmitting) return;
                isSubmitting = true;

                // Loading state
                submitBtn.disabled = true;
                submitBtn.innerHTML = 'Memproses...';
                submitBtn.classList.add('opacity-70');

                const cancelBtn = document.getElementById('checkout-cancel-btn');
                if (cancelBtn) {
                    cancelBtn.style.pointerEvents = 'none';
                    cancelBtn.style.opacity = '0.5';
                }

                // Bersihkan pesan error sebelumnya
                document.querySelectorAll('[id^="error-"]').forEach(el => el.classList.add('hidden'));

                const formData = new FormData(form);

                const timeoutId = setTimeout(function() {
                    if (isSubmitting) {
                        showError('Waktu permintaan habis (Timeout). Server tidak merespons.');
                        resetBtn();
                    }
                }, 10000);

                fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    })
                    .then(async function(res) {
                        let data;
                        try {
                            data = await res.json();
                        } catch (e) {
                            throw new Error('Terjadi kesalahan (Status ' + res.status +
                                '). Mohon tunggu.');
                        }

                        if (!res.ok) {
                            throw data;
                        }

                        return data;
                    })
                    .then(function(data) {
                        clearTimeout(timeoutId);

                        if (!data.success) {
                            showError(data.message || 'Terjadi kesalahan. Silakan coba lagi.');
                            resetBtn();
                            return;
                        }

                        if (data.snap_token) {
                            // Pembayaran Online: tampilkan popup Midtrans Snap di atas halaman checkout
                            window.snap.pay(data.snap_token, {
                                onSuccess: function() {
                                    window.location.href = data.redirect;
                                },
                                onPending: function() {
                                    // Pesanan tersimpan, user bisa bayar nanti dari riwayat
                                    window.location.href = data.redirect;
                                },
                                onError: function() {
                                    window.location.href = data.redirect;
                                },
                                onClose: function() {
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
                    .catch(function(err) {
                        clearTimeout(timeoutId);
                        console.error('Checkout error:', err);

                        try {
                            if (err && err.errors && typeof err.errors === 'object') {
                                Object.keys(err.errors).forEach(function(field) {
                                    const el = document.getElementById('error-' + field);
                                    if (el) {
                                        // Ambil pesan pertama dari array, atau jika string ambil langsung
                                        el.textContent = Array.isArray(err.errors[field]) ? err
                                            .errors[field][0] : err.errors[field];
                                        el.classList.remove('hidden');
                                    }
                                });
                            } else {
                                showError((err && err.message) ||
                                    'Terjadi kesalahan saat memproses pesanan.');
                            }
                        } catch (fallbackErr) {
                            console.error('Crash in error handler:', fallbackErr);
                            showError('Terjadi kesalahan sistem yang tidak diketahui.');
                        }

                        resetBtn();
                    });
            });

            function resetBtn() {
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Konfirmasi';
                submitBtn.classList.remove('opacity-70');
                isSubmitting = false;

                const cancelBtn = document.getElementById('checkout-cancel-btn');
                if (cancelBtn) {
                    cancelBtn.style.pointerEvents = 'auto';
                    cancelBtn.style.opacity = '1';
                }
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

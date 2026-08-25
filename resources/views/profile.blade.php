{{-- 
  Menentukan master layout secara dinamis berdasarkan tipe role pengguna agar halaman pengaturan profil 
  ini dapat dipakai secara global bagi Mahasiswa (app), Admin (admin), maupun Vendor (vendor).
--}}
@php
    $layout = 'layouts.app';
    if (auth()->user()->isAdmin()) {
        $layout = 'layouts.admin';
    } elseif (auth()->user()->isVendor()) {
        $layout = 'layouts.vendor';
    }
@endphp

@extends($layout)

@section('title', 'Profil & Pengaturan - PNC')

@section('content')
    <div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-shadow-grey-900">Profil & Pengaturan</h1>
            <p class="text-sm text-base-content/70 mt-1">Kelola informasi pribadi dan pengaturan keamanan akun Anda.</p>
        </div>

        {{-- 
          Mendengarkan event global 'avatar-cropped' yang ditrigger oleh Cropper.js 
          untuk memperbarui visualisasi pratinjau avatar terpilih secara real-time di UI.
        --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6" x-data="{ avatarPreview: '{{ $user->avatar ? asset('storage/' . $user->avatar) : '' }}' }"
            @avatar-cropped.window="avatarPreview = $event.detail">

            <div class="md:col-span-1 space-y-6">
                <div class="bg-white border border-base-content/10 rounded-3xl p-6 shadow-sm text-center">
                    <div class="relative w-24 h-24 mx-auto mb-4 group cursor-pointer"
                        @click="document.getElementById('avatar-input').click()" title="Klik untuk ubah foto profil">
                        <div
                            class="w-full h-full rounded-full ring ring-fern-50 ring-offset-base-100 ring-offset-2 overflow-hidden flex items-center justify-center bg-base-100">
                            <template x-if="avatarPreview">
                                <img :src="avatarPreview" alt="Avatar" class="w-full h-full object-cover" />
                            </template>
                            <template x-if="!avatarPreview">
                                <div
                                    class="bg-fern-100 text-fern-700 w-full h-full flex items-center justify-center text-4xl font-bold uppercase">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                            </template>
                        </div>

                        <div
                            class="absolute inset-0 bg-black/40 rounded-full flex flex-col items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white mb-0.5" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3 9a2 2 0 0 1 2-2h.93a2 2 0 0 0 1.66-1.01l.82-1.23A2 2 0 0 1 10.08 4h3.84a2 2 0 0 1 1.67 1.01l.82 1.23A2 2 0 0 0 18.07 7H19a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V9z" />
                                <circle cx="12" cy="13" r="3" />
                            </svg>
                            <span class="text-[9px] text-white font-bold">Ubah</span>
                        </div>
                    </div>

                    @error('avatar')
                        <p class="text-error text-xs mb-3 font-semibold">{{ $message }}</p>
                    @enderror

                    <h2 class="text-xl font-bold text-shadow-grey-900">{{ $user->name }}</h2>
                    <p class="text-sm text-base-content/60 mt-1">{{ $user->email ?? 'Belum ada email' }}</p>
                    <div class="mt-4">
                        @if ($user->isAdmin())
                            <span class="badge bg-red-100 text-red-700 border-0 font-medium px-3 py-3 rounded-full">Admin
                                PNC</span>
                        @elseif($user->isVendor())
                            <span
                                class="badge bg-orange-100 text-orange-700 border-0 font-medium px-3 py-3 rounded-full">Vendor
                                Kantin</span>
                        @else
                            <span
                                class="badge bg-fern-100 text-fern-700 border-0 font-medium px-3 py-3 rounded-full">Mahasiswa</span>
                        @endif
                    </div>
                    <div class="mt-6 pt-6 border-t border-base-content/10 text-left">
                        <p class="text-xs font-semibold text-base-content/50 mb-1">NIM / NIP / ID
                        </p>
                        <p class="font-medium text-shadow-grey-900">{{ $user->nim ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <div class="md:col-span-2 space-y-6">

                <div class="bg-white border border-base-content/10 rounded-3xl p-6 sm:p-8 shadow-sm">
                    <h3 class="text-lg font-bold text-shadow-grey-900 mb-4">Informasi Profil</h3>

                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data"
                        class="space-y-4">
                        @csrf
                        @method('PATCH')

                        <input type="file" id="avatar-input" name="avatar" class="hidden" accept="image/*"
                            @change="handleAvatarSelect($event)" />

                        <div>
                            <label class="block text-sm font-bold text-base-content mb-1.5">Nama Lengkap</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}"
                                class="input input-bordered w-full rounded-xl border-base-content/25 focus:outline-none focus:border-fern-600 text-sm font-medium {{ $errors->has('name') ? 'input-error' : '' }}"
                                required />
                            @error('name')
                                <p class="text-error text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-base-content mb-1.5">Alamat Email</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                class="input input-bordered w-full rounded-xl border-base-content/25 focus:outline-none focus:border-fern-600 text-sm font-medium {{ $errors->has('email') ? 'input-error' : '' }}"
                                required />
                            @error('email')
                                <p class="text-error text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="pt-2 flex justify-end">
                            <button type="submit"
                                class="btn bg-fern-700 hover:bg-fern-800 text-white border-0 rounded-xl px-8 shadow-sm transition active:scale-95">Simpan
                                Perubahan</button>
                        </div>
                    </form>
                </div>

                <div class="bg-white border border-base-content/10 rounded-3xl p-6 sm:p-8 shadow-sm">
                    <h3 class="text-lg font-bold text-shadow-grey-900 mb-4">Ubah Password</h3>

                    <form action="{{ route('profile.password') }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PATCH')

                        <div>
                            <label class="block text-sm font-bold text-base-content mb-1.5">Password Saat Ini</label>
                            <input type="password" name="current_password"
                                class="input input-bordered w-full rounded-xl border-base-content/25 focus:outline-none focus:border-fern-600 text-sm font-medium {{ $errors->updatePassword->has('current_password') ? 'input-error' : '' }}"
                                required />
                            @if ($errors->updatePassword->has('current_password'))
                                <p class="text-error text-xs mt-1">{{ $errors->updatePassword->first('current_password') }}
                                </p>
                            @endif
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-base-content mb-1.5">Password Baru</label>
                            <input type="password" name="password"
                                class="input input-bordered w-full rounded-xl border-base-content/25 focus:outline-none focus:border-fern-600 text-sm font-medium {{ $errors->updatePassword->has('password') ? 'input-error' : '' }}"
                                required />
                            @if ($errors->updatePassword->has('password'))
                                <p class="text-error text-xs mt-1">{{ $errors->updatePassword->first('password') }}</p>
                            @endif
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-base-content mb-1.5">Konfirmasi Password Baru</label>
                            <input type="password" name="password_confirmation"
                                class="input input-bordered w-full rounded-xl border-base-content/25 focus:outline-none focus:border-fern-600 text-sm font-medium"
                                required />
                        </div>

                        <div class="pt-2 flex justify-end">
                            <button type="submit"
                                class="btn bg-shadow-grey-900 hover:bg-black text-white border-0 rounded-xl px-8 shadow-sm transition active:scale-95">Perbarui
                                Password</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <x-modal id="cropper_modal" title="Sesuaikan Foto Profil"
        subtitle="Seret atau perbesar foto agar pas di dalam lingkaran." :clickOutside="false" :showClose="false">
        <div
            class="w-full aspect-square bg-base-200 overflow-hidden flex items-center justify-center rounded-2xl border border-base-content/10 mt-2">
            <img id="cropper_image" src="" class="max-w-full block" />
        </div>

        <x-slot:footer>
            <button type="button" class="btn btn-ghost rounded-xl text-sm font-bold active:scale-95 transition-all"
                onclick="closeCropperModal(true)">Batal</button>
            <button type="button"
                class="btn bg-fern-700 hover:bg-fern-800 text-white border-0 rounded-xl px-6 shadow-md text-sm font-bold active:scale-95 transition-all"
                onclick="applyCrop()">Terapkan</button>
        </x-slot:footer>
    </x-modal>

    @push('styles')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css" />
    @endpush

    @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js"></script>
        <script>
            // Menyimpan instansi aktif Cropper.js secara global di halaman ini agar dapat dihancurkan saat modal ditutup.
            let cropperInstance = null;

            /**
             * Memproses gambar yang dipilih oleh pengguna sebelum memicu modal pemotongan.
             * Mencegah proses jika format berkas bukan berupa gambar.
             */
            function handleAvatarSelect(event) {
                const file = event.target.files[0];
                if (!file) return;

                if (!file.type.startsWith('image/')) {
                    alert('Format berkas harus berupa gambar!');
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    openCropperModal(e.target.result);
                };
                reader.readAsDataURL(file);
            }

            /**
             * Membuka modal dialog pemotongan avatar dan menginisialisasi Cropper.js.
             * Menggunakan setelan aspek rasio 1:1 statis sehingga pengguna dipaksa menggeser gambar,
             * demi menghasilkan rasio avatar persegi sempurna yang konsisten.
             */
            function openCropperModal(imageSrc) {
                const imageEl = document.getElementById('cropper_image');
                if (!imageEl) return;
                
                imageEl.src = imageSrc;

                const modal = document.getElementById('cropper_modal');
                if (modal) {
                    modal.showModal();
                }

                // Hancurkan instansi cropper lama untuk membebaskan memori sebelum membuat instansi baru.
                if (cropperInstance) {
                    cropperInstance.destroy();
                }

                cropperInstance = new Cropper(imageEl, {
                    aspectRatio: 1,
                    viewMode: 1,
                    dragMode: 'move',
                    autoCropArea: 0.8,
                    restore: false,
                    guides: false,
                    center: false,
                    highlight: false,
                    cropBoxMovable: false,
                    cropBoxResizable: false,
                    toggleDragModeOnDblclick: false,
                    background: false
                });
            }

            /**
             * Menutup modal dialog pemotongan dan menghancurkan instansi Cropper.
             * Mengosongkan file input asli jika dibatalkan (clearInput = true) agar event change tetap terpicu jika file yang sama dipilih lagi.
             */
            function closeCropperModal(clearInput = true) {
                const modal = document.getElementById('cropper_modal');
                if (modal) {
                    modal.close();
                }
                if (cropperInstance) {
                    cropperInstance.destroy();
                    cropperInstance = null;
                }
                if (clearInput) {
                    const avatarInput = document.getElementById('avatar-input');
                    if (avatarInput) {
                        avatarInput.value = '';
                    }
                }
            }

            /**
             * Mengambil hasil potongan gambar dari canvas, mengubahnya menjadi Blob JPG berkualitas tinggi (90%),
             * lalu menyisipkannya ke input file native agar terkirim dalam payload request Laravel.
             */
            function applyCrop() {
                if (!cropperInstance) return;

                // Membatasi resolusi hasil potong pada 400x400 piksel agar seimbang antara ketajaman gambar dan efisiensi penyimpanan server.
                const canvas = cropperInstance.getCroppedCanvas({
                    width: 400,
                    height: 400,
                    imageSmoothingEnabled: true,
                    imageSmoothingQuality: 'high',
                });

                canvas.toBlob(function(blob) {
                    if (!blob) return;

                    // Membungkus data blob menjadi objek File agar kompatibel dengan input file native browser.
                    const croppedFile = new File([blob], 'avatar_cropped.jpg', { type: 'image/jpeg' });

                    const avatarInput = document.getElementById('avatar-input');
                    if (avatarInput) {
                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(croppedFile);
                        avatarInput.files = dataTransfer.files;
                    }

                    // Memicu event kustom untuk didengarkan oleh Alpine.js guna memperbarui pratinjau avatar di UI.
                    const previewUrl = URL.createObjectURL(blob);
                    window.dispatchEvent(new CustomEvent('avatar-cropped', { detail: previewUrl }));

                    // Tutup modal tanpa menghapus data input yang baru saja kita masukkan.
                    closeCropperModal(false);
                }, 'image/jpeg', 0.9);
            }
        </script>
    @endpush
@endsection

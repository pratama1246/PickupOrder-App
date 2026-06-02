@extends('layouts.vendor')

@section('title', 'Tambah Menu - Vendor PNC')

@section('content')

    <div class="max-w-2xl w-full mb-10 lg:mb-0">
        <x-breadcrumb compact :links="[['label' => 'Menu', 'url' => route('vendor.menu.index')], ['label' => 'Tambah Menu']]" />

        <div class="mb-6 sm:mb-8">
            <h1 class="text-2xl sm:text-4xl font-bold text-base-content mb-2">Tambah Menu</h1>
            <p class="text-base-content/70 text-sm sm:text-lg font-medium">Tambahkan hidangan baru untuk ditawarkan kepada
                mahasiswa.</p>
        </div>

        <form action="{{ route('vendor.menu.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf
            <input type="file" name="image" id="real_image_input" class="hidden" />

            <div>
                <label class="block text-sm font-bold text-base-content mb-1.5">Nama Menu</label>
                <input type="text" name="name" value="{{ old('name') }}"
                    placeholder="Masukkan nama menu (contoh: Nasi Rames)" required
                    class="input input-bordered w-full rounded-xl border-base-content/25 focus:outline-none focus:border-fern-600 text-sm font-medium" />
                @error('name')
                    <p class="text-error text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-bold text-base-content mb-1.5">Kategori Menu</label>
                <select name="category"
                    class="select select-bordered w-full rounded-xl border-base-content/25 focus:outline-none focus:border-fern-600 text-sm font-medium">
                    <option value="" disabled selected>Pilih Kategori</option>
                    <option value="Makanan" {{ old('category') == 'Makanan' ? 'selected' : '' }}>Makanan</option>
                    <option value="Minuman" {{ old('category') == 'Minuman' ? 'selected' : '' }}>Minuman</option>
                    <option value="Camilan" {{ old('category') == 'Camilan' ? 'selected' : '' }}>Camilan</option>
                </select>
                @error('category')
                    <p class="text-error text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-bold text-base-content mb-1.5">Deskripsi Menu</label>
                <textarea name="description" rows="3" placeholder="Masukkan deskripsi singkat menu"
                    class="textarea textarea-bordered w-full rounded-xl border-base-content/25 focus:outline-none focus:border-fern-600 text-sm font-medium resize-none">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-error text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-bold text-base-content mb-1.5">Harga (Rp)</label>
                    <input type="number" name="price" value="{{ old('price') }}" placeholder="15000" min="0"
                        required
                        class="input input-bordered w-full rounded-xl border-base-content/25 focus:outline-none focus:border-fern-600 text-sm font-medium" />
                    @error('price')
                        <p class="text-error text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-base-content mb-1.5">Stok</label>
                    <input type="number" name="stock" value="{{ old('stock') }}" placeholder="50" min="0"
                        required
                        class="input input-bordered w-full rounded-xl border-base-content/25 focus:outline-none focus:border-fern-600 text-sm font-medium" />
                    @error('stock')
                        <p class="text-error text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-base-content mb-1.5">Status Ketersediaan</label>
                <select name="is_available"
                    class="select select-bordered w-full rounded-xl border-base-content/25 focus:outline-none focus:border-fern-600 text-sm font-medium">
                    <option value="1" {{ old('is_available', '1') == '1' ? 'selected' : '' }}>Tersedia</option>
                    <option value="0" {{ old('is_available') == '0' ? 'selected' : '' }}>Habis</option>
                </select>
                @error('is_available')
                    <p class="text-error text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- 
              Menggunakan Alpine.js untuk menampilkan pratinjau gambar makanan secara lokal di sisi klien 
              sebelum berkas diunggah dan diproses oleh server Laravel.
            --}}
            <div x-data="{ imageUrl: null }">
                <label class="block text-sm font-bold text-base-content mb-1.5">Foto Menu</label>
                <label
                    class="relative flex flex-col items-center justify-center w-full h-40 border-2 border-dashed border-base-content/25 rounded-2xl cursor-pointer hover:bg-base-content/5 hover:border-fern-600 transition-all group overflow-hidden bg-white shadow-xs">
                    <img x-show="imageUrl" :src="imageUrl" class="absolute inset-0 w-full h-full object-cover"
                        style="display: none;" />
                    <div class="flex flex-col items-center justify-center pb-6 pt-5 px-4 text-center z-10"
                        :class="imageUrl ?
                            'absolute inset-0 bg-black/50 text-white opacity-0 hover:opacity-100 transition-opacity duration-200' :
                            'text-base-content/60'">
                        <svg class="w-8 h-8 mb-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 20 16">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2" />
                        </svg>
                        <p class="mb-1 text-xs"><span class="font-bold">Klik untuk mengunggah</span> atau seret gambar</p>
                        <p class="text-xxs opacity-75">PNG, JPG, WEBP maks. 10MB</p>
                    </div>
                    <input type="file" accept="image/*" class="hidden"
                        @change="if ($event.target.files.length) { imageUrl = URL.createObjectURL($event.target.files[0]); compressAndSetFile($event.target.files[0], 'real_image_input', 1200, 0.75); }" />
                </label>
                @error('image')
                    <p class="text-error text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                    class="btn bg-fern-700 hover:bg-fern-800 text-white border-none rounded-xl font-bold text-sm shadow-sm px-6">
                    Tambah Menu
                </button>
                <a href="{{ route('vendor.menu.index') }}"
                    class="btn bg-base-200 hover:bg-base-300 text-base-content border-none rounded-xl font-bold text-sm shadow-sm px-6">
                    Batal
                </a>
            </div>

        </form>
    </div>

    @push('scripts')
    <script>
        window.compressAndSetFile = function(file, targetInputId, maxWidth, quality) {
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

                    if (width > height) {
                        if (width > maxWidth) {
                            height *= maxWidth / width;
                            width = maxWidth;
                        }
                    } else {
                        if (height > maxWidth) {
                            width *= maxWidth / height;
                            height = maxWidth;
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

                        const realInput = document.getElementById(targetInputId);
                        if (realInput) {
                            realInput.files = dataTransfer.files;
                            console.log('Menu file compressed: ' + (compressedFile.size / 1024).toFixed(2) + ' KB');
                        }
                    }, 'image/jpeg', quality);
                };
            };
        };
    </script>
    @endpush

@endsection

@extends('layouts.vendor')

@section('title', 'Edit Menu - Vendor PNC')

@section('content')

<div class="max-w-2xl bg-vanilla-custard-50 border border-base-content/20 rounded-3xl p-6 sm:p-8 shadow-sm">
    <h1 class="text-2xl font-bold text-base-content mb-6">Edit Menu</h1>

    <form action="{{ route('vendor.menu.update', $menu->id) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm font-bold text-base-content mb-1.5">Nama Menu</label>
            <input type="text" name="name" value="{{ old('name', $menu->name) }}" placeholder="Masukkan nama menu (contoh: Nasi Rames)" required
                   class="input input-bordered w-full rounded-xl border-base-content/25 focus:outline-none focus:border-fern-600 text-sm font-medium" />
            @error('name')
                <p class="text-error text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-bold text-base-content mb-1.5">Deskripsi Menu</label>
            <textarea name="description" rows="3" placeholder="Masukkan deskripsi singkat menu"
                      class="textarea textarea-bordered w-full rounded-xl border-base-content/25 focus:outline-none focus:border-fern-600 text-sm font-medium resize-none">{{ old('description', $menu->description) }}</textarea>
            @error('description')
                <p class="text-error text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-bold text-base-content mb-1.5">Harga (Rp)</label>
                <input type="number" name="price" value="{{ old('price', (int) $menu->price) }}" placeholder="15000" min="0" required
                       class="input input-bordered w-full rounded-xl border-base-content/25 focus:outline-none focus:border-fern-600 text-sm font-medium" />
                @error('price')
                    <p class="text-error text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-bold text-base-content mb-1.5">Stok</label>
                <input type="number" name="stock" value="{{ old('stock', $menu->stock) }}" placeholder="50" min="0" required
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
                <option value="1" {{ old('is_available', $menu->is_available ? '1' : '0') == '1' ? 'selected' : '' }}>Tersedia</option>
                <option value="0" {{ old('is_available', $menu->is_available ? '1' : '0') == '0' ? 'selected' : '' }}>Habis</option>
            </select>
            @error('is_available')
                <p class="text-error text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div x-data="{ imageUrl: '{{ $menu->image ? asset('storage/' . $menu->image) : '' }}' }">
            <label class="block text-sm font-bold text-base-content mb-1.5">Foto Menu</label>
            <div x-show="imageUrl" class="mb-3" style="display: none;">
                <img :src="imageUrl" alt="Preview" class="w-32 h-32 object-cover rounded-xl border border-base-content/20 shadow-sm" />
            </div>
            <input type="file" name="image" accept="image/*"
                   @change="if ($event.target.files.length) imageUrl = URL.createObjectURL($event.target.files[0])"
                   class="file-input file-input-bordered file-input-md w-full rounded-xl border-base-content/25 focus:outline-none focus:border-fern-600 text-sm font-medium" />
            <p class="text-xs text-base-content/50 mt-1">PNG, JPG, WEBP maks. 10MB (Biarkan kosong jika tidak ingin mengubah)</p>
            @error('image')
                <p class="text-error text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit"
                    class="btn bg-fern-700 hover:bg-fern-800 text-white border-none rounded-xl font-bold text-sm shadow-sm px-6">
                Simpan Perubahan
            </button>
            <a href="{{ route('vendor.menu.index') }}"
               class="btn bg-red-500 hover:bg-red-600 text-white border-none rounded-xl font-bold text-sm shadow-sm px-6">
                Batal
            </a>
        </div>

    </form>
</div>

@endsection

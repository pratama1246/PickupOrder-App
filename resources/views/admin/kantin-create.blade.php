@extends('layouts.admin')

@section('title', 'Tambah Kantin - Admin PNC')

@section('content')

<div class="max-w-2xl bg-vanilla-custard-50 border border-base-content/20 rounded-3xl p-6 sm:p-8 shadow-sm">
    <h1 class="text-2xl font-bold text-base-content mb-6">Tambah Kantin</h1>

    <form action="{{ route('admin.kantin.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf

        <div>
            <label class="block text-sm font-bold text-base-content mb-1.5">Nama Kantin</label>
            <input type="text" name="name" value="{{ old('name') }}" placeholder="Masukkan nama kantin" required
                   class="input input-bordered w-full rounded-xl border-base-content/25 focus:outline-none focus:border-fern-600 text-sm font-medium" />
            @error('name')
                <p class="text-error text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-bold text-base-content mb-1.5">Pengelola (Vendor)</label>
            <select name="user_id" required
                    class="select select-bordered w-full rounded-xl border-base-content/25 focus:outline-none focus:border-fern-600 text-sm font-medium">
                <option value="" disabled selected>Pilih vendor pengelola</option>
                @foreach ($vendors as $vendor)
                    <option value="{{ $vendor->id }}" {{ old('user_id') == $vendor->id ? 'selected' : '' }}>
                        {{ $vendor->name }} (NIM/NIP: {{ $vendor->nim }})
                    </option>
                @endforeach
            </select>
            @error('user_id')
                <p class="text-error text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-bold text-base-content mb-1.5">Deskripsi Kantin</label>
            <textarea name="description" rows="4" placeholder="Masukkan deskripsi atau info kantin"
                      class="textarea textarea-bordered w-full rounded-xl border-base-content/25 focus:outline-none focus:border-fern-600 text-sm font-medium resize-none">{{ old('description') }}</textarea>
            @error('description')
                <p class="text-error text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-bold text-base-content mb-1.5">Status Buka/Tutup</label>
            <select name="is_open"
                    class="select select-bordered w-full rounded-xl border-base-content/25 focus:outline-none focus:border-fern-600 text-sm font-medium">
                <option value="1" {{ old('is_open', '1') == '1' ? 'selected' : '' }}>Buka</option>
                <option value="0" {{ old('is_open') == '0' ? 'selected' : '' }}>Tutup</option>
            </select>
            @error('is_open')
                <p class="text-error text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div x-data="{ imageUrl: null }">
            <label class="block text-sm font-bold text-base-content mb-1.5">Gambar Kantin</label>
            <div x-show="imageUrl" class="mb-3" style="display: none;">
                <img :src="imageUrl" alt="Preview" class="w-32 h-32 object-cover rounded-xl border border-base-content/20 shadow-sm" />
            </div>
            <input type="file" name="image" accept="image/*"
                   @change="if ($event.target.files.length) imageUrl = URL.createObjectURL($event.target.files[0])"
                   class="file-input file-input-bordered file-input-md w-full rounded-xl border-base-content/25 focus:outline-none focus:border-fern-600 text-sm font-medium" />
            <p class="text-xs text-base-content/50 mt-1">PNG, JPG, WEBP maks. 2MB</p>
            @error('image')
                <p class="text-error text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit"
                    class="btn bg-fern-700 hover:bg-fern-800 text-white border-none rounded-xl font-bold text-sm shadow-sm px-6">
                Tambah Kantin
            </button>
            <a href="{{ route('admin.kantin.index') }}"
               class="btn bg-red-500 hover:bg-red-600 text-white border-none rounded-xl font-bold text-sm shadow-sm px-6">
                Batal
            </a>
        </div>

    </form>
</div>

@endsection

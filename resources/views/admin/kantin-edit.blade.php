@extends('layouts.admin')

@section('title', 'Edit Kantin - Admin PNC')

@section('content')

<div class="max-w-2xl bg-vanilla-custard-50 border border-base-content/20 rounded-3xl p-6 sm:p-8 mb-10 lg:mb-0 shadow-sm">
    <x-breadcrumb
        compact
        :links="[
            ['label' => 'Kantin', 'url' => route('admin.kantin.index')],
            ['label' => 'Edit Kantin']
        ]"
    />

    <h1 class="text-2xl font-bold text-base-content mb-6">Edit Kantin</h1>

    <form action="{{ route('admin.kantin.update', $canteen->id) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm font-bold text-base-content mb-1.5">Nama Kantin</label>
            <input type="text" name="name" value="{{ old('name', $canteen->name) }}" placeholder="Masukkan nama kantin" required
                   class="input input-bordered w-full rounded-xl border-base-content/25 focus:outline-none focus:border-fern-600 text-sm font-medium" />
            @error('name')
                <p class="text-error text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-bold text-base-content mb-1.5">Pengelola (Vendor)</label>
            <input type="text" value="{{ $canteen->owner->name }} ({{ $canteen->owner->email }})" disabled
                   class="input input-bordered w-full rounded-xl border-base-content/25 bg-base-200 text-sm font-medium" />
            <p class="text-xs text-base-content/50 mt-1">Pengelola kantin tidak dapat diubah.</p>
        </div>

        <div>
            <label class="block text-sm font-bold text-base-content mb-1.5">Deskripsi Kantin</label>
            <textarea name="description" rows="4" placeholder="Masukkan deskripsi atau info kantin"
                      class="textarea textarea-bordered w-full rounded-xl border-base-content/25 focus:outline-none focus:border-fern-600 text-sm font-medium resize-none">{{ old('description', $canteen->description) }}</textarea>
            @error('description')
                <p class="text-error text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-bold text-base-content mb-1.5">Status Buka/Tutup</label>
            <select name="is_open"
                    class="select select-bordered w-full rounded-xl border-base-content/25 focus:outline-none focus:border-fern-600 text-sm font-medium">
                <option value="1" {{ old('is_open', $canteen->is_open ? '1' : '0') == '1' ? 'selected' : '' }}>Buka</option>
                <option value="0" {{ old('is_open', $canteen->is_open ? '1' : '0') == '0' ? 'selected' : '' }}>Tutup</option>
            </select>
            @error('is_open')
                <p class="text-error text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        @php
            $currentImageUrl = '';
            if ($canteen->image) {
                $currentImageUrl = str_starts_with($canteen->image, 'assets/') ? asset($canteen->image) : asset('storage/' . $canteen->image);
            }
        @endphp
        <div x-data="{ imageUrl: '{{ $currentImageUrl }}' }">
            <label class="block text-sm font-bold text-base-content mb-1.5">Gambar Kantin</label>
            <label class="relative flex flex-col items-center justify-center w-full h-40 border-2 border-dashed border-base-content/25 rounded-2xl cursor-pointer hover:bg-base-content/5 hover:border-fern-600 transition-all group overflow-hidden bg-white shadow-xs">
                <img x-show="imageUrl" :src="imageUrl" class="absolute inset-0 w-full h-full object-cover" style="display: none;" />
                <div class="flex flex-col items-center justify-center pb-6 pt-5 px-4 text-center z-10" :class="imageUrl ? 'absolute inset-0 bg-black/50 text-white opacity-0 hover:opacity-100 transition-opacity duration-200' : 'text-base-content/60'">
                    <svg class="w-8 h-8 mb-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                    </svg>
                    <p class="mb-1 text-xs"><span class="font-bold">Klik untuk mengubah</span> atau seret gambar</p>
                    <p class="text-xxs opacity-75">PNG, JPG, WEBP maks. 2MB</p>
                </div>
                <input type="file" name="image" accept="image/*" class="hidden"
                       @change="if ($event.target.files.length) imageUrl = URL.createObjectURL($event.target.files[0])" />
            </label>
            @error('image')
                <p class="text-error text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit"
                    class="btn bg-fern-700 hover:bg-fern-800 text-white border-none rounded-xl font-bold text-sm shadow-sm px-6">
                Simpan Perubahan
            </button>
            <a href="{{ route('admin.kantin.index') }}"
               class="btn bg-red-500 hover:bg-red-600 text-white border-none rounded-xl font-bold text-sm shadow-sm px-6">
                Batal
            </a>
        </div>

    </form>
</div>

@endsection

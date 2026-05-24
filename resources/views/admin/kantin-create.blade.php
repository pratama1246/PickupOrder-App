@extends('layouts.admin')

@section('title', 'Tambah Kantin - Admin PNC')

@section('content')

<div class="max-w-2xl bg-vanilla-custard-50 border border-base-content/20 rounded-3xl p-6 sm:p-8 mb-10 lg:mb-0 shadow-sm">
    <x-breadcrumb
        compact
        :links="[
            ['label' => 'Kantin', 'url' => route('admin.kantin.index')],
            ['label' => 'Tambah Kantin']
        ]"
    />

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

        <div class="alert alert-info bg-blue-50 text-blue-800 border-none rounded-xl text-sm font-medium">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span>Akun pengelola akan dibuat secara otomatis berdasarkan Nama Kantin. Email dan Password akan ditampilkan setelah berhasil ditambahkan.</span>
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

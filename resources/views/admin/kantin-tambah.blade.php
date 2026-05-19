@extends('layouts.admin')

@section('title', 'Tambah Kantin - Admin PNC')

@section('content')

<div class="max-w-2xl">
    <h1 class="text-2xl font-bold text-base-content mb-6">Tambah Kantin</h1>

    <form action="/admin/kantin" method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf

        <div>
            <label class="block text-sm font-bold text-base-content mb-1.5">Nama Kantin</label>
            <input type="text" name="name" placeholder="Masukkan nama kantin"
                   class="input input-bordered w-full rounded-xl border-base-content/25 focus:outline-none focus:border-fern-600 text-sm font-medium" />
        </div>

        <div>
            <label class="block text-sm font-bold text-base-content mb-1.5">Nama Pengelola</label>
            <input type="text" name="pengelola" placeholder="Masukkan nama pengelola"
                   class="input input-bordered w-full rounded-xl border-base-content/25 focus:outline-none focus:border-fern-600 text-sm font-medium" />
        </div>

        <div>
            <label class="block text-sm font-bold text-base-content mb-1.5">No. HP</label>
            <input type="tel" name="phone" placeholder="Contoh: 08123456789"
                   class="input input-bordered w-full rounded-xl border-base-content/25 focus:outline-none focus:border-fern-600 text-sm font-medium" />
        </div>

        <div>
            <label class="block text-sm font-bold text-base-content mb-1.5">
                Email <span class="text-base-content/40 font-medium">(Opsional)</span>
            </label>
            <input type="email" name="email" placeholder="Masukkan email pengelola"
                   class="input input-bordered w-full rounded-xl border-base-content/25 focus:outline-none focus:border-fern-600 text-sm font-medium" />
        </div>

        <div>
            <label class="block text-sm font-bold text-base-content mb-1.5">Lokasi</label>
            <textarea name="location" rows="3" placeholder="Masukkan lokasi kantin"
                      class="textarea textarea-bordered w-full rounded-xl border-base-content/25 focus:outline-none focus:border-fern-600 text-sm font-medium resize-none"></textarea>
        </div>

        <div>
            <label class="block text-sm font-bold text-base-content mb-1.5">Gambar Kantin</label>
            <label class="flex flex-col items-center justify-center w-full h-36 border-2 border-dashed border-base-content/25 rounded-xl cursor-pointer bg-base-200/50 hover:bg-base-200 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-base-content/30 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span class="text-sm font-bold text-base-content/40">Upload Gambar Kantin</span>
                <span class="text-xs text-base-content/30 mt-1">PNG, JPG, WEBP maks. 2MB</span>
                <input type="file" name="image" accept="image/*" class="hidden" />
            </label>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit"
                    class="btn bg-fern-700 hover:bg-fern-800 text-white border-none rounded-xl font-bold text-sm shadow-sm px-6">
                Tambah Kantin
            </button>
            <a href="/admin/kantin"
               class="btn bg-red-500 hover:bg-red-600 text-white border-none rounded-xl font-bold text-sm shadow-sm px-6">
                Batal
            </a>
        </div>

    </form>
</div>

@endsection

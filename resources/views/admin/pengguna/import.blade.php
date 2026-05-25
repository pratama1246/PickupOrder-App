@extends('layouts.admin')

@section('title', 'Import Pengguna - Admin PNC')

@section('content')

    <div
        class="max-w-2xl bg-vanilla-custard-50 border border-base-content/20 rounded-3xl p-6 sm:p-8 mb-10 lg:mb-0 shadow-sm">
        <x-breadcrumb compact :links="[['label' => 'Pengguna', 'url' => route('admin.pengguna.index')], ['label' => 'Import Pengguna']]" />

        <h1 class="text-2xl font-bold text-base-content mb-6">Import Pengguna</h1>

        <!-- Download Template Section -->
        <div
            class="flex flex-col sm:flex-row sm:items-center justify-between bg-white border border-base-content/15 p-4 rounded-xl mb-5 shadow-xs gap-3">
            <div>
                <h3 class="text-sm font-bold text-base-content">Template Pengguna CSV</h3>
                <p class="text-xs text-base-content/65">Unduh template CSV resmi untuk format pengisian data yang benar.</p>
            </div>
            <a href="{{ route('admin.pengguna.import.template') }}"
                class="btn btn-sm bg-base-200 hover:bg-base-300 text-base-content border-none rounded-lg font-bold flex items-center gap-1.5 shadow-xs shrink-0 w-fit">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                <span>Unduh Template</span>
            </a>
        </div>

        <!-- Instructions / Document Style -->
        <div class="bg-white border border-base-content/15 rounded-2xl p-5 mb-6 shadow-xs text-sm">
            <div class="flex items-center gap-2 pb-3 mb-4 border-b border-base-content/10">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor" class="w-5 h-5 text-fern-700">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                </svg>
                <h2 class="font-bold text-base-content text-sm">Panduan Format & Ketentuan CSV</h2>
            </div>

            <div class="space-y-4">
                <div>
                    <h4 class="text-xxs font-bold uppercase text-base-content/50 mb-2">Struktur Kolom</h4>
                    <div class="grid grid-cols-3 gap-2 bg-base-100 p-2 rounded-xl border border-base-content/10 text-xs">
                        <div class="bg-white p-2 rounded-lg border border-base-content/5 text-center shadow-3xs">
                            <span class="font-bold text-fern-700">nama</span>
                            <span class="block text-xxs text-base-content/50 mt-0.5">Nama Lengkap</span>
                        </div>
                        <div class="bg-white p-2 rounded-lg border border-base-content/5 text-center shadow-3xs">
                            <span class="font-bold text-fern-700">nim</span>
                            <span class="block text-xxs text-base-content/50 mt-0.5">NIM Mahasiswa</span>
                        </div>
                        <div class="bg-white p-2 rounded-lg border border-base-content/5 text-center shadow-3xs">
                            <span class="font-bold text-fern-700">email</span>
                            <span class="block text-xxs text-base-content/50 mt-0.5">Alamat Email</span>
                        </div>
                    </div>
                </div>

                <div class="space-y-2.5 text-xs text-base-content/85 font-medium">
                    <div class="flex gap-2">
                        <span
                            class="flex-shrink-0 flex items-center justify-center w-5 h-5 rounded-full bg-fern-50 text-fern-700 font-bold text-xxs border border-fern-200">1</span>
                        <p class="leading-relaxed">File harus disimpan dalam format <strong>.csv</strong> menggunakan
                            pemisah koma (`,`).</p>
                    </div>
                    <div class="flex gap-2">
                        <span
                            class="flex-shrink-0 flex items-center justify-center w-5 h-5 rounded-full bg-fern-50 text-fern-700 font-bold text-xxs border border-fern-200">2</span>
                        <p class="leading-relaxed">Baris pertama pada header wajib bertuliskan <span
                                class="bg-base-200 px-1.5 py-0.5 rounded text-red-600 font-bold text-xs">nama</span>, <span
                                class="bg-base-200 px-1.5 py-0.5 rounded text-red-600 font-bold text-xs">nim</span>, dan
                            <span class="bg-base-200 px-1.5 py-0.5 rounded text-red-600 font-bold text-xs">email</span>.</p>
                    </div>
                </div>

                <div class="mt-4 pt-4 border-t border-base-content/10 space-y-2.5 text-xs">
                    <div class="flex gap-2.5 items-start text-base-content/75">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5"
                            stroke="currentColor" class="w-4 h-4 text-amber-600 shrink-0 mt-0.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                        </svg>
                        <p><strong>Kata Sandi Otomatis</strong>: Dibuat dengan format <span
                                class="bg-base-200 px-1.5 py-0.5 rounded text-slate-800 font-bold text-xs">Pnc_</span> +
                            <strong>NIM</strong> (contoh: <span
                                class="bg-base-200 px-1.5 py-0.5 rounded text-slate-800 font-bold text-xs">Pnc_2203010100</span>).
                        </p>
                    </div>
                    <div class="flex gap-2.5 items-start text-base-content/75">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5"
                            stroke="currentColor" class="w-4 h-4 text-fern-600 shrink-0 mt-0.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        <p><strong>Akses & Peran</strong>: Pengguna hasil impor otomatis terdaftar sebagai
                            <strong>Mahasiswa</strong> dan wajib mengganti kata sandi saat pertama kali login.</p>
                    </div>
                </div>
            </div>
        </div>

        @if (session('error_list') && count(session('error_list')) > 0 && $errors->has('file'))
            <div class="alert alert-error rounded-xl mb-5 text-sm shadow-xs flex flex-col items-start gap-2">
                <span class="font-bold text-red-800">Daftar kesalahan baris data:</span>
                <ul class="list-disc pl-5 text-xs text-red-700 space-y-1 font-medium max-h-40 overflow-y-auto w-full">
                    @foreach (session('error_list') as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Import Form -->
        <form action="{{ route('admin.pengguna.import') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div x-data="{ fileName: '' }">
                <label class="block text-sm font-bold text-base-content mb-1.5">Pilih Berkas CSV</label>
                <label
                    class="relative flex flex-col items-center justify-center w-full h-36 border-2 border-dashed border-base-content/25 rounded-2xl cursor-pointer hover:bg-base-content/5 hover:border-fern-600 transition-all group bg-white shadow-xs text-base-content/60">
                    <div class="flex flex-col items-center justify-center pb-6 pt-5 text-center px-4">
                        <svg class="w-8 h-8 mb-2 text-base-content/50 group-hover:text-fern-700 transition-colors"
                            fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        <p class="mb-1 text-xs">
                            <span class="font-bold text-base-content/80"
                                x-text="fileName ? 'Berkas terpilih:' : 'Klik untuk mengunggah'"></span>
                            <span x-text="fileName ? '' : ' atau seret berkas'"></span>
                        </p>
                        <p class="text-xs font-bold text-fern-700 mt-1" x-show="fileName" x-text="fileName"
                            style="display: none;"></p>
                        <p class="text-xxs opacity-75 mt-0.5" x-show="!fileName">CSV maks. 10MB</p>
                    </div>
                    <input type="file" name="file" accept=".csv,text/csv" required class="hidden"
                        @change="fileName = $event.target.files.length ? $event.target.files[0].name : ''" />
                </label>
                @error('file')
                    <p class="text-error text-xs mt-1.5 font-bold">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                    class="btn bg-fern-700 hover:bg-fern-800 text-white border-none rounded-xl font-bold text-sm shadow-sm px-6 active:scale-95 transition-all">
                    Mulai Impor
                </button>
                <a href="{{ route('admin.pengguna.index') }}"
                    class="btn bg-red-500 hover:bg-red-600 text-white border-none rounded-xl font-bold text-sm shadow-sm px-6 active:scale-95 transition-all">
                    Batal
                </a>
            </div>

        </form>
    </div>

@endsection

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

        <!-- Instructions / Alert -->
        <div
            class="bg-blue-50/80 border border-blue-100 p-4 rounded-xl text-xs text-blue-900 space-y-1.5 font-medium mb-5 shadow-xs">
            <div class="flex items-center gap-1.5 font-bold mb-1 text-sm text-blue-950">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    class="stroke-current shrink-0 w-4.5 h-4.5">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>Panduan Format CSV</span>
            </div>
            <p>1. File harus berformat <strong>.csv</strong> dan menggunakan koma (`,`) sebagai pemisah kolom.</p>
            <p>2. Baris pertama wajib berisi header kolom: <strong>nama</strong>, <strong>nim</strong>,
                <strong>email</strong> (dalam huruf kecil semua).</p>
            <p>3. Password otomatis dibuat dengan format: <strong>Pnc_</strong> + <strong>NIM/NIP</strong> (contoh:
                <code>Pnc_2203010100</code>).</p>
            <p>4. Pengguna hasil impor otomatis mendapatkan role <strong>mahasiswa</strong> (pembeli umum) dan diwajibkan
                mengganti password saat pertama kali login.</p>
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

            <div>
                <label class="block text-sm font-bold text-base-content mb-1.5">Pilih Berkas CSV</label>
                <input type="file" name="file" accept=".csv,text/csv" required
                    class="file-input file-input-bordered file-input-md w-full rounded-xl border-base-content/25 focus:outline-none focus:border-fern-600 text-sm font-medium" />
                <p class="text-xs text-base-content/50 mt-1">Hanya mendukung tipe berkas .csv dengan ukuran maksimal 2MB.
                </p>
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

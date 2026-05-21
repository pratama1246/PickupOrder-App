@extends('layouts.admin')

@section('title', 'Tambah Pengguna - Admin PNC')

@section('content')

<div class="max-w-2xl bg-vanilla-custard-50 border border-base-content/20 rounded-3xl p-6 sm:p-8 shadow-sm">
    <h1 class="text-2xl font-bold text-base-content mb-6">Tambah Pengguna</h1>

    <form action="{{ route('admin.pengguna.store') }}" method="POST" class="space-y-5" x-data="{ role: '{{ old('role', '') }}' }">
        @csrf

        <div>
            <label class="block text-sm font-bold text-base-content mb-1.5">Nama Lengkap</label>
            <input type="text" name="name" value="{{ old('name') }}" placeholder="Masukkan nama lengkap" required
                   class="input input-bordered w-full rounded-xl border-base-content/25 focus:outline-none focus:border-fern-600 text-sm font-medium" />
            @error('name')
                <p class="text-error text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-1 gap-4" :class="role !== 'vendor' ? 'sm:grid-cols-2' : ''">
            <div x-show="role !== 'vendor'" x-transition class="transition-all duration-300">
                <label class="block text-sm font-bold text-base-content mb-1.5">NIM / NIP</label>
                <input type="text" name="nim" value="{{ old('nim') }}" :disabled="role === 'vendor'" :required="role !== 'vendor'" placeholder="Masukkan NIM atau NIP"
                       class="input input-bordered w-full rounded-xl border-base-content/25 focus:outline-none focus:border-fern-600 text-sm font-medium" />
                @error('nim')
                    <p class="text-error text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-bold text-base-content mb-1.5">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="Contoh: user@pnc.ac.id" required
                       class="input input-bordered w-full rounded-xl border-base-content/25 focus:outline-none focus:border-fern-600 text-sm font-medium" />
                @error('email')
                    <p class="text-error text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-bold text-base-content mb-1.5">Peran (Role)</label>
                <select name="role" x-model="role" required
                        class="select select-bordered w-full rounded-xl border-base-content/25 focus:outline-none focus:border-fern-600 text-sm font-medium">
                    <option value="" disabled selected>Pilih peran</option>
                    <option value="mahasiswa" {{ old('role') == 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                    <option value="vendor" {{ old('role') == 'vendor' ? 'selected' : '' }}>Vendor (Pemilik Kantin)</option>
                </select>
                @error('role')
                    <p class="text-error text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-bold text-base-content mb-1.5">Status Akun</label>
                <select name="is_first_login" required
                        class="select select-bordered w-full rounded-xl border-base-content/25 focus:outline-none focus:border-fern-600 text-sm font-medium">
                    <option value="0" {{ old('is_first_login') === '0' ? 'selected' : '' }}>Aktif</option>
                    <option value="1" {{ old('is_first_login', '1') === '1' ? 'selected' : '' }}>Nonaktif (Belum Aktif)</option>
                </select>
                @error('is_first_login')
                    <p class="text-error text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4" x-data="{ showPw: false, showConfirmPw: false }">
            <div>
                <label class="block text-sm font-bold text-base-content mb-1.5">Password</label>
                <div class="relative">
                    <input :type="showPw ? 'text' : 'password'" name="password" placeholder="Masukkan password minimal 8 karakter" required
                           class="input input-bordered w-full rounded-xl border-base-content/25 focus:outline-none focus:border-fern-600 text-sm font-medium pr-11" />
                    <button type="button"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-base-content/40 hover:text-base-content/70 transition-colors"
                            x-on:click="showPw = !showPw">
                        <svg x-show="!showPw" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <svg x-show="showPw" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="display:none">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 4.411m0 0L21 21"/>
                        </svg>
                    </button>
                </div>
                @error('password')
                    <p class="text-error text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-bold text-base-content mb-1.5">Konfirmasi Password</label>
                <div class="relative">
                    <input :type="showConfirmPw ? 'text' : 'password'" name="password_confirmation" placeholder="Ulangi password" required
                           class="input input-bordered w-full rounded-xl border-base-content/25 focus:outline-none focus:border-fern-600 text-sm font-medium pr-11" />
                    <button type="button"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-base-content/40 hover:text-base-content/70 transition-colors"
                            x-on:click="showConfirmPw = !showConfirmPw">
                        <svg x-show="!showConfirmPw" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <svg x-show="showConfirmPw" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="display:none">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 4.411m0 0L21 21"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit"
                    class="btn bg-fern-700 hover:bg-fern-800 text-white border-none rounded-xl font-bold text-sm shadow-sm px-6">
                Tambah Pengguna
            </button>
            <a href="{{ route('admin.pengguna.index') }}"
               class="btn bg-red-500 hover:bg-red-600 text-white border-none rounded-xl font-bold text-sm shadow-sm px-6">
                Batal
            </a>
        </div>

    </form>
</div>

@endsection

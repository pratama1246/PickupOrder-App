@extends('layouts.auth')

@section('title', 'Ganti Password - PNC')

@section('hero-title', 'Satu Langkah Lagi Buat Mulai Pesan Makanan!')

@section('form')

    {{-- Heading --}}
    <div class="mb-7">
        <h2 class="text-2xl sm:text-3xl font-extrabold text-base-content mb-1">Buat Password Baru</h2>
        <p class="text-sm text-base-content/60 font-medium">Ini login pertamamu, yuk ganti password default dulu biar aman.</p>
    </div>

    {{-- Error Alert --}}
    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 text-sm font-medium rounded-xl px-4 py-3 mb-5">
            {{ $errors->first() }}
        </div>
    @endif

    <form action="{{ route('password.change') }}" method="POST" class="space-y-5">
        @csrf

        {{-- Password Baru --}}
        <div x-data="{ show: false }">
            <label class="block text-sm font-bold text-base-content mb-1.5">Password Baru</label>
            <div class="relative">
                <input
                    :type="show ? 'text' : 'password'"
                    name="password"
                    placeholder="Minimal 8 karakter"
                    autocomplete="new-password"
                    required
                    class="input input-bordered w-full rounded-xl bg-white border-base-content/20 focus:outline-none focus:border-fern-600 text-sm font-medium placeholder:text-base-content/35 pr-11
                           @error('password') border-red-400 @enderror"
                />
                <button type="button"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-base-content/40 hover:text-base-content/70 transition-colors"
                        x-on:click="show = !show">
                    <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <svg x-show="show" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="display:none">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 4.411m0 0L21 21"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Konfirmasi Password --}}
        <div x-data="{ show: false }">
            <label class="block text-sm font-bold text-base-content mb-1.5">Konfirmasi Password</label>
            <div class="relative">
                <input
                    :type="show ? 'text' : 'password'"
                    name="password_confirmation"
                    placeholder="Ulangi password baru"
                    autocomplete="new-password"
                    required
                    class="input input-bordered w-full rounded-xl bg-white border-base-content/20 focus:outline-none focus:border-fern-600 text-sm font-medium placeholder:text-base-content/35 pr-11"
                />
                <button type="button"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-base-content/40 hover:text-base-content/70 transition-colors"
                        x-on:click="show = !show">
                    <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <svg x-show="show" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="display:none">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 4.411m0 0L21 21"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Submit --}}
        <button type="submit"
                class="btn bg-fern-700 hover:bg-fern-800 text-white border-none w-full rounded-xl font-bold text-sm shadow-md active:scale-95 transition-all">
            Simpan Password
        </button>

    </form>

@endsection

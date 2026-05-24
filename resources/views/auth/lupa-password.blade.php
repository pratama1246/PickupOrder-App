@extends('layouts.auth')

@section('title', 'Lupa Password - PNC')

@section('hero-title', 'Tenang, Password Bisa Direset Kok!')

@section('form')

    {{-- Heading --}}
    <div class="mb-7">
        <h2 class="text-2xl sm:text-3xl font-extrabold text-base-content mb-1">Lupa Password?</h2>
        <p class="text-sm text-base-content/60 font-medium">Masukkan NIM/NIP kamu, kami cek dulu ada email-nya atau tidak.</p>
    </div>

    {{-- Error Alert --}}
    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 text-sm font-medium rounded-xl px-4 py-3 mb-5">
            {{ $errors->first() }}
        </div>
    @endif

    {{-- Success --}}
    @if(session('status') === 'reset-sent')
        <div class="bg-fern-50 border border-fern-200 rounded-xl px-4 py-4 mb-5">
            <p class="text-sm font-bold text-fern-700 mb-1">Link reset dikirim!</p>
            <p class="text-xs text-fern-600 font-medium">Cek email kamu dan klik link reset password yang kami kirim.</p>
        </div>
    @endif

    {{-- Gagal hubungi admin --}}
    @if(session('status') === 'no-email')
        <div class="bg-amber-50 border border-amber-200 rounded-xl px-4 py-4 mb-5">
            <p class="text-sm font-bold text-amber-700 mb-1">Akun kamu belum punya email terdaftar.</p>
            <p class="text-xs text-amber-600 font-medium">Silakan hubungi admin atau petugas kampus untuk reset password secara manual.</p>
        </div>
    @endif

    <form action="{{ route('password.request.submit') }}" method="POST" class="space-y-5">
        @csrf

        {{-- Field Email/NIM/NIP --}}
        <div>
            <label class="block text-sm font-bold text-base-content mb-1.5">Email / NIM / NIP</label>
            <input
                type="text"
                name="identifier"
                value="{{ old('identifier') }}"
                placeholder="Masukkan Email atau NIM/NIP Anda"
                required
                class="input input-bordered w-full rounded-xl bg-white border-base-content/20 focus:outline-none focus:border-fern-600 text-sm font-medium placeholder:text-base-content/35
                       @error('identifier') border-red-400 @enderror"
            />
        </div>

        {{-- Submit --}}
        <button type="submit"
                class="btn bg-fern-700 hover:bg-fern-800 text-white border-none w-full rounded-xl font-bold text-sm shadow-md active:scale-95 transition-all">
            Kirim Permintaan Reset
        </button>

        {{-- Kembali Login --}}
        <div class="text-center">
            <a href="{{ route('login') }}" class="text-sm font-bold text-base-content/50 hover:text-fern-700 transition-colors">
                Kembali ke Login
            </a>
        </div>

    </form>

@endsection

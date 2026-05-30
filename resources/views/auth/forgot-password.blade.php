@extends('layouts.auth')

@section('title', 'Lupa Password - PNC')

@section('hero-title', 'Tenang, Password Bisa Direset Kok!')

{{-- 
  Halaman Lupa Password (Password Recovery):
  - Memperluas layout 'layouts.auth' untuk memposisikan komponen formulir di atas backdrop gradien mesh dinamis.
  - Mendukung pengiriman 'identifier' berupa Email, NIM, atau NIP untuk mencocokkan identitas akun di basis data.
  - Menangani status tanggapan sesi (session status) secara multi-kondisi:
    - 'reset-sent': Menampilkan konfirmasi keberhasilan pengiriman tautan reset ke email pengguna.
    - 'no-email': Memberi tahu pengguna bahwa akun mereka belum dikaitkan dengan alamat email mana pun, 
      sehingga harus menghubungi administrator kampus secara manual.
--}}

@section('form')
    <div class="mb-7">
        <h2 class="text-2xl sm:text-3xl font-extrabold text-base-content mb-1">Lupa Password?</h2>
        <p class="text-sm text-base-content/60 font-medium">Masukkan NIM/Email kamu, kami akan kirim notifikasi reset ke email terdaftar.
        </p>
    </div>

    @if (session('status') === 'reset-sent')
        <div class="bg-fern-50 border border-fern-200 rounded-xl px-4 py-4 mb-5">
            <p class="text-sm font-bold text-fern-700 mb-1">Permintaan reset diproses!</p>
            <p class="text-xs text-fern-600 font-medium">Jika NIM/Email yang dimasukkan terdaftar di sistem, kamu akan menerima instruksi reset password. Silakan hubungi admin kampus jika perlu bantuan lebih lanjut.</p>
        </div>
    @endif

    <form action="{{ route('password.request.submit') }}" method="POST" class="space-y-5">
        @csrf

        <div>
            <label class="block text-sm font-bold text-base-content mb-1.5">Email / NIM / NIP</label>
            <input type="text" name="identifier" value="{{ old('identifier') }}"
                placeholder="Masukkan Email atau NIM/NIP Anda" required
                class="input input-bordered w-full rounded-xl bg-white border-base-content/20 focus:outline-none focus:border-fern-600 text-sm font-medium placeholder:text-base-content/35
                       @error('identifier') border-red-400 @enderror" />
        </div>

        <button type="submit"
            class="btn bg-fern-700 hover:bg-fern-800 text-white border-none w-full rounded-xl font-bold text-sm shadow-md active:scale-95 transition-all">
            Kirim Permintaan Reset
        </button>

        <div class="text-center">
            <a href="{{ route('login') }}"
                class="text-sm font-bold text-base-content/50 hover:text-fern-700 transition-colors">
                Kembali ke Login
            </a>
        </div>

    </form>

@endsection

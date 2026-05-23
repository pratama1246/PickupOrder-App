@php
    $layout = 'layouts.app';
    if(auth()->user()->isAdmin()) {
        $layout = 'layouts.admin';
    } elseif(auth()->user()->isVendor()) {
        $layout = 'layouts.vendor';
    }
@endphp

@extends($layout)

@section('title', 'Profil & Pengaturan - PNC')

@section('content')
<div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-shadow-grey-900">Profil & Pengaturan</h1>
        <p class="text-sm text-base-content/70 mt-1">Kelola informasi pribadi dan pengaturan keamanan akun Anda.</p>
    </div>

    @if (session('status') === 'profile-updated')
        <div role="alert" class="alert alert-success bg-fern-50 text-fern-700 border-fern-200 mb-6 rounded-2xl shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <span>Informasi profil berhasil diperbarui!</span>
        </div>
    @endif

    @if (session('status') === 'password-updated')
        <div role="alert" class="alert alert-success bg-fern-50 text-fern-700 border-fern-200 mb-6 rounded-2xl shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <span>Password berhasil diperbarui!</span>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <!-- Sidebar Profile Card -->
        <div class="md:col-span-1 space-y-6">
            <div class="bg-white border border-base-content/10 rounded-3xl p-6 shadow-sm text-center">
                @if($user->avatar)
                <div class="avatar mb-4">
                    <div class="w-24 rounded-full ring ring-fern-50 ring-offset-base-100 ring-offset-2">
                        <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar" class="object-cover" />
                    </div>
                </div>
                @else
                <div class="avatar placeholder mb-4">
                    <div class="bg-fern-100 text-fern-700 rounded-full w-24 ring ring-fern-50 ring-offset-base-100 ring-offset-2 flex items-center justify-center">
                        <span class="text-4xl font-bold uppercase">{{ substr($user->name, 0, 1) }}</span>
                    </div>
                </div>
                @endif
                <h2 class="text-xl font-bold text-shadow-grey-900">{{ $user->name }}</h2>
                <p class="text-sm text-base-content/60 mt-1">{{ $user->email ?? 'Belum ada email' }}</p>
                <div class="mt-4">
                    @if($user->isAdmin())
                        <span class="badge bg-red-100 text-red-700 border-0 font-medium px-3 py-3 rounded-full">Admin PNC</span>
                    @elseif($user->isVendor())
                        <span class="badge bg-orange-100 text-orange-700 border-0 font-medium px-3 py-3 rounded-full">Vendor Kantin</span>
                    @else
                        <span class="badge bg-fern-100 text-fern-700 border-0 font-medium px-3 py-3 rounded-full">Mahasiswa</span>
                    @endif
                </div>
                <div class="mt-6 pt-6 border-t border-base-content/10 text-left">
                    <p class="text-xs font-semibold text-base-content/50 uppercase tracking-wider mb-1">NIM / NIP / ID</p>
                    <p class="font-medium text-shadow-grey-900">{{ $user->nim ?? '-' }}</p>
                </div>
            </div>
        </div>

        <!-- Forms Area -->
        <div class="md:col-span-2 space-y-6">
            
            <!-- Update Profile Info Form -->
            <div class="bg-white border border-base-content/10 rounded-3xl p-6 sm:p-8 shadow-sm">
                <h3 class="text-lg font-bold text-shadow-grey-900 mb-4">Informasi Profil</h3>
                
                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label class="block text-sm font-bold text-base-content mb-1.5">Foto Profil (Opsional)</label>
                        <input type="file" name="avatar" class="file-input file-input-bordered w-full rounded-xl border-base-content/25 focus:outline-none focus:border-fern-600 text-sm font-medium {{ $errors->has('avatar') ? 'file-input-error' : '' }}" accept="image/*" />
                        @error('avatar')
                            <p class="text-error text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-base-content mb-1.5">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" class="input input-bordered w-full rounded-xl border-base-content/25 focus:outline-none focus:border-fern-600 text-sm font-medium {{ $errors->has('name') ? 'input-error' : '' }}" required />
                        @error('name')
                            <p class="text-error text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-base-content mb-1.5">Alamat Email</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" class="input input-bordered w-full rounded-xl border-base-content/25 focus:outline-none focus:border-fern-600 text-sm font-medium {{ $errors->has('email') ? 'input-error' : '' }}" required />
                        @error('email')
                            <p class="text-error text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pt-2 flex justify-end">
                        <button type="submit" class="btn bg-fern-700 hover:bg-fern-800 text-white border-0 rounded-2xl px-8 shadow-sm transition active:scale-95">Simpan Perubahan</button>
                    </div>
                </form>
            </div>

            <!-- Update Password Form -->
            <div class="bg-white border border-base-content/10 rounded-3xl p-6 sm:p-8 shadow-sm">
                <h3 class="text-lg font-bold text-shadow-grey-900 mb-4">Ubah Password</h3>
                
                <form action="{{ route('profile.password') }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label class="block text-sm font-bold text-base-content mb-1.5">Password Saat Ini</label>
                        <input type="password" name="current_password" class="input input-bordered w-full rounded-xl border-base-content/25 focus:outline-none focus:border-fern-600 text-sm font-medium {{ $errors->updatePassword->has('current_password') ? 'input-error' : '' }}" required />
                        @if($errors->updatePassword->has('current_password'))
                            <p class="text-error text-xs mt-1">{{ $errors->updatePassword->first('current_password') }}</p>
                        @endif
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-base-content mb-1.5">Password Baru</label>
                        <input type="password" name="password" class="input input-bordered w-full rounded-xl border-base-content/25 focus:outline-none focus:border-fern-600 text-sm font-medium {{ $errors->updatePassword->has('password') ? 'input-error' : '' }}" required />
                        @if($errors->updatePassword->has('password'))
                            <p class="text-error text-xs mt-1">{{ $errors->updatePassword->first('password') }}</p>
                        @endif
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-base-content mb-1.5">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" class="input input-bordered w-full rounded-xl border-base-content/25 focus:outline-none focus:border-fern-600 text-sm font-medium" required />
                    </div>

                    <div class="pt-2 flex justify-end">
                        <button type="submit" class="btn bg-shadow-grey-900 hover:bg-black text-white border-0 rounded-2xl px-8 shadow-sm transition active:scale-95">Perbarui Password</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
@endsection

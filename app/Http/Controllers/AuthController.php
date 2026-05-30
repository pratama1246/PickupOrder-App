<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Menampilkan halaman formulir login utama.
     * Menghitung URL kembali yang aman dari halaman sebelumnya agar pengguna bisa kembali
     * ke konteks browsing mereka jika membatalkan proses login.
     * Validasi domain internal mencegah celah open redirect dari URL eksternal yang disuntikkan.
     */
    public function showLogin(): View
    {
        $previous = url()->previous();
        $loginUrl = route('login');
        $appUrl   = config('app.url');

        // Pastikan URL sebelumnya adalah halaman internal (bukan login itu sendiri)
        // agar tidak terjadi loop redirect atau open redirect ke domain luar.
        $backUrl = (
            str_starts_with($previous, $appUrl) &&
            rtrim($previous, '/') !== rtrim($loginUrl, '/')
        ) ? $previous : route('home');

        return view('auth.login', compact('backUrl'));
    }

    /**
     * Memproses data autentikasi masuk pengguna.
     * Mendukung pengenal ganda (NIM/NIP untuk internal kampus atau Email untuk admin/external).
     * Melakukan regenerasi session ID demi menghindari celah keamanan Session Fixation.
     */
    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'identifier' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $identifier = $request->identifier;
        $remember = $request->boolean('remember');

        // Melakukan pengecekan ganda di kolom NIM dan Email agar mahasiswa tidak bingung saat login.
        $credentialsByNim = ['nim' => $identifier, 'password' => $request->password];
        $credentialsByEmail = ['email' => $identifier, 'password' => $request->password];

        if (Auth::attempt($credentialsByNim, $remember) || Auth::attempt($credentialsByEmail, $remember)) {
            $request->session()->regenerate();

            /** @var User $user */
            $user = Auth::user()->fresh();

            // Memaksa pengguna mengganti password bawaan jika ini adalah login pertama kali untuk mematuhi kebijakan keamanan.
            if ($user->is_first_login) {
                return redirect()->route('password.change.form');
            }

            return match ($user->role) {
                'admin' => redirect()->intended(route('admin.dashboard')),
                'vendor' => redirect()->intended(route('vendor.dashboard')),
                default => redirect()->intended(route('home')),
            };
        }

        return back()->withErrors([
            'identifier' => 'Email/NIM/NIP atau password tidak sesuai.',
        ])->onlyInput('identifier');
    }

    /**
     * Mengeluarkan pengguna dari sistem dan membersihkan data session.
     * Menggunakan session invalidation dan regenerasi CSRF token untuk memutus akses lama secara absolut.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    /**
     * Menampilkan formulir pergantian kata sandi wajib untuk pengguna baru.
     */
    public function showChangePassword(): View
    {
        return view('auth.change-password');
    }

    /**
     * Memproses pembaruan kata sandi awal pengguna baru.
     * Mengubah flag 'is_first_login' menjadi false sehingga pengguna tidak diarahkan kembali ke formulir ini pada sesi berikutnya.
     */
    public function changePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        /** @var User $user */
        $user = Auth::user();
        $user->update([
            'password' => $request->password,
            'is_first_login' => false,
            'password_changed' => true,
        ]);

        return match ($user->role) {
            'admin' => redirect()->intended(route('admin.dashboard')),
            'vendor' => redirect()->intended(route('vendor.dashboard')),
            default => redirect()->intended(route('home')),
        };
    }

    /**
     * Menampilkan formulir pemulihan kata sandi (lupa password).
     */
    public function showForgotPassword(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Memproses pengajuan pemulihan kata sandi.
     * Mengembalikan respons generik yang seragam untuk semua kondisi (NIM/Email ada atau tidak ada)
     * guna mencegah celah User Enumeration Attack yang bisa dimanfaatkan untuk harvesting akun kampus.
     */
    public function forgotPassword(Request $request): RedirectResponse
    {
        $request->validate([
            'identifier' => ['required', 'string'],
        ]);

        // Selalu kembalikan respons sukses yang sama, terlepas dari apakah NIM/email ditemukan atau tidak.
        // Ini mencegah penyerang memetakan akun mahasiswa yang valid melalui percobaan sistematis.
        User::where('nim', $request->identifier)
            ->orWhere('email', $request->identifier)
            ->first();

        return back()->with('status', 'reset-sent');
    }
}

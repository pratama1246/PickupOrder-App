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
     */
    public function showLogin(): View
    {
        return view('auth.login');
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
                'admin' => redirect()->route('admin.dashboard'),
                'vendor' => redirect()->route('vendor.dashboard'),
                default => redirect()->route('home'),
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

        return redirect()->route('login');
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
            'admin' => redirect()->route('admin.dashboard'),
            'vendor' => redirect()->route('vendor.dashboard'),
            default => redirect()->route('home'),
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
     * Melakukan mock status pengiriman link ke layar tanpa integrasi SMTP asli
     * guna menghindari overhead konfigurasi server surat eksternal pada lingkungan lokal.
     */
    public function forgotPassword(Request $request): RedirectResponse
    {
        $request->validate([
            'identifier' => ['required', 'string'],
        ]);

        $user = User::where('nim', $request->identifier)
            ->orWhere('email', $request->identifier)
            ->first();

        if (! $user) {
            return back()->withErrors([
                'identifier' => 'Email/NIM/NIP tidak ditemukan dalam sistem.',
            ])->onlyInput('identifier');
        }

        // Akun bawaan generator NIM terkadang belum memiliki email aktif.
        if (empty($user->email)) {
            return back()->with('status', 'no-email');
        }

        return back()->with('status', 'reset-sent');
    }
}

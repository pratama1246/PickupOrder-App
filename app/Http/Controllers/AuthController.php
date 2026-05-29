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
     * Tampilkan halaman form login.
     */
    public function showLogin(): View
    {
        return view('auth.login');
    }

    /**
     * Proses autentikasi user.
     * Menggunakan NIM/NIP sebagai identifier.
     */
    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'identifier' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $identifier = $request->identifier;
        $remember = $request->boolean('remember');

        // Check if identifier matches email or nim
        $credentialsByNim = ['nim' => $identifier, 'password' => $request->password];
        $credentialsByEmail = ['email' => $identifier, 'password' => $request->password];

        if (Auth::attempt($credentialsByNim, $remember) || Auth::attempt($credentialsByEmail, $remember)) {
            $request->session()->regenerate();

            /** @var User $user */
            $user = Auth::user()->fresh();

            // Paksa ganti password jika login pertama kali
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
     * Logout user dan hapus session.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Tampilkan form ganti password pertama kali.
     */
    public function showChangePassword(): View
    {
        return view('auth.change-password');
    }

    /**
     * Proses ganti password pertama kali.
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
     * Tampilkan form lupa password.
     */
    public function showForgotPassword(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Proses pengajuan lupa password (NIM/NIP check).
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

        // Cek jika akun belum ada email terdaftar
        if (empty($user->email)) {
            return back()->with('status', 'no-email');
        }

        // Mock status link terkirim untuk menghindari overengineering email server
        return back()->with('status', 'reset-sent');
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\Laravel\Facades\Image;

class ProfileController extends Controller
{
    /**
     * Menampilkan halaman profil dan pengaturan akun.
     * Mengembalikan tampilan yang disesuaikan secara dinamis untuk masing-masing role pengguna.
     */
    public function edit(Request $request): View
    {
        return view('profile', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Memperbarui informasi nama, email, dan avatar pengguna.
     * Menggunakan Intervention Image untuk memotong (cover 400x400) dan mengonversi gambar ke format WebP
     * dengan kualitas 80% guna menghemat memori penyimpanan server dan mengoptimalkan kecepatan muat halaman.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $filename = 'avatars/' . \Illuminate\Support\Str::random(40) . '.webp';

            try {
                // Memotong gambar dengan rasio persegi 400x400 dan dikompresi ke WebP.
                $image = Image::decode($file);
                $image->cover(400, 400);
                $webp = $image->encode(new WebpEncoder(quality: 80));
                Storage::disk('public')->put($filename, $webp->toString());
            } catch (\Exception $e) {
                return back()->withErrors(['avatar' => 'Berkas gambar rusak atau tidak dapat diproses.'])->withInput();
            }

            $data['avatar'] = $filename;
        }

        try {
            $oldAvatar = $user->avatar;
            $user->update($data);

            // Menghapus avatar lama dari penyimpanan agar disk server tidak lekas penuh.
            if (isset($data['avatar']) && $oldAvatar) {
                Storage::disk('public')->delete($oldAvatar);
            }
        } catch (\Exception $e) {
            // Hapus berkas baru jika update DB gagal
            if (isset($data['avatar'])) {
                Storage::disk('public')->delete($data['avatar']);
            }
            return back()->withErrors(['avatar' => 'Gagal memperbarui profil di database. Silakan coba lagi.'])->withInput();
        }

        return redirect()->route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Memperbarui kata sandi pengguna.
     * Menggunakan 'validateWithBag' agar pesan kesalahan tidak tercampur dengan formulir profil umum di view.
     * Hashing kata sandi ditangani secara otomatis oleh Eloquent model User (casts hashed).
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $request->user()->update([
            'password' => $validated['password'],
        ]);

        return redirect()->route('profile.edit')->with('status', 'password-updated');
    }
}

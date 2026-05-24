<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Intervention\Image\Laravel\Facades\Image;
use Intervention\Image\Encoders\WebpEncoder;

class ProfileController extends Controller
{
    /**
     * Menampilkan halaman profil dan pengaturan.
     */
    public function edit(Request $request): View
    {
        return view('profile', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Memperbarui informasi profil pengguna (Nama & Email).
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
            'avatar' => ['nullable', 'image', 'max:2048'],
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            
            $file = $request->file('avatar');
            $filename = 'avatars/' . uniqid('avatar_') . '.webp';
            
            // Kompres dan ubah ke webp
            $image = Image::decode($file);
            $image->cover(400, 400);
            $webp = $image->encode(new WebpEncoder(quality: 80));
            
            Storage::disk('public')->put($filename, $webp->toString());
            
            $data['avatar'] = $filename;
        }

        $user->update($data);

        return redirect()->route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Memperbarui password pengguna.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $request->user()->update([
            'password' => $validated['password'], // Hash is automatically handled by the User model's casts
        ]);

        return redirect()->route('profile.edit')->with('status', 'password-updated');
    }
}

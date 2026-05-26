<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CanteenController extends Controller
{
    public function edit()
    {
        $canteen = Auth::user()->canteen;
        abort_if(is_null($canteen), 403, 'Akun vendor ini belum memiliki kantin terdaftar.');

        return view('vendor.canteen-edit', compact('canteen'));
    }

    public function update(Request $request)
    {
        $canteen = Auth::user()->canteen;
        abort_if(is_null($canteen), 403, 'Akun vendor ini belum memiliki kantin terdaftar.');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240', // max 10MB
        ]);

        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada dan bukan dari path bawaan assets/
            if ($canteen->image && !str_starts_with($canteen->image, 'assets/')) {
                Storage::disk('public')->delete($canteen->image);
            }
            $validated['image'] = $request->file('image')->store('canteen_images', 'public');
        }

        $canteen->update($validated);

        return redirect()->route('vendor.dashboard')->with('success', 'Profil kantin berhasil diperbarui!');
    }
}

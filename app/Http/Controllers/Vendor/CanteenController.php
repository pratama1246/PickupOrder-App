<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\Laravel\Facades\Image;

class CanteenController extends Controller
{
    /**
     * Menampilkan halaman penyuntingan profil kantin khusus untuk pemilik kantin (vendor).
     * Melakukan pengecekan apakah relasi kantin terdaftar untuk mengantisipasi vendor yatim (orphaned vendor).
     */
    public function edit()
    {
        $canteen = Auth::user()->canteen;
        abort_if(is_null($canteen), 403, 'Akun vendor ini belum memiliki kantin terdaftar.');

        return view('vendor.canteen-edit', compact('canteen'));
    }

    /**
     * Memperbarui informasi nama, deskripsi, dan banner gambar kantin milik vendor.
     * Memiliki filter pelindung agar berkas gambar default di folder assets/ tidak ikut terhapus dari disk.
     */
    public function update(Request $request)
    {
        $canteen = Auth::user()->canteen;
        abort_if(is_null($canteen), 403, 'Akun vendor ini belum memiliki kantin terdaftar.');

        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'qris_image'  => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
        ]);

        // Sanitasi input teks agar tag HTML/link phishing tidak masuk ke profil kantin.
        $validated['name']        = strip_tags($validated['name']);
        $validated['description'] = strip_tags($validated['description'] ?? '');

        // By default, do not modify image and qris_image unless specified.
        unset($validated['image'], $validated['qris_image']);

        if ($request->input('delete_image') == '1') {
            if ($canteen->image && ! str_starts_with($canteen->image, 'assets/')) {
                Storage::disk('public')->delete($canteen->image);
            }
            $validated['image'] = null;
        } elseif ($request->hasFile('image')) {
            // Melindungi file aset gambar bawaan sistem agar tidak terhapus secara tidak sengaja.
            if ($canteen->image && ! str_starts_with($canteen->image, 'assets/')) {
                Storage::disk('public')->delete($canteen->image);
            }
            // Decode + re-encode ke WebP via Intervention Image untuk membuang metadata EXIF berbahaya.
            $filename = uniqid('canteen_').'.webp';
            $image    = Image::decode($request->file('image'));
            $image->scale(width: 1200);
            $webp = $image->encode(new WebpEncoder(quality: 75));
            Storage::disk('public')->put('canteens/'.$filename, $webp->toString());
            $validated['image'] = 'canteens/'.$filename;
        }

        if ($request->input('delete_qris_image') == '1') {
            if ($canteen->qris_image) {
                Storage::disk('public')->delete($canteen->qris_image);
            }
            $validated['qris_image'] = null;
        } elseif ($request->hasFile('qris_image')) {
            if ($canteen->qris_image) {
                Storage::disk('public')->delete($canteen->qris_image);
            }
            $filename = uniqid('qris_').'.webp';
            $image    = Image::decode($request->file('qris_image'));
            $image->scale(width: 800); // 800px is perfect for scannable QR Codes
            $webp = $image->encode(new WebpEncoder(quality: 85)); // Higher quality for scan precision
            Storage::disk('public')->put('qris/'.$filename, $webp->toString());
            $validated['qris_image'] = 'qris/'.$filename;
        }

        $canteen->update($validated);

        return redirect()->route('vendor.dashboard')->with('success', 'Profil kantin berhasil diperbarui!');
    }
}

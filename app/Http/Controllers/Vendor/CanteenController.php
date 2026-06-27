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

        $newImageFile = null;
        $newQrisFile = null;
        $deleteOldImage = false;
        $deleteOldQris = false;

        // Banner Image
        if ($request->input('delete_image') == '1') {
            $validated['image'] = null;
            $deleteOldImage = true;
        } elseif ($request->hasFile('image')) {
            $filename = \Illuminate\Support\Str::random(40).'.webp';
            try {
                $image = Image::decode($request->file('image'));
                $image->cover(1200, 450); // Aspect ratio landscape cover
                $webp = $image->encode(new WebpEncoder(quality: 75));
                Storage::disk('public')->put('canteens/'.$filename, $webp->toString());
                $newImageFile = 'canteens/'.$filename;
                $validated['image'] = $newImageFile;
                $deleteOldImage = true;
            } catch (\Exception $e) {
                return back()->withErrors(['image' => 'Berkas gambar banner rusak atau tidak dapat diproses.'])->withInput();
            }
        }

        // QRIS Image
        if ($request->input('delete_qris_image') == '1') {
            $validated['qris_image'] = null;
            $deleteOldQris = true;
        } elseif ($request->hasFile('qris_image')) {
            $filename = \Illuminate\Support\Str::random(40).'.webp';
            try {
                $image = Image::decode($request->file('qris_image'));
                $image->scale(width: 800); // 800px is perfect for scannable QR Codes
                $webp = $image->encode(new WebpEncoder(quality: 85)); // Higher quality for scan precision
                Storage::disk('public')->put('qris/'.$filename, $webp->toString());
                $newQrisFile = 'qris/'.$filename;
                $validated['qris_image'] = $newQrisFile;
                $deleteOldQris = true;
            } catch (\Exception $e) {
                // Hapus new image if QRIS decode fails
                if ($newImageFile) {
                    Storage::disk('public')->delete($newImageFile);
                }
                return back()->withErrors(['qris_image' => 'Berkas gambar QRIS rusak atau tidak dapat diproses.'])->withInput();
            }
        }

        try {
            $oldImage = $canteen->image;
            $oldQris = $canteen->qris_image;

            $canteen->update($validated);

            // Jika DB berhasil diupdate, lakukan penghapusan file lama di disk
            if ($deleteOldImage && $oldImage && ! str_starts_with($oldImage, 'assets/')) {
                Storage::disk('public')->delete($oldImage);
            }
            if ($deleteOldQris && $oldQris) {
                Storage::disk('public')->delete($oldQris);
            }
        } catch (\Exception $e) {
            // Hapus file baru yang baru saja di-upload jika DB gagal diupdate
            if ($newImageFile) {
                Storage::disk('public')->delete($newImageFile);
            }
            if ($newQrisFile) {
                Storage::disk('public')->delete($newQrisFile);
            }
            return back()->withErrors(['image' => 'Gagal memperbarui data kantin di database. Silakan coba lagi.'])->withInput();
        }

        return redirect()->route('vendor.dashboard')->with('success', 'Profil kantin berhasil diperbarui!');
    }
}

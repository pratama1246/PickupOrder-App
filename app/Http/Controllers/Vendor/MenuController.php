<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\Laravel\Facades\Image;

class MenuController extends Controller
{
    /**
     * Menampilkan daftar menu makanan khusus milik kantin vendor bersangkutan (/vendor/menu).
     * Menerapkan pembatasan query (tenant isolation) untuk menjamin keamanan akses data antar-pemilik kantin.
     */
    public function index(Request $request): View
    {
        $canteen = Auth::user()->canteen;

        // Membatasi akses query hanya pada menu yang berelasi dengan kantin vendor bersangkutan.
        $query = $canteen->menus()->latest();

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        if ($request->filled('status')) {
            $query->where('is_available', $request->status === 'tersedia');
        }

        $menus = $query->paginate(12)->withQueryString();

        return view('vendor.menu', compact('menus', 'canteen'));
    }

    /**
     * Menampilkan formulir pendaftaran menu baru.
     */
    public function create(): View
    {
        return view('vendor.menu-create');
    }

    /**
     * Menyimpan menu baru ke dalam database.
     * Mengompresi dan mengubah format gambar secara real-time ke format WebP untuk menghemat bandwidth
     * bandwidth jaringan kampus dan media penyimpanan server (storage).
     */
    public function store(Request $request): RedirectResponse
    {
        $canteen = Auth::user()->canteen;

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'category' => ['nullable', 'string', 'in:Nasi,Ayam,Sayur,Minuman,Makanan,Cemilan'],
            'description' => ['nullable', 'string', 'max:500'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'is_available' => ['boolean'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
        ]);

        // Sanitasi input teks agar nama/deskripsi menu tidak mengandung tag HTML atau link phishing.
        $validated['name']        = strip_tags($validated['name']);
        $validated['description'] = strip_tags($validated['description'] ?? '');

        if ($request->hasFile('image')) {
            // Pemrosesan Gambar: Konversi ke WebP, batasi lebar maksimal 800px untuk menghemat ruang disk,
            // serta atur kualitas kompresi ke 75% untuk menjaga kualitas visual yang optimal.
            $filename = uniqid('menu_').'.webp';
            $image = Image::decode($request->file('image'));
            $image->scale(width: 800);
            $webp = $image->encode(new WebpEncoder(quality: 75));
            Storage::disk('public')->put('menus/'.$filename, $webp->toString());
            $validated['image'] = 'menus/'.$filename;
        }

        $canteen->menus()->create($validated);

        return redirect()->route('vendor.menu.index')
            ->with('success', 'Menu berhasil ditambahkan.');
    }

    /**
     * Menampilkan formulir sunting menu berdasarkan ID.
     */
    public function edit(int $id): View
    {
        $canteen = Auth::user()->canteen;
        $menu = $canteen->menus()->findOrFail($id);

        return view('vendor.menu-edit', compact('menu'));
    }

    /**
     * Memperbarui detail informasi dan gambar menu makanan milik vendor.
     * Membersihkan berkas gambar lama dari penyimpanan lokal apabila pengguna mengganti gambar.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $canteen = Auth::user()->canteen;
        $menu = $canteen->menus()->findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'category' => ['nullable', 'string', 'in:Nasi,Ayam,Sayur,Minuman,Makanan,Cemilan'],
            'description' => ['nullable', 'string', 'max:500'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'is_available' => ['boolean'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
        ]);

        // Sanitasi input teks agar nama/deskripsi menu tidak mengandung tag HTML atau link phishing.
        $validated['name']        = strip_tags($validated['name']);
        $validated['description'] = strip_tags($validated['description'] ?? '');

        if ($request->hasFile('image')) {
            // Menghapus gambar lama di disk jika ada untuk mencegah sampah berkas tidak terpakai.
            if ($menu->image) {
                Storage::disk('public')->delete($menu->image);
            }
            $filename = uniqid('menu_').'.webp';
            $image = Image::decode($request->file('image'));
            $image->scale(width: 800);
            $webp = $image->encode(new WebpEncoder(quality: 75));
            Storage::disk('public')->put('menus/'.$filename, $webp->toString());
            $validated['image'] = 'menus/'.$filename;
        }

        $menu->update($validated);

        return redirect()->route('vendor.menu.index')
            ->with('success', 'Menu berhasil diperbarui.');
    }

    /**
     * Menghapus secara permanen record menu makanan dari database.
     * Secara otomatis menghapus gambar terkait dari server penyimpanan.
     */
    public function destroy(int $id): RedirectResponse
    {
        $canteen = Auth::user()->canteen;
        $menu = $canteen->menus()->findOrFail($id);

        if ($menu->image) {
            Storage::disk('public')->delete($menu->image);
        }

        $menu->delete();

        return back()->with('success', 'Menu berhasil dihapus.');
    }
}

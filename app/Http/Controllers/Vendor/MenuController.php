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
     * Daftar menu milik kantin vendor (/vendor/menu).
     * Mendukung pencarian dan filter ketersediaan.
     */
    public function index(Request $request): View
    {
        $canteen = Auth::user()->canteen;

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
     * Form tambah menu baru.
     */
    public function create(): View
    {
        return view('vendor.menu-create');
    }

    /**
     * Simpan menu baru ke database.
     */
    public function store(Request $request): RedirectResponse
    {
        $canteen = Auth::user()->canteen;

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'is_available' => ['boolean'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
        ]);

        if ($request->hasFile('image')) {
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
     * Form edit menu.
     */
    public function edit(int $id): View
    {
        $canteen = Auth::user()->canteen;
        $menu = $canteen->menus()->findOrFail($id);

        return view('vendor.menu-edit', compact('menu'));
    }

    /**
     * Update data menu.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $canteen = Auth::user()->canteen;
        $menu = $canteen->menus()->findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'is_available' => ['boolean'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
        ]);

        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada
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
     * Hapus menu dari database.
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

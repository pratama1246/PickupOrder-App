<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Canteen;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\Laravel\Facades\Image;

class CanteenController extends Controller
{
    /**
     * Daftar semua kantin terdaftar (/admin/kantin).
     * Mendukung pencarian dan filter status buka/tutup.
     */
    public function index(Request $request): View
    {
        $query = Canteen::with('owner')->withCount('menus');

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        if ($request->filled('status')) {
            $query->where('is_open', $request->status === 'buka');
        }

        $canteens = $query->latest()->paginate(10)->withQueryString();

        return view('admin.kantin', compact('canteens'));
    }

    /**
     * Detail satu kantin (/admin/kantin/{id}).
     */
    public function show(int $id): View
    {
        $canteen = Canteen::with('owner')
            ->withCount('menus')
            ->withCount('orders')
            ->withCount(['orders as completed_orders_count' => function ($query) {
                $query->where('status', 'selesai');
            }])
            ->withSum(['orders as total_revenue' => function ($query) {
                $query->where('status', 'selesai');
            }], 'total_price')
            ->findOrFail($id);

        $menus = $canteen->menus()->latest()->paginate(5, ['*'], 'menus_page')->withQueryString();
        $orders = $canteen->orders()->with('user')->latest()->paginate(5, ['*'], 'orders_page')->withQueryString();

        return view('admin.kantin-show', compact('canteen', 'menus', 'orders'));
    }

    /**
     * Form tambah kantin baru (/admin/kantin/tambah).
     */
    public function create(): View
    {
        return view('admin.kantin-create');
    }

    /**
     * Simpan kantin baru ke database.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'is_open' => ['boolean'],
        ]);

        // Generate base email (tanpa spasi/dash, e.g. kantinharmoni)
        $cleanName = Str::slug($validated['name'], '');
        $baseEmail = $cleanName.'@pnc.ac.id';
        $email = $baseEmail;
        $counter = 1;

        // Ensure email is unique
        while (User::where('email', $email)->exists()) {
            $email = $cleanName.$counter.'@pnc.ac.id';
            $counter++;
        }

        $password = 'pncpickup123';

        // Auto-create vendor user
        $user = User::create([
            'name' => 'Vendor '.$validated['name'],
            'email' => $email,
            'password' => Hash::make($password),
            'role' => 'vendor',
            'is_first_login' => true,
            'password_changed' => false,
        ]);

        $validated['user_id'] = $user->id;

        if ($request->hasFile('image')) {
            $filename = uniqid('canteen_').'.webp';
            $image = Image::decode($request->file('image'));
            $image->scale(width: 1200);
            $webp = $image->encode(new WebpEncoder(quality: 75));
            Storage::disk('public')->put('canteens/'.$filename, $webp->toString());
            $validated['image'] = 'canteens/'.$filename;
        }

        Canteen::create($validated);

        return redirect()->route('admin.kantin.index')
            ->with('success', "Kantin berhasil ditambahkan. Akun Vendor dibuat dengan Email: {$email} dan Password: {$password}");
    }

    /**
     * Form edit data kantin (/admin/kantin/{id}/edit).
     */
    public function edit(int $id): View
    {
        $canteen = Canteen::with('owner')->findOrFail($id);

        return view('admin.kantin-edit', compact('canteen'));
    }

    /**
     * Update data kantin.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $canteen = Canteen::findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'is_open' => ['boolean'],
        ]);

        if ($request->hasFile('image')) {
            if ($canteen->image) {
                Storage::disk('public')->delete($canteen->image);
            }
            $filename = uniqid('canteen_').'.webp';
            $image = Image::decode($request->file('image'));
            $image->scale(width: 1200);
            $webp = $image->encode(new WebpEncoder(quality: 75));
            Storage::disk('public')->put('canteens/'.$filename, $webp->toString());
            $validated['image'] = 'canteens/'.$filename;
        }

        $canteen->update($validated);

        return redirect()->route('admin.kantin.index')
            ->with('success', 'Data kantin berhasil diperbarui.');
    }

    /**
     * Hapus kantin dari sistem (cascade delete ke menus & orders).
     */
    public function destroy(int $id): RedirectResponse
    {
        $canteen = Canteen::findOrFail($id);

        if ($canteen->image) {
            Storage::disk('public')->delete($canteen->image);
        }

        $canteen->delete();

        return redirect()->route('admin.kantin.index')->with('success', 'Kantin berhasil dihapus.');
    }

    /**
     * Hapus beberapa kantin sekaligus.
     */
    public function bulkDestroy(Request $request): RedirectResponse
    {
        $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['exists:canteens,id'],
        ]);

        $canteens = Canteen::whereIn('id', $request->ids)->get();

        foreach ($canteens as $canteen) {
            if ($canteen->image) {
                Storage::disk('public')->delete($canteen->image);
            }
            $canteen->delete();
        }

        return redirect()->route('admin.kantin.index')->with('success', 'Kantin terpilih berhasil dihapus.');
    }
}

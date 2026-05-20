<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Canteen;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

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
        $canteen = Canteen::with('owner')->withCount('menus')->findOrFail($id);

        return view('admin.kantin-show', compact('canteen'));
    }

    /**
     * Form tambah kantin baru (/admin/kantin/tambah).
     */
    public function create(): View
    {
        // Hanya user ber-role vendor yang belum punya kantin yang bisa dipilih
        $vendors = User::where('role', 'vendor')
            ->doesntHave('canteen')
            ->orderBy('name')
            ->get();

        return view('admin.kantin-create', compact('vendors'));
    }

    /**
     * Simpan kantin baru ke database.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_open' => ['boolean'],
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('canteens', 'public');
        }

        Canteen::create($validated);

        return redirect()->route('admin.kantin.index')
            ->with('success', 'Kantin berhasil ditambahkan.');
    }

    /**
     * Form edit data kantin (/admin/kantin/{id}/edit).
     */
    public function edit(int $id): View
    {
        $canteen = Canteen::with('owner')->findOrFail($id);

        $vendors = User::where('role', 'vendor')
            ->where(function ($q) use ($canteen) {
                $q->doesntHave('canteen')
                    ->orWhere('id', $canteen->user_id);
            })
            ->orderBy('name')
            ->get();

        return view('admin.kantin-edit', compact('canteen', 'vendors'));
    }

    /**
     * Update data kantin.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $canteen = Canteen::findOrFail($id);

        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_open' => ['boolean'],
        ]);

        if ($request->hasFile('image')) {
            if ($canteen->image) {
                Storage::disk('public')->delete($canteen->image);
            }
            $validated['image'] = $request->file('image')->store('canteens', 'public');
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

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Daftar semua pengguna sistem (/admin/pengguna).
     * Menampilkan mahasiswa dan vendor, mendukung pencarian dan filter role.
     */
    public function index(Request $request): View
    {
        $query = User::whereIn('role', ['mahasiswa', 'vendor'])->orderBy('name');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('nim', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->paginate(15)->withQueryString();

        return view('admin.pengguna', compact('users'));
    }

    /**
     * Form tambah pengguna baru (/admin/pengguna/create).
     */
    public function create(): View
    {
        return view('admin.pengguna-create');
    }

    /**
     * Simpan pengguna baru ke database.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nim' => [$request->role === 'vendor' ? 'nullable' : 'required', 'string', 'max:50', 'unique:users,nim'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'in:mahasiswa,vendor'],
            'is_first_login' => ['required', 'boolean'],
        ]);

        if ($request->role === 'vendor') {
            $validated['nim'] = null;
        }

        $validated['password'] = \Illuminate\Support\Facades\Hash::make($validated['password']);
        $validated['password_changed'] = false;

        User::create($validated);

        return redirect()->route('admin.pengguna.index')
            ->with('success', 'Pengguna berhasil ditambahkan.');
    }

    /**
     * Form edit data pengguna (/admin/pengguna/{id}/edit).
     */
    public function edit(int $id): View
    {
        $user = User::whereIn('role', ['mahasiswa', 'vendor'])->findOrFail($id);

        return view('admin.pengguna-edit', compact('user'));
    }

    /**
     * Update data pengguna.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $user = User::whereIn('role', ['mahasiswa', 'vendor'])->findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nim' => [$request->role === 'vendor' ? 'nullable' : 'required', 'string', 'max:50', 'unique:users,nim,' . $id],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $id],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'in:mahasiswa,vendor'],
            'is_first_login' => ['required', 'boolean'],
        ]);

        if ($request->role === 'vendor') {
            $validated['nim'] = null;
        }

        if (! empty($validated['password'])) {
            $validated['password'] = \Illuminate\Support\Facades\Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('admin.pengguna.index')
            ->with('success', 'Pengguna berhasil diperbarui.');
    }

    /**
     * Toggle status aktif/nonaktif akun pengguna.
     * Menggunakan kolom is_first_login sebagai status aktif (sementara).
     * Implementasi penuh memerlukan kolom is_active di migrasi.
     */
    public function toggle(int $id): RedirectResponse
    {
        $user = User::whereIn('role', ['mahasiswa', 'vendor'])->findOrFail($id);

        // Toggle: jika is_first_login true berarti belum aktif sepenuhnya
        $user->update([
            'is_first_login' => ! $user->is_first_login,
        ]);

        $status = $user->is_first_login ? 'dinonaktifkan' : 'diaktifkan';

        return back()->with('success', "Akun {$user->name} berhasil {$status}.");
    }

    /**
     * Hapus akun pengguna dari database.
     */
    public function destroy(int $id): RedirectResponse
    {
        $user = User::whereIn('role', ['mahasiswa', 'vendor'])->findOrFail($id);

        $user->delete();

        return back()->with('success', "Akun {$user->name} berhasil dihapus.");
    }
}

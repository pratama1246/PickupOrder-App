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
     * Toggle status aktif/nonaktif akun pengguna.
     * Menggunakan kolom is_first_login sebagai status aktif (sementara).
     * Implementasi penuh memerlukan kolom is_active di migrasi.
     */
    public function update(Request $request, int $id): RedirectResponse
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

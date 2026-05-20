<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Canteen;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CanteenController extends Controller
{
    /**
     * Browse semua kantin dan menu (halaman /pesan).
     * Mendukung pencarian dan filter status kantin.
     */
    public function index(Request $request): View
    {
        $query = Canteen::with(['menus' => function ($q) {
            $q->where('is_available', true)->where('stock', '>', 0);
        }])->withCount('availableMenus');

        // Pencarian berdasarkan nama kantin atau nama menu
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhereHas('menus', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Filter status: buka / tutup
        if ($request->filled('status')) {
            $query->where('is_open', $request->status === 'buka');
        }

        $canteens = $query->latest()->paginate(9)->withQueryString();

        return view('user.pesanan', compact('canteens'));
    }

    /**
     * Detail satu kantin beserta daftar menu-nya (halaman /kantin/{id}).
     */
    public function show(int $id): View
    {
        $canteen = Canteen::with(['menus' => function ($q) {
            $q->where('is_available', true)->orderBy('name');
        }])->findOrFail($id);

        return view('user.kantin', compact('canteen'));
    }
}

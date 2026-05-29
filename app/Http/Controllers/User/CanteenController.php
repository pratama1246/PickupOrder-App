<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Canteen;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CanteenController extends Controller
{
    /**
     * Browse semua kantin dan menu (halaman /browse).
     * Mendukung pencarian dan filter status kantin.
     */
    public function index(Request $request): View
    {
        $query = Canteen::with(['menus' => function ($q) use ($request) {
            $q->where('is_available', true)->where('stock', '>', 0);
            if ($request->filled('category')) {
                $q->where('category', $request->category);
            }
            if ($request->filled('search')) {
                $search = $request->search;
                $q->where(function ($q3) use ($search) {
                    $q3->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhereHas('canteen', function ($q4) use ($search) {
                            $q4->where('name', 'like', "%{$search}%")
                                ->orWhere('description', 'like', "%{$search}%");
                        });
                });
            }
        }])->withCount(['availableMenus' => function ($q) use ($request) {
            if ($request->filled('category')) {
                $q->where('category', $request->category);
            }
            if ($request->filled('search')) {
                $search = $request->search;
                $q->where(function ($q3) use ($search) {
                    $q3->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhereHas('canteen', function ($q4) use ($search) {
                            $q4->where('name', 'like', "%{$search}%")
                                ->orWhere('description', 'like', "%{$search}%");
                        });
                });
            }
        }]);

        // Pencarian berdasarkan nama/deskripsi kantin atau nama/deskripsi menu
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('menus', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%")
                            ->orWhere('description', 'like', "%{$search}%");
                    });
            });
        }

        // Filter kantin spesifik
        if ($request->filled('canteen')) {
            $query->where('id', $request->canteen);
        }

        // Filter kategori spesifik
        if ($request->filled('category')) {
            $query->whereHas('availableMenus', function ($q) use ($request) {
                $q->where('category', $request->category);
            });
        }

        // Filter status: buka / tutup
        if ($request->filled('status')) {
            $query->where('is_open', $request->status === 'buka');
        }

        $canteens = $query->latest()->paginate(9)->withQueryString();

        $categories = Menu::select('category')->distinct()->whereNotNull('category')->pluck('category');
        $allCanteens = Canteen::where('is_open', true)->select('id', 'name')->get();

        return view('user.browse', compact('canteens', 'categories', 'allCanteens'));
    }

    /**
     * Detail satu kantin beserta daftar menu-nya (halaman /canteen/{id}).
     */
    public function show(int $id): View
    {
        $canteen = Canteen::with(['menus' => function ($q) {
            $q->where('is_available', true)->orderBy('name');
        }])->findOrFail($id);

        return view('user.canteen', compact('canteen'));
    }
}

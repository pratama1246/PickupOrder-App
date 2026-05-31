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
     * Menampilkan halaman pencarian/jelajah kantin dan menu secara interaktif (/browse).
     * Mendukung filter pencarian nama makanan/kantin, filter kategori, status buka,
     * serta menggunakan nested eager loading untuk menyaring isi menu yang ditampilkan di bawah kartu kantin.
     */
    public function index(Request $request): View
    {
        // Membatasi menu yang dimuat hanya yang berstatus aktif dan stoknya tersedia.
        // Menerapkan pencarian dan filter kategori secara rekursif ke dalam relasi menus.
        $query = Canteen::withAvg('reviews', 'rating')->with(['menus' => function ($q) use ($request) {
            $q->where('is_available', true);
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

        // Melakukan penelusuran nama/deskripsi kantin atau nama/deskripsi menu pada level query utama.
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

        if ($request->filled('canteen')) {
            $query->where('id', $request->canteen);
        }

        if ($request->filled('category')) {
            $query->whereHas('availableMenus', function ($q) use ($request) {
                $q->where('category', $request->category);
            });
        }

        if ($request->filled('status')) {
            $query->where('is_open', $request->status === 'buka');
        }

        // Paginate dengan angka 9 agar pas terbagi dalam grid 3 kolom pada desktop (3 baris sempurna).
        $canteens = $query->latest()->paginate(9)->withQueryString();

        $categories = Menu::select('category')->distinct()->whereNotNull('category')->pluck('category');
        $allCanteens = Canteen::where('is_open', true)->select('id', 'name')->get();

        return view('user.browse', compact('canteens', 'categories', 'allCanteens'));
    }

    /**
     * Menampilkan informasi detail satu kantin beserta katalog menu miliknya (/canteen/{id}).
     * Hanya memuat menu-menu aktif dan diurutkan secara alfabetis (A-Z) untuk mempermudah pembacaan.
     */
    public function show(int $id): View
    {
        $canteen = Canteen::withAvg('reviews', 'rating')->with(['menus' => function ($q) {
            $q->where('is_available', true)->orderBy('name');
        }])->findOrFail($id);

        return view('user.canteen', compact('canteen'));
    }
}

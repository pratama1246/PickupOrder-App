<?php

namespace App\Http\Controllers;

use App\Models\Canteen;
use App\Models\Menu;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Beranda mahasiswa.
     * Menampilkan kantin yang sedang buka dan menu-menu populer.
     */
    public function index(): View|RedirectResponse
    {
        // Kantin yang sedang buka, diambil 6 teratas
        $canteens = Canteen::where('is_open', true)
            ->withCount('availableMenus')
            ->latest()
            ->take(6)
            ->get();

        // Menu populer: menu tersedia dari kantin yang buka, diurutkan berdasarkan frekuensi dipesan
        $popularMenus = Menu::where('is_available', true)
            ->where('stock', '>', 0)
            ->whereHas('canteen', function ($query) {
                $query->where('is_open', true);
            })
            ->with(['canteen'])
            ->withAvg('reviews', 'rating')
            ->withCount('orderItems')
            ->orderByDesc('order_items_count')
            ->take(8)
            ->get();

        // Kategori untuk slider shortcut di beranda
        $categories = Menu::select('category')
            ->distinct()
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->where('is_available', true)
            ->where('stock', '>', 0)
            ->pluck('category')
            ->sort()
            ->values();

        return view('user.index', compact('canteens', 'popularMenus', 'categories'));
    }

    /**
     * Halaman Tentang Kami.
     */
    public function about(): View
    {
        return view('user.about');
    }
}

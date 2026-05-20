<?php

namespace App\Http\Controllers;

use App\Models\Canteen;
use App\Models\Menu;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Beranda mahasiswa.
     * Menampilkan kantin yang sedang buka dan menu-menu populer.
     */
    public function index(): View
    {
        // Kantin yang sedang buka, diambil 6 teratas
        $canteens = Canteen::where('is_open', true)
            ->withCount('availableMenus')
            ->latest()
            ->take(6)
            ->get();

        // Menu populer: menu tersedia, diurutkan berdasarkan frekuensi dipesan
        $popularMenus = Menu::where('is_available', true)
            ->where('stock', '>', 0)
            ->withCount('orderItems')
            ->orderByDesc('order_items_count')
            ->take(8)
            ->get();

        return view('user.index', compact('canteens', 'popularMenus'));
    }
}

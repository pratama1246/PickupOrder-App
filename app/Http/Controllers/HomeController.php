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
            ->withAvg('reviews', 'rating')
            ->withCount('availableMenus')
            ->latest()
            ->take(6)
            ->get();

        // Menu populer: menu tersedia dari kantin yang buka, diurutkan berdasarkan frekuensi dipesan (pesanan selesai dalam 30 hari terakhir, minimal dipesan 1x)
        $popularMenus = Menu::where('is_available', true)
            ->where('stock', '>', 0)
            ->whereHas('canteen', function ($query) {
                $query->where('is_open', true);
            })
            ->whereHas('orderItems', function ($query) {
                $query->whereHas('order', function ($q) {
                    $q->where('status', 'selesai')
                      ->where('created_at', '>=', now()->subDays(30));
                });
            })
            ->with(['canteen'])
            ->withAvg('reviews', 'rating')
            ->withCount(['orderItems as order_items_count' => function ($query) {
                $query->whereHas('order', function ($q) {
                    $q->where('status', 'selesai')
                      ->where('created_at', '>=', now()->subDays(30));
                });
            }])
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

    public function about(): View
    {
        return view('user.about');
    }
}

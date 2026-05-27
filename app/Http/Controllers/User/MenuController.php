<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\View\View;

class MenuController extends Controller
{
    /**
     * Halaman detail satu menu makanan (/kantin/{canteenId}/menu/{id}).
     * Menampilkan info lengkap menu + daftar menu lain dari kantin yang sama.
     */
    public function show(int $canteenId, int $id): View
    {
        $menu = Menu::with('canteen')->findOrFail($id);

        // Pastikan menu memang milik kantin yang diminta
        abort_if($menu->canteen_id !== $canteenId, 404);

        // Menu lain dari kantin yang sama (tidak termasuk menu yang sedang dilihat)
        $otherMenus = Menu::where('canteen_id', $canteenId)
            ->where('id', '!=', $id)
            ->where('is_available', true)
            ->take(6)
            ->get();

        // Mengambil daftar ulasan beserta user pengulas (maksimal 20 terbaru)
        $reviews = $menu->reviews()->with('user')->latest()->take(20)->get();

        return view('user.menu-detail', compact('menu', 'otherMenus', 'reviews'));
    }
}

<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\View\View;

class MenuController extends Controller
{
    /**
     * Menampilkan halaman detail lengkap sebuah menu makanan (/canteen/{canteenId}/menu/{id}).
     * Memuat ulasan terkini, menghitung akumulasi penjualan 30 hari terakhir sebagai bukti sosial (social proof),
     * serta merekomendasikan menu sejenis dari kantin yang sama.
     */
    public function show(int $canteenId, int $id): View
    {
        // Menghitung jumlah pesanan selesai dalam 30 hari terakhir untuk menampilkan label "Terjual X" di antarmuka.
        $menu = Menu::with('canteen')
            ->withAvg('reviews', 'rating')
            ->withCount(['orderItems as recent_orders_count' => function ($query) {
                $query->whereHas('order', function ($q) {
                    $q->where('status', 'selesai')
                      ->where('created_at', '>=', now()->subDays(30));
                });
            }])
            ->findOrFail($id);

        // Validasi silang untuk memastikan menu benar-benar milik kantin yang sedang dijelajahi.
        abort_if($menu->canteen_id !== $canteenId, 404);

        // Mengambil menu lain dari kantin yang sama sebagai rekomendasi alternatif,
        // mengecualikan menu yang saat ini sedang dibuka agar tidak redundan.
        $otherMenus = Menu::where('canteen_id', $canteenId)
            ->where('id', '!=', $id)
            ->where('is_available', true)
            ->with(['canteen'])
            ->withAvg('reviews', 'rating')
            ->take(6)
            ->get();

        // Membatasi ulasan hanya 20 terbaru untuk menjaga kecepatan loading dan kebersihan tampilan.
        $reviews = $menu->reviews()->with('user')->latest()->take(20)->get();

        return view('user.menu-detail', compact('menu', 'otherMenus', 'reviews'));
    }
}

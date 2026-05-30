<?php

namespace App\Http\Controllers;

use App\Models\Canteen;
use App\Models\Menu;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Menampilkan halaman beranda utama untuk Mahasiswa.
     * Memuat informasi kantin aktif, menu terpopuler dalam 30 hari terakhir,
     * serta daftar kategori makanan untuk mempermudah pencarian cepat.
     */
    public function index(): View|RedirectResponse
    {
        // Mengambil maksimal 6 kantin yang status operasionalnya sedang buka.
        // Eager load data rata-rata rating review dan jumlah menu yang aktif untuk ditampilkan di kartu info kantin.
        $canteens = Canteen::where('is_open', true)
            ->withAvg('reviews', 'rating')
            ->withCount('availableMenus')
            ->latest()
            ->take(6)
            ->get();

        // Mengambil menu populer dengan kriteria: stok fisik tersedia, kantin pemilik sedang buka,
        // dan memiliki riwayat pesanan bersetatus 'selesai' dalam kurun waktu 30 hari terakhir.
        // Diurutkan berdasarkan jumlah transaksi terbanyak agar representatif terhadap tren terkini mahasiswa.
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

        // Mengambil daftar nama kategori unik dari seluruh menu makanan yang saat ini dapat dipesan.
        // Digunakan untuk membangun slider tombol shortcut kategori di beranda.
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
     * Menampilkan halaman informasi profil institusi ("Tentang Kami").
     * Menjelaskan latar belakang pembuatan aplikasi Pickup Order untuk Politeknik Negeri Cilacap.
     */
    public function about(): View
    {
        return view('user.about');
    }
}

<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Mengatur status buka/tutup warung kantin secara real-time.
     * Mendukung permintaan asinkron (AJAX/JSON) agar toggle status di navbar dashboard berjalan seamless.
     */
    public function toggleStatus(Request $request)
    {
        $canteen = Auth::user()->canteen;
        abort_if(is_null($canteen), 403, 'Akun vendor ini belum memiliki kantin terdaftar.');

        $isOpen = $request->has('is_open') ? $request->boolean('is_open') : ! $canteen->is_open;
        $canteen->update(['is_open' => $isOpen]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'is_open' => $canteen->is_open,
                'message' => $canteen->is_open ? 'Kantin berhasil dibuka!' : 'Kantin telah ditutup.',
            ]);
        }

        return back()->with('success', $canteen->is_open ? 'Kantin berhasil dibuka!' : 'Kantin telah ditutup.');
    }

    /**
     * Memperbarui nominal target omzet harian kantin.
     * Nilai ini digunakan oleh widget KPI "Target Pendapatan Hari Ini" di antarmuka vendor.
     */
    public function updateTarget(Request $request)
    {
        $canteen = Auth::user()->canteen;
        abort_if(is_null($canteen), 403, 'Akun vendor ini belum memiliki kantin terdaftar.');

        $request->validate([
            'daily_target' => 'required|numeric|min:1',
        ]);

        $canteen->update(['daily_target' => $request->daily_target]);

        return back()->with('success', 'Target pendapatan harian berhasil diperbarui!');
    }

    /**
     * Menampilkan dashboard utama monitoring performa kantin milik vendor.
     * Mengompilasi statistik antrean, omzet harian vs target harian,
     * laju penyelesaian pesanan (completion rate), dan ulasan pelanggan.
     */
    public function index(): View
    {
        $canteen = Auth::user()->canteen;

        abort_if(is_null($canteen), 403, 'Akun vendor ini belum memiliki kantin terdaftar.');

        $todayStart = now()->startOfDay();
        $yesterdayStart = now()->subDay()->startOfDay();
        $yesterdayEnd = now()->subDay()->endOfDay();

        // Penghitungan pertumbuhan omzet pendapatan harian dibanding hari kemarin.
        $todayRevenue = Order::where('canteen_id', $canteen->id)->where('status', 'selesai')->where('created_at', '>=', $todayStart)->sum('total_price');
        $yesterdayRevenue = Order::where('canteen_id', $canteen->id)->where('status', 'selesai')->whereBetween('created_at', [$yesterdayStart, $yesterdayEnd])->sum('total_price');
        $revenueGrowth = $yesterdayRevenue > 0 ? (($todayRevenue - $yesterdayRevenue) / $yesterdayRevenue) * 100 : ($todayRevenue > 0 ? 100 : 0);

        // Penghitungan pertumbuhan total kuantitas order selesai.
        $todayOrders = Order::where('canteen_id', $canteen->id)->where('status', 'selesai')->where('created_at', '>=', $todayStart)->count();
        $yesterdayOrders = Order::where('canteen_id', $canteen->id)->where('status', 'selesai')->whereBetween('created_at', [$yesterdayStart, $yesterdayEnd])->count();
        $ordersGrowth = $yesterdayOrders > 0 ? (($todayOrders - $yesterdayOrders) / $yesterdayOrders) * 100 : ($todayOrders > 0 ? 100 : 0);

        $aovToday = $todayOrders > 0 ? $todayRevenue / $todayOrders : 0;

        // Completion Rate: Menilai persentase pesanan masuk hari ini yang sukses diselesaikan
        // dibanding total pesanan masuk (termasuk yang dibatalkan/ditolak).
        $totalToday = Order::where('canteen_id', $canteen->id)->where('created_at', '>=', $todayStart)->count();
        $completionRate = $totalToday > 0 ? ($todayOrders / $totalToday) * 100 : 0;

        $stats = [
            'pendapatan_hari_ini' => $todayRevenue,
            'pendapatan_growth' => round($revenueGrowth, 1),
            'pesanan_hari_ini' => $todayOrders,
            'pesanan_growth' => round($ordersGrowth, 1),
            'aov_hari_ini' => $aovToday,
            'completion_rate' => round($completionRate, 1),
            'pesanan_baru' => Order::where('canteen_id', $canteen->id)->where('status', 'menunggu')->count(),
            'sedang_dimasak' => Order::where('canteen_id', $canteen->id)->where('status', 'dimasak')->count(),
            'siap_pickup' => Order::where('canteen_id', $canteen->id)->where('status', 'siap_diambil')->count(),
            'pesanan_batal' => Order::where('canteen_id', $canteen->id)->where('status', 'dibatalkan')->count(),
            'menu_habis' => Menu::where('canteen_id', $canteen->id)->where(function ($query) {
                $query->where('stock', '<=', 0)->orWhere('is_available', false);
            })->count(),
        ];

        // 1. Tren Pendapatan & Jumlah Transaksi Kantin (7 Hari Terakhir).
        $currentStart = now()->subDays(6)->startOfDay();

        $revenueTrendRaw = Order::where('canteen_id', $canteen->id)
            ->where('status', 'selesai')
            ->where('created_at', '>=', $currentStart)
            ->selectRaw('DATE(created_at) as date, SUM(total_price) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date')
            ->toArray();

        $ordersTrendRaw = Order::where('canteen_id', $canteen->id)
            ->where('status', 'selesai')
            ->where('created_at', '>=', $currentStart)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date')
            ->toArray();

        $trendDates = [];
        $trendRevenues = [];
        $trendOrders = [];
        for ($i = 6; $i >= 0; $i--) {
            $dateStr = now()->subDays($i)->format('Y-m-d');
            $trendDates[] = now()->subDays($i)->translatedFormat('d M');
            $trendRevenues[] = (float) ($revenueTrendRaw[$dateStr] ?? 0);
            $trendOrders[] = (int) ($ordersTrendRaw[$dateStr] ?? 0);
        }

        // 2. Grafik Donut Distribusi Status Seluruh Pesanan Kantin.
        $statusRaw = Order::where('canteen_id', $canteen->id)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $statusLabels = ['Menunggu', 'Dimasak', 'Siap Pickup', 'Selesai', 'Dibatalkan'];
        $statusSeries = [
            (int) ($statusRaw['menunggu'] ?? 0),
            (int) ($statusRaw['dimasak'] ?? 0),
            (int) ($statusRaw['siap_diambil'] ?? 0),
            (int) ($statusRaw['selesai'] ?? 0),
            (int) ($statusRaw['dibatalkan'] ?? 0),
        ];

        // 3. Diagram Bar 5 Menu Terlaris di Kantin Ini.
        $topMenusRaw = OrderItem::whereHas('order', function ($query) use ($canteen) {
            $query->where('canteen_id', $canteen->id)->where('status', 'selesai');
        })
            ->selectRaw('menu_id, SUM(qty) as total_qty')
            ->groupBy('menu_id')
            ->with('menu:id,name')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        $topMenuLabels = [];
        $topMenuSeries = [];
        foreach ($topMenusRaw as $item) {
            $topMenuLabels[] = $item->menu ? $item->menu->name : 'Menu Terhapus';
            $topMenuSeries[] = (int) $item->total_qty;
        }

        // 4. Rating Rata-rata dan Ulasan Khusus untuk Kantin Ini.
        $avgRating = Review::whereHas('menu', function ($q) use ($canteen) {
            $q->where('canteen_id', $canteen->id);
        })->avg('rating') ?? 0;

        $totalReviews = Review::whereHas('menu', function ($q) use ($canteen) {
            $q->where('canteen_id', $canteen->id);
        })->count();

        $recentReviews = Review::whereHas('menu', function ($q) use ($canteen) {
            $q->where('canteen_id', $canteen->id);
        })->with(['user:id,name,avatar', 'menu:id,name'])
            ->latest()
            ->take(5)
            ->get();

        // 5. Grafik Distribusi Penjualan Menu Berdasarkan Kategori.
        $categoryDistRaw = OrderItem::whereHas('order', function ($q) use ($canteen) {
            $q->where('canteen_id', $canteen->id)->where('status', 'selesai');
        })
            ->join('menus', 'order_items.menu_id', '=', 'menus.id')
            ->selectRaw('menus.category, SUM(order_items.qty) as total_qty')
            ->whereNotNull('menus.category')
            ->where('menus.category', '!=', '')
            ->groupBy('menus.category')
            ->get();

        $categoryLabels = $categoryDistRaw->pluck('category')->toArray();
        $categorySeries = $categoryDistRaw->pluck('total_qty')->map(fn ($v) => (int) $v)->toArray();

        // 6. Umpan Pesanan Aktif (Active Orders Feed) untuk pemantauan antrean real-time di dapur.
        $activeOrders = Order::with('user:id,name')
            ->where('canteen_id', $canteen->id)
            ->whereIn('status', ['menunggu', 'dimasak', 'siap_diambil'])
            ->orderBy('created_at', 'asc')
            ->take(10)
            ->get();

        return view('vendor.dashboard', compact(
            'canteen',
            'stats',
            'trendDates',
            'trendRevenues',
            'trendOrders',
            'statusLabels',
            'statusSeries',
            'topMenuLabels',
            'topMenuSeries',
            'activeOrders',
            'avgRating',
            'totalReviews',
            'recentReviews',
            'categoryLabels',
            'categorySeries'
        ));
    }
}


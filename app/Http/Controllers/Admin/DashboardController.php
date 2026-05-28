<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Canteen;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Review;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $currentStart = now()->subDays(6)->startOfDay();
        $previousStart = now()->subDays(13)->startOfDay();
        $previousEnd = now()->subDays(7)->endOfDay();

        $currentRevenue = Order::where('status', 'selesai')->where('created_at', '>=', $currentStart)->sum('total_price');
        $previousRevenue = Order::where('status', 'selesai')->whereBetween('created_at', [$previousStart, $previousEnd])->sum('total_price');
        $revenueGrowth = $previousRevenue > 0 ? (($currentRevenue - $previousRevenue) / $previousRevenue) * 100 : ($currentRevenue > 0 ? 100 : 0);

        $currentOrders = Order::where('status', 'selesai')->where('created_at', '>=', $currentStart)->count();
        $previousOrders = Order::where('status', 'selesai')->whereBetween('created_at', [$previousStart, $previousEnd])->count();
        $ordersGrowth = $previousOrders > 0 ? (($currentOrders - $previousOrders) / $previousOrders) * 100 : ($currentOrders > 0 ? 100 : 0);

        $totalRevenue = Order::where('status', 'selesai')->sum('total_price');
        $totalOrders = Order::where('status', 'selesai')->count();
        $aov = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        $stats = [
            'total_pendapatan' => $totalRevenue,
            'pendapatan_growth' => round($revenueGrowth, 1),
            'volume_transaksi' => $totalOrders,
            'transaksi_growth' => round($ordersGrowth, 1),
            'aov' => $aov,
            'total_pengguna' => User::where('role', 'mahasiswa')->count(),
            'total_kantin' => Canteen::count(),
            'total_menu' => Menu::count(),
        ];

        // 1. Platform Revenue & Order Trend (Last 7 Days)
        $revenueTrendRaw = Order::where('status', 'selesai')
            ->where('created_at', '>=', $currentStart)
            ->selectRaw('DATE(created_at) as date, SUM(total_price) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date')
            ->toArray();

        $ordersTrendRaw = Order::where('status', 'selesai')
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

        // 2. Canteen Market Share (Donut Chart)
        $canteenSharesRaw = Order::where('status', 'selesai')
            ->selectRaw('canteen_id, SUM(total_price) as total_revenue')
            ->groupBy('canteen_id')
            ->with('canteen:id,name')
            ->orderByDesc('total_revenue')
            ->get();

        $shareLabels = [];
        $shareSeries = [];
        foreach ($canteenSharesRaw as $share) {
            $shareLabels[] = $share->canteen ? $share->canteen->name : 'Kantin Terhapus';
            $shareSeries[] = (float) $share->total_revenue;
        }

        // 3. Top Canteens Performance Table & Bar Chart
        $topCanteens = Canteen::withCount('orders')
            ->withCount(['orders as completed_orders_count' => function ($query) {
                $query->where('status', 'selesai');
            }])
            ->withSum(['orders as total_revenue' => function ($query) {
                $query->where('status', 'selesai');
            }], 'total_price')
            ->orderByDesc('total_revenue')
            ->limit(5)
            ->get();

        $topCanteenLabels = [];
        $topCanteenSeries = [];
        foreach ($topCanteens as $canteen) {
            $topCanteenLabels[] = $canteen->name;
            $topCanteenSeries[] = (float) $canteen->total_revenue;
        }

        // 4. Top 5 Best Selling Menus (Platform)
        $topMenusRaw = \App\Models\OrderItem::whereHas('order', function ($query) {
                $query->where('status', 'selesai');
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

        // 5. Recent Transactions Log
        $recentOrders = Order::with(['user:id,name', 'canteen:id,name'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // 6. Distribusi Penjualan per Kategori (Platform-wide)
        $categoryDistRaw = OrderItem::whereHas('order', function ($q) {
                $q->where('status', 'selesai');
            })
            ->join('menus', 'order_items.menu_id', '=', 'menus.id')
            ->selectRaw('menus.category, SUM(order_items.qty) as total_qty')
            ->whereNotNull('menus.category')
            ->where('menus.category', '!=', '')
            ->groupBy('menus.category')
            ->get();

        $categoryLabels = $categoryDistRaw->pluck('category')->toArray();
        $categorySeries = $categoryDistRaw->pluck('total_qty')->map(fn ($v) => (int) $v)->toArray();

        // 7. Rata-rata rating & total ulasan platform
        $platformAvgRating = round((float) (Review::avg('rating') ?? 0), 1);
        $totalReviews      = Review::count();
        $stats['avg_rating']    = $platformAvgRating;
        $stats['total_ulasan']  = $totalReviews;

        return view('admin.dashboard', compact(
            'stats',
            'trendDates',
            'trendRevenues',
            'trendOrders',
            'shareLabels',
            'shareSeries',
            'topCanteens',
            'topCanteenLabels',
            'topCanteenSeries',
            'topMenuLabels',
            'topMenuSeries',
            'recentOrders',
            'categoryLabels',
            'categorySeries'
        ));
    }
}

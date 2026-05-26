<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Canteen;
use App\Models\Menu;
use App\Models\Order;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total_pengguna' => User::where('role', 'mahasiswa')->count(),
            'total_kantin' => Canteen::count(),
            'total_order' => Order::count(),
            'total_transaksi' => Order::where('status', 'selesai')->sum('total_price'),
            'total_menu' => Menu::count(),
        ];

        // 1. Platform Revenue Trend (Last 7 Days)
        $revenueTrendRaw = Order::where('status', 'selesai')
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->selectRaw('DATE(created_at) as date, SUM(total_price) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date')
            ->toArray();

        $trendDates = [];
        $trendRevenues = [];
        for ($i = 6; $i >= 0; $i--) {
            $dateStr = now()->subDays($i)->format('Y-m-d');
            $trendDates[] = now()->subDays($i)->translatedFormat('d M');
            $trendRevenues[] = (float) ($revenueTrendRaw[$dateStr] ?? 0);
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

        // 3. Top Canteens Performance Table
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

        // 4. Recent Transactions Log
        $recentOrders = Order::with(['user:id,name', 'canteen:id,name'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'trendDates',
            'trendRevenues',
            'shareLabels',
            'shareSeries',
            'topCanteens',
            'recentOrders'
        ));
    }
}

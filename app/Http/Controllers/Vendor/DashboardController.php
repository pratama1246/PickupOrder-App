<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function toggleStatus(\Illuminate\Http\Request $request)
    {
        $canteen = Auth::user()->canteen;
        abort_if(is_null($canteen), 403, 'Akun vendor ini belum memiliki kantin terdaftar.');
        
        $isOpen = $request->has('is_open') ? $request->boolean('is_open') : !$canteen->is_open;
        $canteen->update(['is_open' => $isOpen]);
        
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'is_open' => $canteen->is_open,
                'message' => $canteen->is_open ? 'Kantin berhasil dibuka!' : 'Kantin telah ditutup.'
            ]);
        }
        
        return back()->with('success', $canteen->is_open ? 'Kantin berhasil dibuka!' : 'Kantin telah ditutup.');
    }

    public function index(): View
    {
        $canteen = Auth::user()->canteen;

        abort_if(is_null($canteen), 403, 'Akun vendor ini belum memiliki kantin terdaftar.');

        $stats = [
            'pesanan_baru' => Order::where('canteen_id', $canteen->id)->where('status', 'menunggu')->count(),
            'sedang_dimasak' => Order::where('canteen_id', $canteen->id)->where('status', 'dimasak')->count(),
            'siap_pickup' => Order::where('canteen_id', $canteen->id)->where('status', 'siap_diambil')->count(),
            'total_pendapatan' => Order::where('canteen_id', $canteen->id)->where('status', 'selesai')->sum('total_price'),
            'menu_habis' => $canteen->menus()->where(function ($q) {
                $q->where('stock', 0)->orWhere('is_available', false);
            })->count(),
        ];

        // 1. Canteen Revenue Trend (Last 7 Days)
        $revenueTrendRaw = Order::where('canteen_id', $canteen->id)
            ->where('status', 'selesai')
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

        // 2. Order Status Distribution (Donut Chart)
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

        // 3. Top 5 Best Selling Menus (Bar Chart)
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

        // 4. Active Orders Feed
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
            'statusLabels',
            'statusSeries',
            'topMenuLabels',
            'topMenuSeries',
            'activeOrders'
        ));
    }
}

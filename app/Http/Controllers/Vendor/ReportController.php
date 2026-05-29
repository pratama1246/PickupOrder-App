<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Tampilkan halaman laporan penjualan vendor.
     */
    public function index(Request $request): View
    {
        $canteen = Auth::user()->canteen;
        abort_if(! $canteen, 403, 'Anda tidak memiliki kantin.');

        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        // Query pesanan selesai pada rentang tanggal
        $ordersQuery = Order::where('canteen_id', $canteen->id)
            ->where('status', 'selesai')
            ->whereBetween('created_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay(),
            ]);

        $totalOrders = $ordersQuery->count();
        $totalRevenue = $ordersQuery->sum('total_price');
        $averageOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        // Query produk terlaris
        $topMenus = OrderItem::select('menu_id', DB::raw('SUM(qty) as total_qty'), DB::raw('SUM(qty * price) as total_sales'))
            ->whereHas('order', function ($q) use ($canteen, $startDate, $endDate) {
                $q->where('canteen_id', $canteen->id)
                    ->where('status', 'selesai')
                    ->whereBetween('created_at', [
                        Carbon::parse($startDate)->startOfDay(),
                        Carbon::parse($endDate)->endOfDay(),
                    ]);
            })
            ->with('menu')
            ->groupBy('menu_id')
            ->orderByDesc('total_qty')
            ->take(10)
            ->get();

        return view('vendor.laporan', compact(
            'canteen', 'startDate', 'endDate', 'totalOrders', 'totalRevenue', 'averageOrderValue', 'topMenus'
        ));
    }
}

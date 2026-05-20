<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Dashboard vendor: statistik pesanan dan pendapatan kantin (/vendor/dashboard).
     */
    public function index(): View
    {
        $canteen = Auth::user()->canteen;

        abort_if(is_null($canteen), 403, 'Akun vendor ini belum memiliki kantin terdaftar.');

        $stats = [
            'pesanan_baru' => Order::where('canteen_id', $canteen->id)
                ->where('status', 'menunggu')
                ->count(),
            'sedang_dimasak' => Order::where('canteen_id', $canteen->id)
                ->where('status', 'dimasak')
                ->count(),
            'siap_pickup' => Order::where('canteen_id', $canteen->id)
                ->where('status', 'siap_diambil')
                ->count(),
            'total_pendapatan' => Order::where('canteen_id', $canteen->id)
                ->where('status', 'selesai')
                ->sum('total_price'),
            'menu_habis' => $canteen->menus()
                ->where(function ($q) {
                    $q->where('stock', 0)
                        ->orWhere('is_available', false);
                })
                ->count(),
        ];

        return view('vendor.dashboard', compact('canteen', 'stats'));
    }
}

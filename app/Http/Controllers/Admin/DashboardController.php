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
    /**
     * Dashboard admin: statistik global seluruh sistem (/admin/dashboard).
     */
    public function index(): View
    {
        $stats = [
            'total_pengguna' => User::where('role', 'mahasiswa')->count(),
            'total_kantin' => Canteen::count(),
            'total_order' => Order::count(),
            'total_transaksi' => Order::where('status', 'selesai')->sum('total_price'),
            'total_menu' => Menu::count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}

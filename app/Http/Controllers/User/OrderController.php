<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * Daftar riwayat pesanan mahasiswa (/riwayat).
     * Mendukung filter berdasarkan status badge.
     */
    public function index(Request $request): View
    {
        $query = Order::with(['canteen', 'items.menu'])
            ->where('user_id', Auth::id())
            ->latest();

        // Filter berdasarkan status label UI (Menunggu, Diproses, Selesai, Dibatalkan)
        if ($request->filled('status')) {
            $dbStatuses = match ($request->status) {
                'Menunggu' => ['menunggu'],
                'Diproses' => ['dimasak', 'siap_diambil'],
                'Selesai' => ['selesai'],
                'Dibatalkan' => ['dibatalkan'],
                default => [],
            };
            if (! empty($dbStatuses)) {
                $query->whereIn('status', $dbStatuses);
            }
        }

        $orders = $query->paginate(10)->withQueryString();

        return view('user.riwayat', compact('orders'));
    }

    /**
     * Detail satu pesanan dari riwayat (/riwayat/{id}).
     */
    public function show(int $id): View
    {
        $order = Order::with(['canteen', 'items.menu'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return view('user.order-detail', compact('order'));
    }

}

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

    /**
     * API endpoint untuk polling status pembayaran dari frontend JavaScript.
     * GET /api/order/{id}/payment-status
     * Hanya mengembalikan status, tidak me-render view.
     */
    public function paymentStatus(int $id): \Illuminate\Http\JsonResponse
    {
        $order = Order::where('user_id', Auth::id())->findOrFail($id);

        return response()->json([
            'payment_status' => $order->payment_status,
            'status'         => $order->status,
            'is_paid'        => $order->isPaid(),
        ]);
    }

    /**
     * Batalkan pesanan oleh mahasiswa.
     * Hanya diizinkan jika belum dibayar (payment_status == 'pending') dan status masih 'menunggu'.
     */
    public function destroy(int $id): \Illuminate\Http\RedirectResponse
    {
        $order = Order::where('user_id', Auth::id())->findOrFail($id);

        if ($order->payment_status !== 'pending') {
            return back()->with('error', 'Pesanan yang sudah dibayar tidak dapat dibatalkan.');
        }

        if ($order->status !== 'menunggu') {
            return back()->with('error', 'Pesanan yang sedang/sudah diproses tidak dapat dibatalkan.');
        }

        $order->update([
            'status' => 'dibatalkan',
            'payment_status' => 'failed',
        ]);

        return redirect()->route('order.index')->with('success', 'Pesanan #' . $order->order_code . ' berhasil dibatalkan.');
    }

}

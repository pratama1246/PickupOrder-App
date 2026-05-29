<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * Daftar riwayat pesanan mahasiswa (/riwayat).
     * Pesanan online yang masih pending dikelompokkan per payment_code (1 kontainer).
     * Pesanan lainnya (sudah lunas atau tunai) ditampilkan per-item.
     */
    public function index(Request $request): View
    {
        $userId = Auth::id();

        // Ambil semua pesanan online yang masih pending milik user ini
        // Kelompokkan per payment_code agar tampil sebagai 1 kontainer di riwayat
        $pendingOnlineGroups = Order::with(['canteen', 'items.menu'])
            ->where('user_id', $userId)
            ->where('payment_method', 'midtrans')
            ->where('payment_status', 'pending')
            ->whereNotIn('status', ['dibatalkan'])
            ->orderByDesc('created_at')
            ->get()
            ->groupBy('payment_code');

        // Ambil pesanan selain pending online (tunai atau sudah dibayar)
        $query = Order::with(['canteen', 'items.menu'])
            ->where('user_id', $userId)
            ->where(function ($q) {
                $q->where('payment_method', '!=', 'midtrans')
                    ->orWhere('payment_status', '!=', 'pending');
            })
            ->latest();

        // Filter berdasarkan status label UI
        if ($request->filled('status')) {
            $dbStatuses = match ($request->status) {
                'Menunggu' => ['menunggu'],
                'Diproses' => ['dimasak'],
                'Siap Diambil' => ['siap_diambil'],
                'Selesai' => ['selesai'],
                'Dibatalkan' => ['dibatalkan'],
                default => [],
            };
            if (! empty($dbStatuses)) {
                $query->whereIn('status', $dbStatuses);
            }
        }

        $orders = $query->paginate(10)->withQueryString();

        return view('user.riwayat', compact('orders', 'pendingOnlineGroups'));
    }

    /**
     * Detail satu pesanan dari riwayat (/riwayat/{id}).
     */
    public function show(int $id): View
    {
        $order = Order::with(['canteen', 'items.menu', 'reviews.menu'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return view('user.order-detail', compact('order'));
    }

    /**
     * API endpoint untuk polling status pembayaran dari frontend JavaScript.
     * GET /api/order/{id}/payment-status
     */
    public function paymentStatus(int $id): JsonResponse
    {
        $order = Order::where('user_id', Auth::id())->findOrFail($id);

        return response()->json([
            'payment_status' => $order->payment_status,
            'status' => $order->status,
            'is_paid' => $order->isPaid(),
        ]);
    }

    /**
     * Batalkan satu pesanan oleh mahasiswa.
     * Hanya diizinkan jika belum dibayar (payment_status == 'pending') dan status 'menunggu'.
     */
    public function destroy(int $id): RedirectResponse
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

        return redirect()->route('order.index')->with('success', 'Pesanan #'.$order->order_code.' berhasil dibatalkan.');
    }

    /**
     * Batalkan seluruh grup transaksi berdasarkan payment_code.
     * Digunakan ketika user menekan "Batalkan Semua" pada kontainer grouped pending order.
     */
    public function cancelGroup(string $paymentCode): RedirectResponse
    {
        $orders = Order::where('user_id', Auth::id())
            ->where('payment_code', $paymentCode)
            ->where('payment_status', 'pending')
            ->whereNotIn('status', ['dibatalkan'])
            ->get();

        if ($orders->isEmpty()) {
            return redirect()->route('order.index')->with('error', 'Transaksi tidak ditemukan atau sudah dibatalkan.');
        }

        foreach ($orders as $order) {
            $order->update([
                'status' => 'dibatalkan',
                'payment_status' => 'failed',
            ]);
        }

        return redirect()->route('order.index')->with('success', 'Seluruh transaksi dengan kode '.$paymentCode.' berhasil dibatalkan.');
    }
}

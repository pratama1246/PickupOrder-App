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
     * Menampilkan daftar riwayat transaksi kuliner mahasiswa (/history).
     * Memisahkan pesanan online yang belum lunas (pending) untuk dikelompokkan berdasarkan payment_code,
     * sehingga pesanan dari multi-kantin yang dibayar sekaligus tetap tampil dalam satu kartu tagihan di UI.
     */
    public function index(Request $request): View
    {
        $userId = Auth::id();

        // Mengambil pesanan online yang belum diselesaikan pembayarannya.
        // Dikelompokkan per payment_code agar siswa dapat melunasi seluruh keranjang belanja dalam satu klik pembayaran.
        $pendingOnlineGroups = Order::with(['canteen', 'items.menu'])
            ->where('user_id', $userId)
            ->where('payment_method', 'midtrans')
            ->where('payment_status', 'pending')
            ->whereNotIn('status', ['dibatalkan'])
            ->orderByDesc('created_at')
            ->get()
            ->groupBy('payment_code');

        // Mengambil transaksi yang sudah dibayar atau menggunakan metode tunai untuk dirender biasa per item.
        $query = Order::with(['canteen', 'items.menu'])
            ->where('user_id', $userId)
            ->where(function ($q) {
                $q->where('payment_method', '!=', 'midtrans')
                    ->orWhere('payment_status', '!=', 'pending');
            })
            ->latest();

        // Pemetaan label status antarmuka pengguna (UI) ke kolom status database yang sesuai.
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

        return view('user.history', compact('orders', 'pendingOnlineGroups'));
    }

    /**
     * Menampilkan rincian struk belanja digital per pesanan (/history/{id}).
     */
    public function show(int $id): View
    {
        $order = Order::with(['canteen', 'items.menu', 'reviews.menu'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return view('user.order-detail', compact('order'));
    }

    /**
     * API Endpoint untuk memantau (polling) perubahan status pembayaran secara asinkron dari JS frontend.
     * Mengeliminasi keharusan pengguna melakukan refresh halaman manual pasca pembayaran Midtrans berhasil.
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
     * Membatalkan satu pesanan oleh mahasiswa secara sepihak.
     * Dibatasi ketat hanya untuk pesanan yang belum lunas (pending) dan belum mulai diolah oleh vendor (menunggu).
     */
    public function destroy(int $id): RedirectResponse
    {
        $order = Order::where('user_id', Auth::id())->findOrFail($id);

        // Mencegah kerugian finansial/stok bahan mentah kantin dengan memblokir pembatalan menu yang sudah dimasak/dibayar.
        if ($order->payment_status !== 'pending') {
            return back()->with('error', 'Pesanan yang sudah dibayar tidak dapat dibatalkan.');
        }

        if ($order->status !== 'menunggu') {
            return back()->with('error', 'Pesanan yang sedang/sudah diproses tidak dapat dibatalkan.');
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($order) {
            $order->update([
                'status' => 'dibatalkan',
                'payment_status' => 'failed',
            ]);

            foreach ($order->items as $item) {
                if ($item->menu) {
                    $item->menu->increment('stock', $item->qty);
                }
            }
        });

        return redirect()->route('order.index')->with('success', 'Pesanan #'.$order->order_code.' berhasil dibatalkan.');
    }

    /**
     * Membatalkan seluruh paket pesanan multi-kantin yang tergabung dalam satu kode pembayaran pending.
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

        \Illuminate\Support\Facades\DB::transaction(function () use ($orders) {
            foreach ($orders as $order) {
                $order->update([
                    'status' => 'dibatalkan',
                    'payment_status' => 'failed',
                ]);

                foreach ($order->items as $item) {
                    if ($item->menu) {
                        $item->menu->increment('stock', $item->qty);
                    }
                }
            }
        });

        return redirect()->route('order.index')->with('success', 'Seluruh transaksi dengan kode '.$paymentCode.' berhasil dibatalkan.');
    }
}

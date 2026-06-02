<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * Menampilkan daftar antrean transaksi kuliner masuk khusus untuk kantin vendor (/vendor/transaksi).
     * Menerapkan filter status pesanan untuk memantau progress pengerjaan dapur.
     */
    public function index(Request $request): View
    {
        $canteen = Auth::user()->canteen;

        // Membatasi transaksi hanya untuk kantin vendor yang masuk (tenant isolation).
        $query = Order::with(['user', 'items.menu'])
            ->where('canteen_id', $canteen->id)
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->paginate(15)->withQueryString();

        return view('vendor.order', compact('orders', 'canteen'));
    }

    /**
     * Menampilkan rincian pesanan kuliner mahasiswa (/vendor/transaksi/{id}).
     */
    public function show(int $id): View
    {
        $canteen = Auth::user()->canteen;

        $order = Order::with(['user', 'items.menu'])
            ->where('canteen_id', $canteen->id)
            ->findOrFail($id);

        return view('vendor.order-detail', compact('order', 'canteen'));
    }

    /**
     * Memproses pencarian kode pesanan (order_code) baik dari input manual maupun hasil scan QR code.
     * Menggunakan pencarian berbasis akhiran (LIKE %code) agar vendor cukup mengetik 4-6 karakter terakhir
     * dari kode pesanan untuk memverifikasi pelanggan.
     */
    public function scan(string $code): RedirectResponse
    {
        $canteen = Auth::user()->canteen;

        $order = Order::where('canteen_id', $canteen->id)
            ->where('order_code', 'LIKE', '%'.strtoupper($code))
            ->first();

        if (! $order) {
            return redirect()->route('vendor.order.index')->with('error', 'Pesanan dengan kode tersebut tidak ditemukan atau bukan milik kantin Anda.');
        }

        return redirect()->route('vendor.order.show', $order->id)->with('success', 'Pesanan ditemukan.');
    }

    /**
     * Mengubah status kemajuan (progress) pesanan dalam siklus persiapan makanan.
     * Alur linear: menunggu (pending) -> dimasak (cooking) -> siap_diambil (ready) -> selesai (picked up).
     * Otomatis melunasi status pembayaran pesanan tunai (cash) begitu status pesanan diubah ke 'selesai'.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $canteen = Auth::user()->canteen;

        $order = Order::where('canteen_id', $canteen->id)->findOrFail($id);

        // Penanganan Aksi Verifikasi Pembayaran QRIS Manual
        if ($request->input('action_type') === 'confirm_payment') {
            abort_if($order->payment_method !== 'qris_manual' || $order->payment_status !== 'pending', 422, 'Pesanan tidak memerlukan konfirmasi pembayaran.');
            
            $order->update([
                'payment_status' => 'paid',
                'status' => 'dimasak', // Langsung masuk tahap dimasak setelah dibayar
            ]);

            return back()->with('success', "Pembayaran untuk pesanan #{$order->order_code} berhasil dikonfirmasi!");
        }

        if ($request->input('action_type') === 'reject_payment') {
            abort_if($order->payment_method !== 'qris_manual' || $order->payment_status !== 'pending', 422, 'Pesanan tidak memerlukan verifikasi pembayaran.');

            \Illuminate\Support\Facades\DB::transaction(function () use ($order) {
                $order->update([
                    'payment_status' => 'failed',
                    'status' => 'dibatalkan',
                ]);

                // Kembalikan stok menu karena pesanan dibatalkan
                foreach ($order->items as $item) {
                    if ($item->menu) {
                        $item->menu->increment('stock', $item->qty);
                    }
                }
            });

            return back()->with('success', "Pembayaran ditolak. Pesanan #{$order->order_code} berhasil dibatalkan.");
        }

        // Aturan transisi linear status pesanan makanan.
        $nextStatus = match ($order->status) {
            'menunggu' => 'dimasak',
            'dimasak' => 'siap_diambil',
            'siap_diambil' => 'selesai',
            default => null,
        };

        abort_if(is_null($nextStatus), 422, 'Status pesanan tidak dapat diubah lagi.');

        $updateData = ['status' => $nextStatus];

        // Sinkronisasi Pembayaran Tunai: Pada metode cash, uang diterima langsung saat serah terima
        // makanan di outlet. Maka, menandai order 'selesai' sekaligus mengubah payment_status menjadi 'paid'.
        if ($nextStatus === 'selesai' && $order->payment_method === 'cash') {
            $updateData['payment_status'] = 'paid';
        }

        $order->update($updateData);

        return back()->with('success', "Status pesanan #{$order->order_code} diperbarui.");
    }

    /**
     * Membatalkan pesanan masuk oleh pemilik kantin (misal bahan baku habis).
     * Membatasi pembatalan hanya ketika pesanan masih dalam status 'menunggu' atau 'dimasak'.
     */
    public function destroy(int $id): RedirectResponse
    {
        $canteen = Auth::user()->canteen;

        // Membatasi pembatalan agar tidak dapat membatalkan pesanan yang sudah siap diambil atau selesai.
        $order = Order::where('canteen_id', $canteen->id)
            ->whereIn('status', ['menunggu', 'dimasak'])
            ->findOrFail($id);

        \Illuminate\Support\Facades\DB::transaction(function () use ($order) {
            $order->update(['status' => 'dibatalkan']);

            foreach ($order->items as $item) {
                if ($item->menu) {
                    $item->menu->increment('stock', $item->qty);
                }
            }
        });

        return back()->with('success', "Pesanan #{$order->order_code} dibatalkan.");
    }
}

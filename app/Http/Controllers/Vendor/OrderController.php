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
     * Daftar semua transaksi masuk ke kantin vendor (/vendor/transaksi).
     * Mendukung filter berdasarkan status.
     */
    public function index(Request $request): View
    {
        $canteen = Auth::user()->canteen;

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
     * Detail satu transaksi (/vendor/transaksi/{id}).
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
     * Proses kode pesanan dari pemindai QR atau input manual.
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
     * Update status pesanan oleh vendor (Ubah Status).
     * Alur maju: menunggu -> dimasak -> siap_diambil -> selesai
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $canteen = Auth::user()->canteen;

        $order = Order::where('canteen_id', $canteen->id)->findOrFail($id);

        $nextStatus = match ($order->status) {
            'menunggu' => 'dimasak',
            'dimasak' => 'siap_diambil',
            'siap_diambil' => 'selesai',
            default => null,
        };

        abort_if(is_null($nextStatus), 422, 'Status pesanan tidak dapat diubah lagi.');

        $updateData = ['status' => $nextStatus];

        // Jika pesanan tunai mencapai 'selesai', tandai sebagai lunas
        // karena pembayaran tunai diterima saat makanan diserahkan ke mahasiswa
        if ($nextStatus === 'selesai' && $order->payment_method === 'cash') {
            $updateData['payment_status'] = 'paid';
        }

        $order->update($updateData);

        return back()->with('success', "Status pesanan #{$order->order_code} diperbarui.");
    }

    /**
     * Batalkan pesanan oleh vendor.
     */
    public function destroy(int $id): RedirectResponse
    {
        $canteen = Auth::user()->canteen;

        $order = Order::where('canteen_id', $canteen->id)
            ->whereIn('status', ['menunggu', 'dimasak'])
            ->findOrFail($id);

        $order->update(['status' => 'dibatalkan']);

        return back()->with('success', "Pesanan #{$order->order_code} dibatalkan.");
    }
}

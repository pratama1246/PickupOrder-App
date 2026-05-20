<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    private const SESSION_KEY = 'cart';

    /**
     * Tampilkan halaman checkout (/checkout).
     * Berisi time slot picker, payment method picker, dan ringkasan order.
     */
    public function index(): View
    {
        $cart = session(self::SESSION_KEY, []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Keranjang belanja kosong.');
        }

        // Kelompokkan per kantin untuk ditampilkan di kolom kanan
        $grouped = [];
        foreach ($cart as $item) {
            $grouped[$item['canteen_id']]['canteen_name'] = $item['canteen_name'];
            $grouped[$item['canteen_id']]['items'][] = $item;
        }

        $total = array_sum(array_column($cart, 'subtotal'));

        return view('user.checkout', compact('grouped', 'total'));
    }

    /**
     * Proses checkout: validasi, simpan ke DB, kosongkan keranjang, redirect ke antrian.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'pickup_time' => ['required', 'date', 'after:now'],
            'payment_method' => ['required', 'in:qris,bayar_di_warung'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $cart = session(self::SESSION_KEY, []);
        abort_if(empty($cart), 422, 'Keranjang belanja kosong.');

        // Kelompokkan per kantin karena satu checkout bisa banyak kantin
        $grouped = [];
        foreach ($cart as $item) {
            $grouped[$item['canteen_id']][] = $item;
        }

        $lastOrder = null;

        DB::transaction(function () use ($grouped, $request, &$lastOrder) {
            foreach ($grouped as $canteenId => $items) {
                $total = array_sum(array_column($items, 'subtotal'));

                $order = Order::create([
                    'user_id' => Auth::id(),
                    'canteen_id' => $canteenId,
                    'status' => 'menunggu',
                    'pickup_time' => $request->pickup_time,
                    'total_price' => $total,
                    'notes' => $request->notes,
                ]);

                foreach ($items as $item) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'menu_id' => $item['menu_id'],
                        'qty' => $item['quantity'],
                        'price' => $item['price'],
                    ]);
                }

                $lastOrder = $order;
            }
        });

        // Kosongkan keranjang setelah berhasil checkout
        session()->forget(self::SESSION_KEY);

        return redirect()->route('order.queue', $lastOrder->id)
            ->with('success', 'Pesanan berhasil dibuat!');
    }
}

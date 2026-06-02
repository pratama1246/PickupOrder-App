<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    // Kunci session untuk menyimpan data keranjang belanja secara lokal per sesi pengguna.
    // Menghindari penulisan database berlebih untuk item yang bersifat temporer/cepat berubah.
    private const SESSION_KEY = 'cart';

    /**
     * Menampilkan isi keranjang belanja pengguna (/cart).
     * Melakukan sinkronisasi data harga dan stok riil dari database terlebih dahulu sebelum dirender
     * guna menghindari pemesanan dengan harga usang (stale prices).
     */
    public function index(): View
    {
        $cart = session(self::SESSION_KEY, []);
        $cart = $this->syncCartWithMenus($cart);

        session([self::SESSION_KEY => $cart]);

        $grouped = $this->groupByCanteen($cart);
        $total = array_sum(array_column($cart, 'subtotal'));

        return view('user.cart', compact('grouped', 'total'));
    }

    /**
     * Menambahkan item menu makanan ke dalam keranjang belanja.
     * Melakukan verifikasi ketersediaan stok fisik dan status aktif operasional kantin induk.
     */
    public function store(Request $request)
    {
        $request->validate([
            'menu_id' => ['required', 'integer', 'exists:menus,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:20'],
            'force' => ['nullable', 'boolean'],
        ]);

        $menu = Menu::with('canteen')->findOrFail($request->menu_id);

        // Validasi ketersediaan barang secara real-time untuk menghindari kegagalan proses di sisi vendor.
        abort_if(! $menu->isInStock(), 422, 'Menu tidak tersedia saat ini.');
        abort_if(! $menu->canteen || ! $menu->canteen->is_open, 422, 'Kantin sedang tutup.');

        $cart = session(self::SESSION_KEY, []);

        $key = $request->menu_id;

        if (isset($cart[$key])) {
            $cart[$key]['quantity'] += $request->quantity;
        } else {
            $cart[$key] = [
                'menu_id' => $menu->id,
                'name' => $menu->name,
                'image' => $menu->image,
                'description' => $menu->description,
                'price' => (float) $menu->price,
                'canteen_id' => $menu->canteen_id,
                'canteen_name' => $menu->canteen->name,
                'quantity' => $request->quantity,
            ];
        }

        $cart[$key]['subtotal'] = $cart[$key]['price'] * $cart[$key]['quantity'];

        session([self::SESSION_KEY => $cart]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "{$menu->name} berhasil ditambahkan ke keranjang.",
                'cart' => $cart,
            ]);
        }

        return back()->with('success', "{$menu->name} ditambahkan ke keranjang.");
    }

    /**
     * Memperbarui kuantitas item dalam keranjang belanja.
     * Menerima request AJAX (Wants JSON) untuk memodifikasi keranjang tanpa memicu muat ulang halaman.
     */
    public function update(Request $request, int $menuId)
    {
        $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:20'],
        ]);

        $cart = session(self::SESSION_KEY, []);

        if (isset($cart[$menuId])) {
            $cart[$menuId]['quantity'] = $request->quantity;
            $cart[$menuId]['subtotal'] = $cart[$menuId]['price'] * $request->quantity;
            session([self::SESSION_KEY => $cart]);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Jumlah item diperbarui.',
                'cart' => $cart,
            ]);
        }

        return back()->with('success', 'Jumlah item diperbarui.');
    }

    /**
     * Menghapus satu baris item makanan dari daftar keranjang belanja.
     * Mendukung pemrosesan asinkron untuk interaktivitas tombol hapus di UI.
     */
    public function destroy(Request $request, int $menuId)
    {
        $cart = session(self::SESSION_KEY, []);
        unset($cart[$menuId]);
        session([self::SESSION_KEY => $cart]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Item dihapus dari keranjang.',
                'cart' => $cart,
            ]);
        }

        return back()->with('success', 'Item dihapus dari keranjang.');
    }

    /**
     * Menghapus semua item menu makanan dari daftar keranjang belanja yang memiliki stok habis (0).
     */
    public function clearOutOfStock(): RedirectResponse
    {
        $cart = session(self::SESSION_KEY, []);

        if (!empty($cart)) {
            $menus = Menu::whereIn('id', array_keys($cart))->get()->keyBy('id');
            foreach ($cart as $menuId => $item) {
                $menu = $menus->get($menuId);
                if (!$menu || !$menu->isInStock()) {
                    unset($cart[$menuId]);
                }
            }
            session([self::SESSION_KEY => $cart]);
        }

        return back()->with('success', 'Semua menu habis berhasil dibersihkan.');
    }

    /**
     * Menyalin kembali item dari riwayat transaksi lama pengguna ke keranjang belanja (fitur "Beli Lagi").
     * Menyaring menu yang sudah tidak lagi dijual oleh pemilik kantin agar tidak menimbulkan error harga.
     */
    public function reorder(Request $request, int $id): RedirectResponse
    {
        $order = Order::with('items.menu.canteen')
            ->where('user_id', $request->user()->id)
            ->findOrFail($id);

        // Muat keranjang belanja yang sudah ada
        $cart = session(self::SESSION_KEY, []);
        $addedCount = 0;
        $skippedCount = 0;

        foreach ($order->items as $item) {
            $menu = $item->menu;

            // Melewati item jika menu telah dihapus atau persediaan fisiknya sedang kosong.
            if (! $menu || ! $menu->isInStock()) {
                $skippedCount++;
                continue;
            }

            $key = $menu->id;
            if (isset($cart[$key])) {
                $cart[$key]['quantity'] += $item->qty;
            } else {
                $cart[$key] = [
                    'menu_id' => $menu->id,
                    'name' => $menu->name,
                    'image' => $menu->image,
                    'description' => $menu->description,
                    'price' => (float) $menu->price,
                    'canteen_id' => $menu->canteen_id,
                    'canteen_name' => $menu->canteen->name,
                    'quantity' => $item->qty,
                ];
            }

            $cart[$key]['subtotal'] = $cart[$key]['price'] * $cart[$key]['quantity'];
            $addedCount++;
        }

        session([self::SESSION_KEY => $cart]);

        if ($addedCount === 0) {
            return redirect()->route('cart.index')
                ->with('error', 'Semua menu dari pesanan ini sudah tidak tersedia.');
        }

        $message = "{$addedCount} menu berhasil ditambahkan ke keranjang.";
        if ($skippedCount > 0) {
            $message .= " {$skippedCount} menu dilewati karena tidak tersedia.";
        }

        return redirect()->route('cart.index')->with('success', $message);
    }

    /**
     * Mengelompokkan item belanja berdasarkan ID Kantin agar dapat dirender terpisah dalam sub-kontainer di antarmuka.
     */
    private function groupByCanteen(array $cart): array
    {
        $grouped = [];
        foreach ($cart as $item) {
            $grouped[$item['canteen_id']]['canteen_name'] = $item['canteen_name'];
            $grouped[$item['canteen_id']]['items'][] = $item;
        }

        return $grouped;
    }

    /**
     * Melakukan kueri ulang ke database untuk memperbarui detail nama, gambar, dan nominal harga teranyar menu.
     * Berfungsi menghapus item keranjang jika data menu aslinya telah dihapus dari sistem.
     */
    private function syncCartWithMenus(array $cart): array
    {
        if (empty($cart)) {
            return $cart;
        }

        $menus = Menu::with('canteen')
            ->whereIn('id', array_keys($cart))
            ->get()
            ->keyBy('id');

        foreach ($cart as $menuId => $item) {
            $menu = $menus->get($menuId);

            if (! $menu) {
                unset($cart[$menuId]);

                continue;
            }

            $cart[$menuId]['name'] = $menu->name;
            $cart[$menuId]['image'] = $menu->image;
            $cart[$menuId]['description'] = $menu->description;
            $cart[$menuId]['price'] = (float) $menu->price;
            $cart[$menuId]['canteen_id'] = $menu->canteen_id;
            $cart[$menuId]['canteen_name'] = $menu->canteen->name;
            $cart[$menuId]['stock'] = $menu->stock;
            $cart[$menuId]['subtotal'] = $cart[$menuId]['price'] * $cart[$menuId]['quantity'];
        }

        return $cart;
    }
}

<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    private const SESSION_KEY = 'cart';

    /**
     * Tampilkan isi keranjang belanja (/keranjang).
     * Data keranjang disimpan di session, dikelompokkan per kantin.
     */
    public function index(): View
    {
        $cart = session(self::SESSION_KEY, []);
        $cart = $this->syncCartWithMenus($cart);

        session([self::SESSION_KEY => $cart]);

        $grouped = $this->groupByCanteen($cart);
        $total = array_sum(array_column($cart, 'subtotal'));

        return view('user.keranjang', compact('grouped', 'total'));
    }

    /**
     * Tambah item menu ke keranjang.
     * Jika sudah ada, tambahkan qty-nya.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'menu_id' => ['required', 'integer', 'exists:menus,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:20'],
        ]);

        $menu = Menu::with('canteen')->findOrFail($request->menu_id);

        abort_if(! $menu->isInStock(), 422, 'Menu tidak tersedia saat ini.');

        $cart = session(self::SESSION_KEY, []);
        $key = $request->menu_id;

        if (isset($cart[$key])) {
            $cart[$key]['quantity'] += $request->quantity;
        } else {
            $cart[$key] = [
                'menu_id' => $menu->id,
                'name' => $menu->name,
                'image' => $menu->image,
                'price' => (float) $menu->price,
                'canteen_id' => $menu->canteen_id,
                'canteen_name' => $menu->canteen->name,
                'quantity' => $request->quantity,
            ];
        }

        $cart[$key]['subtotal'] = $cart[$key]['price'] * $cart[$key]['quantity'];

        session([self::SESSION_KEY => $cart]);

        return back()->with('success', "{$menu->name} ditambahkan ke keranjang.");
    }

    /**
     * Update qty satu item di keranjang.
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
                'cart'    => $cart,
            ]);
        }

        return back()->with('success', 'Jumlah item diperbarui.');
    }

    /**
     * Hapus satu item dari keranjang.
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
                'cart'    => $cart,
            ]);
        }

        return back()->with('success', 'Item dihapus dari keranjang.');
    }

    /**
     * Kelompokkan item keranjang berdasarkan kantin.
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
     * Sinkronkan data keranjang dengan harga menu terbaru.
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
            $cart[$menuId]['price'] = (float) $menu->price;
            $cart[$menuId]['canteen_id'] = $menu->canteen_id;
            $cart[$menuId]['canteen_name'] = $menu->canteen->name;
            $cart[$menuId]['subtotal'] = $cart[$menuId]['price'] * $cart[$menuId]['quantity'];
        }

        return $cart;
    }
}

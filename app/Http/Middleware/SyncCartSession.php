<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CartItem;
use App\Models\Menu;
use Symfony\Component\HttpFoundation\Response;

class SyncCartSession
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user && $user->isMahasiswa()) {
            $sessionCart = session('cart', []);
            $dbCount = CartItem::where('user_id', $user->id)->count();

            // 1. If database is empty but session is not, populate database first (e.g. for testing)
            if ($dbCount === 0 && !empty($sessionCart)) {
                foreach ($sessionCart as $menuId => $item) {
                    CartItem::updateOrCreate(
                        ['user_id' => $user->id, 'menu_id' => $menuId],
                        ['quantity' => $item['quantity']]
                    );
                }
            }

            // 2. Load latest cart state from the database
            $cartItems = CartItem::where('user_id', $user->id)->get();
            $cart = [];

            if ($cartItems->isNotEmpty()) {
                $menuIds = $cartItems->pluck('menu_id')->toArray();
                $menus = Menu::with('canteen')->whereIn('id', $menuIds)->get()->keyBy('id');

                foreach ($cartItems as $item) {
                    $menu = $menus->get($item->menu_id);
                    if ($menu) {
                        $cart[$item->menu_id] = [
                            'menu_id' => $menu->id,
                            'name' => $menu->name,
                            'image' => $menu->image,
                            'description' => $menu->description,
                            'price' => (float) $menu->price,
                            'canteen_id' => $menu->canteen_id,
                            'canteen_name' => $menu->canteen->name,
                            'quantity' => $item->quantity,
                            'stock' => $menu->stock,
                            'subtotal' => (float) $menu->price * $item->quantity,
                        ];
                    }
                }
            }

            session(['cart' => $cart]);
        }

        $response = $next($request);

        // 3. Persist session back to database after the request is processed
        if ($user && $user->isMahasiswa()) {
            $cart = session('cart', []);

            // Remove database items that are no longer in the session
            CartItem::where('user_id', $user->id)
                ->whereNotIn('menu_id', array_keys($cart))
                ->delete();

            // Insert or update remaining items
            foreach ($cart as $menuId => $item) {
                CartItem::updateOrCreate(
                    ['user_id' => $user->id, 'menu_id' => $menuId],
                    ['quantity' => $item['quantity']]
                );
            }
        }

        return $response;
    }
}

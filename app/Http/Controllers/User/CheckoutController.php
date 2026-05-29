<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Midtrans\Config as MidtransConfig;
use Midtrans\Snap;

class CheckoutController extends Controller
{
    private const SESSION_KEY = 'cart';

    /**
     * Persiapkan checkout dari keranjang (menyimpan catatan ke session).
     */
    public function prepare(Request $request): RedirectResponse
    {
        $request->validate([
            'notes' => ['nullable', 'array'],
            'notes.*' => ['nullable', 'string', 'max:500'],
            'selected_menu_ids' => ['nullable', 'array'],
            'selected_menu_ids.*' => ['nullable', 'integer'],
        ]);

        session(['checkout_notes' => $request->input('notes', [])]);
        session(['checkout_selected_ids' => $request->input('selected_menu_ids', [])]);

        return redirect()->route('checkout.index');
    }

    /**
     * Tampilkan halaman checkout (/checkout).
     * Berisi time slot picker, payment method picker, dan ringkasan order.
     */
    public function index(): View
    {
        $cart = session(self::SESSION_KEY, []);
        $cart = $this->syncCartWithMenus($cart);
        session([self::SESSION_KEY => $cart]);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Keranjang belanja kosong.');
        }

        // Filter hanya item yang dipilih user dari keranjang
        $selectedIds = session('checkout_selected_ids', []);
        if (! empty($selectedIds)) {
            $selectedIds = array_map('intval', $selectedIds);
            $cart = array_filter($cart, fn ($item) => in_array((int) $item['menu_id'], $selectedIds));
        }

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Pilih setidaknya satu menu untuk checkout.');
        }

        // Kelompokkan per kantin untuk ditampilkan di kolom kanan
        $grouped = [];
        foreach ($cart as $item) {
            $grouped[$item['canteen_id']]['canteen_name'] = $item['canteen_name'];
            $grouped[$item['canteen_id']]['items'][] = $item;
        }

        $total = array_sum(array_column($cart, 'subtotal'));
        $notes = session('checkout_notes', []);

        return view('user.checkout', compact('grouped', 'total', 'notes'));
    }

    /**
     * Proses checkout: validasi, simpan ke DB, kosongkan keranjang.
     * Mendukung request AJAX (wantsJson) maupun request biasa.
     */
    public function store(Request $request)
    {
        $request->validate([
            'pickup_time' => ['required', 'string'],
            'custom_time' => ['nullable', 'required_if:pickup_time,custom', 'string', 'regex:/^\d{2}:\d{2}$/'],
            'payment_method' => ['required', 'in:qris,bayar_di_warung'],
            'notes' => ['nullable', 'array'],
            'notes.*' => ['nullable', 'string', 'max:500'],
        ]);

        $fullCart = $this->syncCartWithMenus(session(self::SESSION_KEY, []));
        session([self::SESSION_KEY => $fullCart]);

        // Filter hanya item yang dipilih untuk di-checkout
        $selectedIds = session('checkout_selected_ids', []);
        if (! empty($selectedIds)) {
            $selectedIds = array_map('intval', $selectedIds);
            $cart = array_filter($fullCart, fn ($item) => in_array((int) $item['menu_id'], $selectedIds));
        } else {
            $cart = $fullCart;
        }

        if (empty($cart)) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Tidak ada item yang dipilih untuk checkout.'], 422);
            }
            abort(422, 'Tidak ada item yang dipilih.');
        }

        // --- Parser pickup_time ---
        $pickupTime = $this->parsePickupTime($request->pickup_time, $request->custom_time);
        if (! $pickupTime) {
            $errorMsg = 'Waktu pengambilan tidak valid. Pastikan waktu yang dipilih belum terlewat.';
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'errors' => ['pickup_time' => [$errorMsg]]], 422);
            }

            return back()->withErrors(['pickup_time' => $errorMsg])->withInput();
        }

        // --- Proteksi spam untuk metode tunai ---
        if ($request->payment_method === 'bayar_di_warung') {
            $hasActiveCashOrder = Order::where('user_id', Auth::id())
                ->where('payment_method', 'cash')
                ->where('payment_status', 'pending')
                ->whereNotIn('status', ['selesai', 'dibatalkan'])
                ->exists();

            if ($hasActiveCashOrder) {
                $errorMsg = 'Anda masih memiliki pesanan dengan pembayaran di tempat yang belum selesai. Selesaikan pesanan tersebut terlebih dahulu atau gunakan pembayaran online.';
                if ($request->wantsJson()) {
                    return response()->json(['success' => false, 'errors' => ['payment_method' => [$errorMsg]]], 422);
                }

                return back()->withErrors(['payment_method' => $errorMsg])->withInput();
            }
        }

        // Kelompokkan per kantin
        $grouped = [];
        foreach ($cart as $item) {
            $grouped[$item['canteen_id']][] = $item;
        }

        $paymentMethod = $request->payment_method === 'qris' ? 'midtrans' : 'cash';
        $notes = $request->input('notes', []);

        if ($paymentMethod === 'midtrans') {
            return $this->processMidtransCheckout($request, $grouped, $pickupTime, $notes, $cart);
        }

        // --- Proses pembayaran tunai ---
        $sharedOrderCode = Order::generateOrderCode();
        $lastOrder = null;

        DB::transaction(function () use ($grouped, $pickupTime, $notes, $sharedOrderCode, &$lastOrder) {
            foreach ($grouped as $canteenId => $items) {
                $total = array_sum(array_column($items, 'subtotal'));

                $order = Order::create([
                    'user_id' => Auth::id(),
                    'canteen_id' => $canteenId,
                    'order_code' => $sharedOrderCode,
                    'status' => 'menunggu',
                    'pickup_time' => $pickupTime,
                    'total_price' => $total,
                    'notes' => $notes[$canteenId] ?? null,
                    'payment_method' => 'cash',
                    'payment_status' => 'pending',
                    'payment_code' => null,
                    'snap_token' => null,
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

        // Hapus hanya item yang sudah di-checkout dari keranjang session
        foreach (array_keys($cart) as $menuId) {
            unset($fullCart[$menuId]);
        }
        session([self::SESSION_KEY => $fullCart]);
        session()->forget('checkout_notes');
        session()->forget('checkout_selected_ids');

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'redirect' => route('order.index'),
                'message' => 'Pesanan berhasil dibuat! Silakan bayar saat mengambil pesanan.',
            ]);
        }

        return redirect()->route('order.index')
            ->with('success', 'Pesanan berhasil dibuat! Bayar saat mengambil pesanan.');
    }

    /**
     * Retry payment: generate ulang Snap token jika user belum membayar.
     * Mengembalikan JSON berisi snap_token baru.
     */
    public function retry(string $paymentCode): JsonResponse
    {
        // Ambil salah satu order dari grup payment_code ini
        $order = Order::where('payment_code', $paymentCode)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Tolak jika pesanan sudah dibatalkan
        if ($order->status === 'dibatalkan') {
            return response()->json([
                'success' => false,
                'message' => 'Pembayaran tidak dapat diulang karena pesanan telah dibatalkan.',
            ], 422);
        }

        // Tolak jika sudah lunas
        if ($order->isPaid()) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan ini sudah lunas.',
            ], 422);
        }

        // Generate Snap token baru
        try {
            $snapToken = $this->generateSnapToken($paymentCode, $order->user);

            // Update snap_token di semua order dengan payment_code yang sama
            Order::where('payment_code', $paymentCode)->update(['snap_token' => $snapToken]);

            return response()->json([
                'success' => true,
                'snap_token' => $snapToken,
            ]);
        } catch (\Exception $e) {
            Log::error('Midtrans retry token error', [
                'payment_code' => $paymentCode,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui token pembayaran. Silakan coba lagi.',
            ], 500);
        }
    }

    // -----------------------------------------------------------------------
    // Private Helpers
    // -----------------------------------------------------------------------

    /**
     * Proses checkout dengan Midtrans: generate payment_code, Snap token, simpan orders.
     * Mendukung request AJAX (wantsJson) untuk menampilkan Snap popup di halaman checkout.
     */
    private function processMidtransCheckout(Request $request, array $grouped, Carbon $pickupTime, array $notes, array $cart)
    {
        // Generate payment_code unik yang menghubungkan semua order dalam satu transaksi
        do {
            $paymentCode = 'PAY-'.now()->format('Ymd').'-'.strtoupper(Str::random(6));
        } while (Order::where('payment_code', $paymentCode)->exists());

        $user = Auth::user();
        $gross = (int) array_sum(array_column($cart, 'subtotal'));

        // Bangun item_details untuk Midtrans dari seluruh item di keranjang
        $itemDetails = [];
        foreach ($cart as $item) {
            $itemDetails[] = [
                'id' => (string) $item['menu_id'],
                'price' => (int) $item['price'],
                'quantity' => (int) $item['quantity'],
                'name' => mb_substr($item['name'], 0, 50), // Midtrans max 50 chars
            ];
        }

        $snapToken = null;

        try {
            $snapToken = $this->generateSnapToken($paymentCode, $user, $gross, $itemDetails);
        } catch (\Exception $e) {
            Log::error('Midtrans Snap token error saat checkout', [
                'payment_code' => $paymentCode,
                'error' => $e->getMessage(),
            ]);

            $errorMsg = 'Gagal terhubung ke sistem pembayaran. Silakan coba lagi atau pilih metode lain.';

            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $errorMsg], 422);
            }

            return back()->withErrors(['payment_method' => $errorMsg])->withInput();
        }

        $sharedOrderCode = Order::generateOrderCode();
        $lastOrder = null;

        DB::transaction(function () use ($grouped, $pickupTime, $notes, $paymentCode, $snapToken, $sharedOrderCode, &$lastOrder) {
            foreach ($grouped as $canteenId => $items) {
                $total = array_sum(array_column($items, 'subtotal'));

                $order = Order::create([
                    'user_id' => Auth::id(),
                    'canteen_id' => $canteenId,
                    'order_code' => $sharedOrderCode,
                    'status' => 'menunggu',
                    'pickup_time' => $pickupTime,
                    'total_price' => $total,
                    'notes' => $notes[$canteenId] ?? null,
                    'payment_method' => 'midtrans',
                    'payment_status' => 'pending',
                    'payment_code' => $paymentCode,
                    'snap_token' => $snapToken,
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

        // Hapus hanya item yang sudah di-checkout dari keranjang session
        $fullCart = session(self::SESSION_KEY, []);
        foreach (array_keys($cart) as $menuId) {
            unset($fullCart[$menuId]);
        }
        session([self::SESSION_KEY => $fullCart]);
        session()->forget('checkout_notes');
        session()->forget('checkout_selected_ids');

        // Jika request via AJAX: kembalikan snap_token ke JS agar popup Midtrans
        // muncul langsung di atas halaman checkout (tidak ada redirect sebelum bayar)
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'snap_token' => $snapToken,
                'redirect' => route('order.index'),
                'message' => 'Pesanan berhasil dibuat! Silakan selesaikan pembayaran.',
            ]);
        }

        return redirect()->route('order.index')
            ->with('success', 'Pesanan dibuat! Selesaikan pembayaran untuk melanjutkan.');
    }

    /**
     * Generate Snap token dari Midtrans.
     * Dipakai oleh processMidtransCheckout() dan retry().
     */
    private function generateSnapToken(string $paymentCode, $user, ?int $gross = null, array $itemDetails = []): string
    {
        // Inisialisasi konfigurasi Midtrans dari config/services.php
        MidtransConfig::$serverKey = config('services.midtrans.server_key');
        MidtransConfig::$isProduction = config('services.midtrans.is_production');
        MidtransConfig::$isSanitized = config('services.midtrans.is_sanitized');
        MidtransConfig::$is3ds = config('services.midtrans.is_3ds');

        // Jika gross dan itemDetails tidak diberikan (skenario retry),
        // hitung ulang dari orders yang ada di DB
        if ($gross === null) {
            $orders = Order::where('payment_code', $paymentCode)->get();
            $gross = (int) $orders->sum('total_price');

            foreach ($orders as $ord) {
                foreach ($ord->items as $item) {
                    $itemDetails[] = [
                        'id' => (string) $item->menu_id,
                        'price' => (int) $item->price,
                        'quantity' => (int) $item->qty,
                        'name' => mb_substr($item->menu->name ?? 'Menu', 0, 50),
                    ];
                }
            }
        }

        $params = [
            'transaction_details' => [
                'order_id' => $paymentCode,
                'gross_amount' => $gross,
            ],
            'item_details' => $itemDetails,
            'customer_details' => [
                'first_name' => $user->name,
                'email' => $user->email ?? ($user->nim.'@mhs.pnc.ac.id'),
                'phone' => '-',
            ],
            // Batas waktu pembayaran: 30 menit dari sekarang
            'expiry' => [
                'start_time' => now()->format('Y-m-d H:i:s O'),
                'unit' => 'minute',
                'duration' => 30,
            ],
        ];

        return Snap::getSnapToken($params);
    }

    /**
     * Parse nilai pickup_time dari radio button ke Carbon datetime yang valid.
     * Mengembalikan null jika waktu sudah terlewat.
     */
    private function parsePickupTime(string $pickupTime, ?string $customTime): ?Carbon
    {
        $now = Carbon::now();
        $date = $now->toDateString(); // YYYY-MM-DD hari ini

        if ($pickupTime === 'now') {
            // "Sekarang" = 15 menit dari saat ini
            return $now->copy()->addMinutes(15);
        }

        if ($pickupTime === 'custom') {
            if (! $customTime) {
                return null;
            }
            $parsed = Carbon::createFromFormat('Y-m-d H:i', $date.' '.$customTime);
            // Tolak jika waktu sudah lewat (berikan toleransi 1 menit)
            if ($parsed->lessThan($now->copy()->subMinute())) {
                return null;
            }

            return $parsed;
        }

        // Slot waktu preset: "09.20", "11.30", dst.
        // Ubah titik ke titik dua agar bisa di-parse sebagai H:i
        $timeString = str_replace('.', ':', $pickupTime);
        $parsed = Carbon::createFromFormat('Y-m-d H:i', $date.' '.$timeString);

        // Tolak jika waktu preset sudah lewat hari ini
        if ($parsed->lessThan($now->copy()->subMinute())) {
            return null;
        }

        return $parsed;
    }

    /**
     * Sinkronkan harga keranjang dengan data menu terbaru sebelum checkout.
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

            if (! $menu || ! $menu->isInStock()) {
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

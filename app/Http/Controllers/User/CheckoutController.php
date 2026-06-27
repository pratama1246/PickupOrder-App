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
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\Laravel\Facades\Image;

class CheckoutController extends Controller
{
    // Kunci session untuk data keranjang belanja lokal.
    private const SESSION_KEY = 'cart';

    /**
     * Mempersiapkan metadata checkout (catatan per kantin & ID menu yang dipilih).
     * Disimpan sementara di session untuk diolah di halaman konfirmasi pembayaran final,
     * menghindari penulisan record draft sampah di database.
     */
    public function prepare(Request $request): RedirectResponse
    {
        $request->validate([
            'notes' => ['nullable', 'array'],
            'notes.*' => ['nullable', 'string', 'max:500'],
            'selected_menu_ids' => ['nullable', 'array'],
            'selected_menu_ids.*' => ['nullable', 'integer'],
        ]);

        // Sanitasi catatan per kantin sebelum disimpan di session untuk mencegah injeksi HTML.
        $rawNotes = $request->input('notes', []);
        $cleanNotes = array_map(fn ($note) => $note ? strip_tags($note) : null, $rawNotes);

        session(['checkout_notes'        => $cleanNotes]);
        session(['checkout_selected_ids' => $request->input('selected_menu_ids', [])]);

        return redirect()->route('checkout.index');
    }

    /**
     * Menampilkan halaman ringkasan checkout (/checkout).
     * Menyaring item keranjang agar hanya memproses menu yang dicentang (dipilih) oleh mahasiswa.
     */
    public function index(): View|RedirectResponse
    {
        $cart = session(self::SESSION_KEY, []);
        $cart = $this->syncCartWithMenus($cart);
        session([self::SESSION_KEY => $cart]);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Keranjang belanja kosong.');
        }

        // Membatasi item checkout hanya pada menu yang dipilih (dicentang) pada halaman keranjang.
        $selectedIds = session('checkout_selected_ids', []);
        if (! empty($selectedIds)) {
            $selectedIds = array_map('intval', $selectedIds);
            $cart = array_filter($cart, fn ($item) => in_array((int) $item['menu_id'], $selectedIds));
        }

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Pilih setidaknya satu menu untuk checkout.');
        }

        // Pastikan semua menu yang di-checkout hanya berasal dari satu kantin yang sama
        $canteenIds = array_unique(array_column($cart, 'canteen_id'));
        if (count($canteenIds) > 1) {
            return redirect()->route('cart.index')->with('error', 'Anda hanya dapat melakukan checkout dari satu kantin dalam sekali transaksi.');
        }

        // Mengelompokkan item belanja berdasarkan kantin agar mahasiswa dapat melihat subtotal per warung.
        $grouped = [];
        foreach ($cart as $item) {
            if (!isset($grouped[$item['canteen_id']])) {
                $canteen = \App\Models\Canteen::find($item['canteen_id']);
                $grouped[$item['canteen_id']]['canteen_name'] = $item['canteen_name'];
                $grouped[$item['canteen_id']]['qris_image'] = $canteen ? $canteen->qris_image : null;
                $grouped[$item['canteen_id']]['items'] = [];
            }
            $grouped[$item['canteen_id']]['items'][] = $item;
        }

        $total = array_sum(array_column($cart, 'subtotal'));
        $notes = session('checkout_notes', []);

        return view('user.checkout', compact('grouped', 'total', 'notes'));
    }

    /**
     * Memproses finalisasi pemesanan makanan.
     * Mendukung metode tunai ("Bayar di Warung") dengan pengaman anti-spam,
     * serta memisahkan pesanan multi-kantin menjadi baris transaksi independen di database (DB Transaction).
     */
    public function store(Request $request)
    {
        $request->validate([
            'pickup_time' => ['required', 'string'],
            'custom_time' => ['nullable', 'required_if:pickup_time,custom', 'string', 'regex:/^\d{2}:\d{2}$/'],
            'payment_method' => ['required', 'in:qris,bayar_di_warung,qris_manual'],
            'notes' => ['nullable', 'array'],
            'notes.*' => ['nullable', 'string', 'max:500'],
            'payment_proof' => ['required_if:payment_method,qris_manual', 'nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:10240'],
        ]);

        $fullCart = $this->syncCartWithMenus(session(self::SESSION_KEY, []));
        session([self::SESSION_KEY => $fullCart]);

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

        $pickupTime = $this->parsePickupTime($request->pickup_time, $request->custom_time);
        if (! $pickupTime) {
            $errorMsg = 'Waktu pengambilan tidak valid. Pastikan waktu yang dipilih belum terlewat.';
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'errors' => ['pickup_time' => [$errorMsg]]], 422);
            }

            return back()->withErrors(['pickup_time' => $errorMsg])->withInput();
        }

        // Pengaman Anti-Spam: Membatasi mahasiswa agar tidak bisa membuat pesanan cash baru
        // apabila masih memiliki pesanan tunai aktif yang belum diambil atau dibayar di kantin.
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

        // Pengelompokan data per kantin untuk pemisahan order tiket.
        $grouped = [];
        foreach ($cart as $item) {
            $grouped[$item['canteen_id']][] = $item;
        }

        $paymentMethod = $request->payment_method;
        $notes = $request->input('notes', []);

        if ($paymentMethod === 'qris') {
            return $this->processMidtransCheckout($request, $grouped, $pickupTime, $notes, $cart);
        }

        if ($paymentMethod === 'qris_manual') {
            return $this->processQrisManualCheckout($request, $grouped, $pickupTime, $notes, $cart);
        }

        // --- Transaksi Pembayaran Tunai (Cash) ---
        // Menggunakan satu order_code induk yang sama agar mahasiswa menganggap ini satu transaksi terpadu,
        // namun di DB terbagi menjadi beberapa baris order berdasarkan kantin untuk kemudahan klaim vendor.
        $sharedOrderCode = Order::generateOrderCode();
        $lastOrder = null;

        try {
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
                        // Kunci baris menu untuk update untuk menghindari race condition
                        $menu = Menu::lockForUpdate()->findOrFail($item['menu_id']);
                        if ($menu->stock < $item['quantity']) {
                            throw new \Exception("Stok menu '{$menu->name}' tidak mencukupi. Sisa stok: {$menu->stock}.");
                        }
                        $menu->decrement('stock', $item['quantity']);

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
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }

            return back()->withErrors(['payment_method' => $e->getMessage()])->withInput();
        }

        // Menghapus item terpilih dari keranjang belanja global setelah sukses checkout.
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
     * Meminta ulang (regenerate) Snap Token Midtrans untuk pesanan online tertunda yang belum lunas.
     * Berguna jika sesi pembayaran sebelumnya terputus atau ditutup secara tidak sengaja oleh mahasiswa.
     */
    public function retry(string $paymentCode): JsonResponse
    {
        $order = Order::where('payment_code', $paymentCode)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($order->status === 'dibatalkan') {
            return response()->json([
                'success' => false,
                'message' => 'Pembayaran tidak dapat diulang karena pesanan telah dibatalkan.',
            ], 422);
        }

        if ($order->isPaid()) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan ini sudah lunas.',
            ], 422);
        }

        try {
            $snapToken = $this->generateSnapToken($paymentCode, $order->user);

            // Memperbarui token pembayaran baru ke seluruh order dengan kode pembayaran yang sama.
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
     * Mengatur proses integrasi Midtrans Snap API.
     * Membuat 'payment_code' unik gabungan dan menghasilkan token pembayaran sebelum menyimpan record order.
     */
    private function processMidtransCheckout(Request $request, array $grouped, Carbon $pickupTime, array $notes, array $cart)
    {
        // Membuat kode pelunasan unik (PAY-...) yang merepresentasikan gabungan nominal seluruh kantin.
        do {
            $paymentCode = 'PAY-'.now()->format('Ymd').'-'.strtoupper(Str::random(6));
        } while (Order::where('payment_code', $paymentCode)->exists());

        $user = Auth::user();
        $gross = (int) array_sum(array_column($cart, 'subtotal'));

        $itemDetails = [];
        foreach ($cart as $item) {
            $itemDetails[] = [
                'id' => (string) $item['menu_id'],
                'price' => (int) $item['price'],
                'quantity' => (int) $item['quantity'],
                'name' => mb_substr($item['name'], 0, 50),
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

        try {
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
                        // Kunci baris menu untuk update untuk menghindari race condition
                        $menu = Menu::lockForUpdate()->findOrFail($item['menu_id']);
                        if ($menu->stock < $item['quantity']) {
                            throw new \Exception("Stok menu '{$menu->name}' tidak mencukupi. Sisa stok: {$menu->stock}.");
                        }
                        $menu->decrement('stock', $item['quantity']);

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
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }

            return back()->withErrors(['payment_method' => $e->getMessage()])->withInput();
        }

        $fullCart = session(self::SESSION_KEY, []);
        foreach (array_keys($cart) as $menuId) {
            unset($fullCart[$menuId]);
        }
        session([self::SESSION_KEY => $fullCart]);
        session()->forget('checkout_notes');
        session()->forget('checkout_selected_ids');

        // wantsJson true mengindikasikan modal Snap akan memicu callback JS secara seamless di halaman checkout.
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
     * Memanggil Snap API Midtrans untuk mendapatkan token transaksi pembayaran.
     * Mengatur batas kedaluwarsa pembayaran online (expiry duration) selama 30 menit.
     */
    private function generateSnapToken(string $paymentCode, $user, ?int $gross = null, array $itemDetails = []): string
    {
        MidtransConfig::$serverKey = config('services.midtrans.server_key');
        MidtransConfig::$isProduction = config('services.midtrans.is_production');
        MidtransConfig::$isSanitized = config('services.midtrans.is_sanitized');
        MidtransConfig::$is3ds = config('services.midtrans.is_3ds');

        // Jika gross null (dipanggil dari alur retry), hitung ulang total belanja langsung dari DB.
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
            // Membatasi waktu pembayaran maksimal 30 menit untuk melepaskan reservasi menu sesegera mungkin.
            'expiry' => [
                'start_time' => now()->format('Y-m-d H:i:s O'),
                'unit' => 'minute',
                'duration' => 30,
            ],
        ];

        return Snap::getSnapToken($params);
    }

    /**
     * Mengubah opsi input 'pickup_time' mahasiswa menjadi format DateTime Carbon.
     * Opsi 'now' diberikan jeda persiapan default selama 15 menit.
     * Pilihan custom divalidasi dengan toleransi 1 menit dari waktu sekarang untuk mencegah pemesanan di masa lalu.
     */
    private function parsePickupTime(string $pickupTime, ?string $customTime): ?Carbon
    {
        $now = Carbon::now();
        $date = $now->toDateString();

        if ($pickupTime === 'now') {
            return $now->copy()->addMinutes(15);
        }

        if ($pickupTime === 'custom') {
            if (! $customTime) {
                return null;
            }
            $parsed = Carbon::createFromFormat('Y-m-d H:i', $date.' '.$customTime);
            if ($parsed->lessThan($now->copy()->subMinute())) {
                return null;
            }

            return $parsed;
        }

        $timeString = str_replace('.', ':', $pickupTime);
        $parsed = Carbon::createFromFormat('Y-m-d H:i', $date.' '.$timeString);

        if ($parsed->lessThan($now->copy()->subMinute())) {
            return null;
        }

        return $parsed;
    }

    /**
     * Memastikan item checkout memiliki harga terkini, stok mencukupi, dan kantin berstatus buka.
     * Jika tidak valid, item otomatis dibersihkan dari keranjang session.
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

            if (! $menu || ! $menu->isInStock() || ! $menu->canteen || ! $menu->canteen->is_open) {
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

    /**
     * Memproses pesanan dengan metode transfer QRIS Kantin (Manual).
     * Menyimpan gambar bukti transfer terkompresi dan mencatat status pending.
     */
    private function processQrisManualCheckout(Request $request, array $grouped, Carbon $pickupTime, array $notes, array $cart)
    {
        $paymentProofPath = null;
        if ($request->hasFile('payment_proof')) {
            try {
                $filename = \Illuminate\Support\Str::random(40).'.webp';
                $image = Image::decode($request->file('payment_proof'));
                $image->scale(width: 1000); // 1000px is perfect for scannable receipt details
                $webp = $image->encode(new WebpEncoder(quality: 80));
                Storage::disk('public')->put('proofs/'.$filename, $webp->toString());
                $paymentProofPath = 'proofs/'.$filename;
            } catch (\Exception $e) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'payment_proof' => 'Berkas gambar bukti pembayaran rusak atau tidak dapat diproses.',
                ]);
            }
        }

        $sharedOrderCode = Order::generateOrderCode();
        $lastOrder = null;

        try {
            DB::transaction(function () use ($grouped, $pickupTime, $notes, $sharedOrderCode, $paymentProofPath, &$lastOrder) {
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
                        'payment_method' => 'qris_manual',
                        'payment_status' => 'pending',
                        'payment_code' => null,
                        'snap_token' => null,
                        'payment_proof' => $paymentProofPath,
                    ]);

                    foreach ($items as $item) {
                        // Kunci baris menu untuk update untuk menghindari race condition
                        $menu = Menu::lockForUpdate()->findOrFail($item['menu_id']);
                        if ($menu->stock < $item['quantity']) {
                            throw new \Exception("Stok menu '{$menu->name}' tidak mencukupi. Sisa stok: {$menu->stock}.");
                        }
                        $menu->decrement('stock', $item['quantity']);

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
        } catch (\Exception $e) {
            // Delete uploaded file if transaction fails
            if ($paymentProofPath) {
                Storage::disk('public')->delete($paymentProofPath);
            }

            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }

            return back()->withErrors(['payment_method' => $e->getMessage()])->withInput();
        }

        // Hapus item dari keranjang
        $fullCart = session(self::SESSION_KEY, []);
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
                'message' => 'Pesanan berhasil dibuat! Silakan tunggu verifikasi pembayaran.',
            ]);
        }

        return redirect()->route('order.index')
            ->with('success', 'Pesanan dibuat! Silakan tunggu verifikasi pembayaran.');
    }
}

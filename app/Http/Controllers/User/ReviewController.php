<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Menyimpan ulasan (review) masukan untuk setiap menu makanan dari pesanan yang telah selesai.
     * Menggunakan filter order berstatus 'selesai' dan milik user bersangkutan untuk mencegah ulasan palsu,
     * serta menggunakan updateOrCreate agar bersifat idempoten jika formulir dikirim berulang kali.
     */
    public function store(Request $request, $orderId): RedirectResponse
    {
        // Memastikan mahasiswa hanya bisa menilai makanan dari pesanan milik sendiri yang benar-benar telah tuntas.
        $order = Order::where('id', $orderId)
            ->where('user_id', Auth::id())
            ->where('status', 'selesai')
            ->firstOrFail();

        $request->validate([
            'reviews' => ['required', 'array'],
            'reviews.*.menu_id' => ['required', 'exists:menus,id'],
            'reviews.*.rating' => ['required', 'integer', 'min:1', 'max:5'],
            'reviews.*.comment' => ['nullable', 'string', 'max:500'],
            'reviews.*.is_anonymous' => ['nullable', 'boolean'],
        ]);

        // Menyimpan ulasan terpisah untuk masing-masing menu dalam satu transaksi pesanan.
        // updateOrCreate digunakan agar pengguna dapat memperbarui ulasan lama jika mengirimkan form edit ulasan.
        foreach ($request->reviews as $reviewData) {
            // Sanitasi komentar untuk mencegah tag HTML/link phishing masuk ke tampilan ulasan vendor.
            $comment = isset($reviewData['comment']) ? strip_tags($reviewData['comment']) : null;

            Review::updateOrCreate(
                [
                    'user_id'  => Auth::id(),
                    'order_id' => $order->id,
                    'menu_id'  => $reviewData['menu_id'],
                ],
                [
                    'rating'       => $reviewData['rating'],
                    'comment'      => $comment,
                    'is_anonymous' => isset($reviewData['is_anonymous']) ? (bool) $reviewData['is_anonymous'] : false,
                ]
            );
        }

        return redirect()->back()->with('success', 'Ulasan berhasil dikirim! Terima kasih atas penilaian Anda.');
    }
}

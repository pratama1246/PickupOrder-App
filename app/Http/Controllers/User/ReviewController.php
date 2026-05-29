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
     * Simpan ulasan untuk pesanan yang sudah selesai.
     */
    public function store(Request $request, $orderId): RedirectResponse
    {
        $order = Order::where('id', $orderId)
            ->where('user_id', Auth::id())
            ->where('status', 'selesai')
            ->firstOrFail();

        // Validasi input
        $request->validate([
            'reviews' => ['required', 'array'],
            'reviews.*.menu_id' => ['required', 'exists:menus,id'],
            'reviews.*.rating' => ['required', 'integer', 'min:1', 'max:5'],
            'reviews.*.comment' => ['nullable', 'string', 'max:500'],
            'reviews.*.is_anonymous' => ['nullable', 'boolean'],
        ]);

        // Simpan ulasan untuk setiap menu
        foreach ($request->reviews as $reviewData) {
            Review::updateOrCreate(
                [
                    'user_id' => Auth::id(),
                    'order_id' => $order->id,
                    'menu_id' => $reviewData['menu_id'],
                ],
                [
                    'rating' => $reviewData['rating'],
                    'comment' => $reviewData['comment'] ?? null,
                    'is_anonymous' => isset($reviewData['is_anonymous']) ? (bool) $reviewData['is_anonymous'] : false,
                ]
            );
        }

        return redirect()->back()->with('success', 'Ulasan berhasil dikirim! Terima kasih atas penilaian Anda.');
    }
}

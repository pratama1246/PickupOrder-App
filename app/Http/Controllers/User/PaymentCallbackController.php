<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentCallbackController extends Controller
{
    /**
     * Tangani notifikasi webhook dari Midtrans (POST /payment/notification).
     *
     * Midtrans mengirim JSON payload langsung ke endpoint ini.
     * Kita baca manual dari php://input agar tidak bergantung
     * pada Midtrans\Notification yang butuh API call tambahan.
     */
    public function handle(Request $request): JsonResponse
    {
        // Baca raw JSON dari body request
        $payload = json_decode($request->getContent(), true);

        if (empty($payload) || ! isset($payload['order_id'])) {
            Log::warning('Midtrans webhook: payload kosong atau tidak valid', ['body' => $request->getContent()]);

            return response()->json(['message' => 'Invalid payload'], 400);
        }

        $orderId           = $payload['order_id'];
        $transactionStatus = $payload['transaction_status'] ?? null;
        $fraudStatus       = $payload['fraud_status'] ?? 'accept';
        $statusCode        = $payload['status_code'] ?? '200';
        $grossAmount       = $payload['gross_amount'] ?? '0';
        $signatureKey      = $payload['signature_key'] ?? '';

        // --- Validasi Signature Key ---
        // Format SHA512: order_id + status_code + gross_amount + server_key
        $serverKey         = config('services.midtrans.server_key');
        $expectedSignature = hash('sha512', $orderId.$statusCode.$grossAmount.$serverKey);

        if ($signatureKey !== $expectedSignature) {
            Log::warning('Midtrans webhook: signature tidak valid', [
                'order_id'          => $orderId,
                'received_sig'      => $signatureKey,
                'expected_sig'      => $expectedSignature,
            ]);

            return response()->json(['message' => 'Invalid signature'], 403);
        }

        // Cari semua order yang terkait dengan payment_code ini
        $orders = Order::where('payment_code', $orderId)->get();

        if ($orders->isEmpty()) {
            Log::warning('Midtrans webhook: payment_code tidak ditemukan', ['payment_code' => $orderId]);

            // Return 200 agar Midtrans tidak retry terus-menerus
            return response()->json(['message' => 'Order not found, ignored'], 200);
        }

        Log::info('Midtrans webhook diterima', [
            'payment_code'       => $orderId,
            'transaction_status' => $transactionStatus,
            'fraud_status'       => $fraudStatus,
        ]);

        // --- Update status berdasarkan transaction_status ---
        if ($transactionStatus === 'capture') {
            if ($fraudStatus === 'accept') {
                $orders->each->update(['payment_status' => 'paid']);
            }
            // challenge: biarkan pending, tunggu keputusan manual di dashboard Midtrans
        } elseif ($transactionStatus === 'settlement') {
            // Pembayaran non-kartu (QRIS, e-wallet, VA) berhasil settled
            $orders->each->update(['payment_status' => 'paid']);

        } elseif ($transactionStatus === 'pending') {
            $orders->each->update(['payment_status' => 'pending']);

        } elseif (in_array($transactionStatus, ['deny', 'cancel', 'failure'])) {
            $orders->each->update([
                'payment_status' => 'failed',
                'status'         => 'dibatalkan',
            ]);
        } elseif ($transactionStatus === 'expire') {
            $orders->each->update([
                'payment_status' => 'expired',
                'status'         => 'dibatalkan',
            ]);
        }

        return response()->json(['message' => 'OK'], 200);
    }
}

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
     * Menangani notifikasi webhook pembaruan transaksi dari Midtrans (POST /payment/notification).
     * Melakukan verifikasi keamanan signature key secara mandiri tanpa SDK eksternal untuk menghindari
     * overhead latensi koneksi balik (back-channel API call) ke server Midtrans.
     */
    public function handle(Request $request): JsonResponse
    {
        // Membaca isi raw JSON langsung dari body request.
        $payload = json_decode($request->getContent(), true);

        if (empty($payload) || ! isset($payload['order_id'])) {
            Log::warning('Midtrans webhook: payload kosong atau tidak valid', ['body' => $request->getContent()]);

            return response()->json(['message' => 'Invalid payload'], 400);
        }

        $orderId = $payload['order_id'];
        $transactionStatus = $payload['transaction_status'] ?? null;
        $fraudStatus = $payload['fraud_status'] ?? 'accept';
        $statusCode = $payload['status_code'] ?? '200';
        $grossAmount = $payload['gross_amount'] ?? '0';
        $signatureKey = $payload['signature_key'] ?? '';

        // --- Verifikasi Signature Key (Keamanan Webhook) ---
        // Formula SHA512: order_id + status_code + gross_amount + server_key.
        // Melindungi sistem dari eksploitasi payload palsu oleh pihak ketiga tidak dikenal.
        $serverKey = config('services.midtrans.server_key');
        $expectedSignature = hash('sha512', $orderId.$statusCode.$grossAmount.$serverKey);

        if ($signatureKey !== $expectedSignature) {
            Log::warning('Midtrans webhook: signature tidak valid', [
                'order_id' => $orderId,
                'received_sig' => $signatureKey,
                'expected_sig' => $expectedSignature,
            ]);

            return response()->json(['message' => 'Invalid signature'], 403);
        }

        // Mencari seluruh relasi pesanan di database yang dibayar bersama dalam grup payment_code ini.
        $orders = Order::where('payment_code', $orderId)->get();

        if ($orders->isEmpty()) {
            Log::warning('Midtrans webhook: payment_code tidak ditemukan', ['payment_code' => $orderId]);

            // Mengembalikan status 200 OK agar Midtrans menghentikan antrean retry notifikasi (webhook throttling).
            return response()->json(['message' => 'Order not found, ignored'], 200);
        }

        Log::info('Midtrans webhook diterima', [
            'payment_code' => $orderId,
            'transaction_status' => $transactionStatus,
            'fraud_status' => $fraudStatus,
        ]);

        // --- Sinkronisasi Status Transaksi dengan Database Lokal ---
        if ($transactionStatus === 'capture') {
            if ($fraudStatus === 'accept') {
                $orders->each->update(['payment_status' => 'paid']);
            }
        } elseif ($transactionStatus === 'settlement') {
            // Pelunasan sukses via kanal non-kartu seperti QRIS, e-wallet, atau Virtual Account.
            $orders->each->update(['payment_status' => 'paid']);

        } elseif ($transactionStatus === 'pending') {
            $orders->each->update(['payment_status' => 'pending']);

        } elseif (in_array($transactionStatus, ['deny', 'cancel', 'failure'])) {
            $orders->each->update([
                'payment_status' => 'failed',
                'status' => 'dibatalkan',
            ]);
        } elseif ($transactionStatus === 'expire') {
            // Batas waktu 30 menit pembayaran habis, kembalikan status pesanan ke batal.
            $orders->each->update([
                'payment_status' => 'expired',
                'status' => 'dibatalkan',
            ]);
        }

        return response()->json(['message' => 'OK'], 200);
    }
}

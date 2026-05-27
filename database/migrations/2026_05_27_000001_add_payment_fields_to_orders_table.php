<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambahkan kolom-kolom pembayaran ke tabel orders.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // 'cash' = bayar di warung, 'midtrans' = bayar online via Snap
            $table->string('payment_method')->default('cash')->after('notes');

            // 'pending' = belum lunas, 'paid' = lunas, 'failed' = gagal, 'expired' = kedaluwarsa
            $table->string('payment_status')->default('pending')->after('payment_method');

            // Kode grup pembayaran yang mengikat beberapa order dalam satu transaksi Midtrans
            // Format: PAY-YYYYMMDD-XXXXXX. Null untuk pembayaran tunai.
            $table->string('payment_code')->nullable()->index()->after('payment_status');

            // Token Snap Midtrans untuk membuka kembali popup pembayaran
            $table->string('snap_token', 512)->nullable()->after('payment_code');
        });
    }

    /**
     * Batalkan perubahan kolom.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'payment_status', 'payment_code', 'snap_token']);
        });
    }
};

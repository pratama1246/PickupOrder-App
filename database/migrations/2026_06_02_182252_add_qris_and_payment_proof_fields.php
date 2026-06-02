<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('canteens', function (Blueprint $table) {
            $table->string('qris_image')->nullable()->after('image');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_proof')->nullable()->after('snap_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('canteens', function (Blueprint $table) {
            $table->dropColumn('qris_image');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('payment_proof');
        });
    }
};

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
            $table->index('is_open');
        });

        Schema::table('menus', function (Blueprint $table) {
            $table->index('is_available');
            $table->index('category');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->index('order_code');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('canteens', function (Blueprint $table) {
            $table->dropIndex(['is_open']);
        });

        Schema::table('menus', function (Blueprint $table) {
            $table->dropIndex(['is_available']);
            $table->dropIndex(['category']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['order_code']);
            $table->dropIndex(['status']);
        });
    }
};

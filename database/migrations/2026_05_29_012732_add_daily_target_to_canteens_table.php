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
            $table->integer('daily_target')->default(500000)->after('is_open');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('canteens', function (Blueprint $table) {
            $table->dropColumn('daily_target');
        });
    }
};

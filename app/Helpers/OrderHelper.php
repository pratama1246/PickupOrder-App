<?php

namespace App\Helpers;

use Carbon\Carbon;

class OrderHelper
{
    /**
     * Memeriksa apakah waktu sekarang berada di dalam jam operasional pemesanan online.
     *
     * @return bool
     */
    public static function isOrderTimeActive(): bool
    {
        $now = Carbon::now();
        $startTime = Carbon::createFromTimeString(config('app.order_hours.start'));
        $endTime = Carbon::createFromTimeString(config('app.order_hours.end'));

        return $now->between($startTime, $endTime);
    }
}

<?php

namespace App\Http\Middleware;

use App\Helpers\OrderHelper;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckOnlineOrderHours
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! OrderHelper::isOrderTimeActive()) {
            $activeDays = OrderHelper::getActiveDaysFormatted();
            $timeRange = config('app.order_hours.start') . ' - ' . config('app.order_hours.end');

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pemesanan online saat ini sedang tutup. Pemesanan online dapat dilakukan pada hari ' . $activeDays . ' pukul ' . $timeRange . ' WIB.'
                ], 422);
            }

            return redirect()->route('cart.index')->with('error', 'Pemesanan online saat ini sedang tutup (Buka: hari ' . $activeDays . ' pukul ' . $timeRange . ' WIB).');
        }

        return $next($request);
    }
}

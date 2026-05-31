<?php

namespace App\Helpers;

use Carbon\Carbon;

class OrderHelper
{
    /**
     * Pemetaan nama hari ke angka hari ISO-8601 (1 = Senin, 7 = Minggu).
     *
     * @var array<string, int>
     */
    private static array $dayMapping = [
        // English long
        'monday' => 1, 'tuesday' => 2, 'wednesday' => 3, 'thursday' => 4, 'friday' => 5, 'saturday' => 6, 'sunday' => 7,
        // English short
        'mon' => 1, 'tue' => 2, 'wed' => 3, 'thu' => 4, 'fri' => 5, 'sat' => 6, 'sun' => 7,
        // Indonesian long
        'senin' => 1, 'selasa' => 2, 'rabu' => 3, 'kamis' => 4, 'jumat' => 5, 'sabtu' => 6, 'minggu' => 7,
        // Indonesian short
        'sen' => 1, 'sel' => 2, 'rab' => 3, 'kam' => 4, 'jum' => 5, 'sab' => 6, 'min' => 7,
    ];

    /**
     * Memeriksa apakah waktu sekarang berada di dalam jam dan hari operasional pemesanan online.
     *
     * @return bool
     */
    public static function isOrderTimeActive(): bool
    {
        $now = Carbon::now();

        if (! self::isOrderDayActive($now)) {
            return false;
        }

        $startTime = Carbon::createFromTimeString(config('app.order_hours.start'));
        $endTime = Carbon::createFromTimeString(config('app.order_hours.end'));

        return $now->between($startTime, $endTime);
    }

    /**
     * Memeriksa apakah hari sekarang berada di dalam hari operasional pemesanan online.
     *
     * @param  \Carbon\Carbon|null  $date
     * @return bool
     */
    public static function isOrderDayActive(Carbon $date = null): bool
    {
        $date = $date ?? Carbon::now();
        $configDays = config('app.order_days');

        // Jika kosong, '*' atau 'all', default ke setiap hari aktif
        if (empty($configDays) || $configDays === '*' || strtolower($configDays) === 'all') {
            return true;
        }

        $activeDays = array_map('trim', explode(',', $configDays));
        $currentDayIso = $date->dayOfWeekIso; // 1 (Senin) - 7 (Minggu)

        $allowedIsoDays = [];
        foreach ($activeDays as $day) {
            $dayLower = strtolower($day);
            if (isset(self::$dayMapping[$dayLower])) {
                $allowedIsoDays[] = self::$dayMapping[$dayLower];
            } elseif (is_numeric($day)) {
                $dayNum = (int) $day;
                if ($dayNum === 0) {
                    $allowedIsoDays[] = 7; // 0 diartikan sebagai hari Minggu
                } elseif ($dayNum >= 1 && $dayNum <= 7) {
                    $allowedIsoDays[] = $dayNum;
                }
            }
        }

        // Jika tidak ada hari yang valid terkonfigurasi, izinkan semua hari
        if (empty($allowedIsoDays)) {
            return true;
        }

        return in_array($currentDayIso, $allowedIsoDays);
    }

    /**
     * Mengembalikan representasi teks ramah bahasa Indonesia untuk hari-hari operasional.
     * Contoh: "Senin - Jumat", "Senin, Rabu & Jumat", atau "Setiap Hari".
     *
     * @return string
     */
    public static function getActiveDaysFormatted(): string
    {
        $configDays = config('app.order_days');
        if (empty($configDays) || $configDays === '*' || strtolower($configDays) === 'all') {
            return 'Setiap Hari';
        }

        $activeDays = array_map('trim', explode(',', $configDays));
        $allowedIsoDays = [];
        foreach ($activeDays as $day) {
            $dayLower = strtolower($day);
            if (isset(self::$dayMapping[$dayLower])) {
                $allowedIsoDays[] = self::$dayMapping[$dayLower];
            } elseif (is_numeric($day)) {
                $dayNum = (int) $day;
                if ($dayNum === 0) {
                    $allowedIsoDays[] = 7;
                } elseif ($dayNum >= 1 && $dayNum <= 7) {
                    $allowedIsoDays[] = $dayNum;
                }
            }
        }

        if (empty($allowedIsoDays)) {
            return 'Setiap Hari';
        }

        // Urutkan hari secara unik
        $allowedIsoDays = array_unique($allowedIsoDays);
        sort($allowedIsoDays);

        $indonesianDays = [
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            7 => 'Minggu',
        ];

        // Jika semua 7 hari aktif
        if (count($allowedIsoDays) === 7) {
            return 'Setiap Hari';
        }

        // Cek apakah hari berurutan, misal 1 sampai 5 (Senin - Jumat)
        $isConsecutive = true;
        for ($i = 0; $i < count($allowedIsoDays) - 1; $i++) {
            if ($allowedIsoDays[$i + 1] !== $allowedIsoDays[$i] + 1) {
                $isConsecutive = false;
                break;
            }
        }

        if ($isConsecutive && count($allowedIsoDays) > 1) {
            $startDay = $indonesianDays[min($allowedIsoDays)];
            $endDay = $indonesianDays[max($allowedIsoDays)];
            return "$startDay - $endDay";
        }

        // Jika tidak berurutan, daftarkan nama-namanya
        $names = array_map(function ($dayNum) use ($indonesianDays) {
            return $indonesianDays[$dayNum];
        }, $allowedIsoDays);

        if (count($names) === 1) {
            return $names[0];
        }

        $last = array_pop($names);
        return implode(', ', $names) . ' & ' . $last;
    }
}

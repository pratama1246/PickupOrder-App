<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Canteen extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'image',
        'is_open',
        'daily_target',
        'qris_image',
    ];

    protected $casts = [
        'is_open' => 'boolean',
    ];

    /**
     * Relasi ke User pemilik kantin (Vendor).
     * Digunakan untuk memvalidasi hak akses pengelolaan dashboard vendor.
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Semua menu makanan dan minuman yang didaftarkan oleh kantin ini.
     */
    public function menus(): HasMany
    {
        return $this->hasMany(Menu::class);
    }

    /**
     * Riwayat seluruh pesanan yang masuk ke kantin ini.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Menu aktif yang siap dipesan (is_available aktif dan stok tersedia).
     * Digunakan pada halaman beranda & pencarian agar pengguna tidak memesan menu kosong.
     */
    public function availableMenus(): HasMany
    {
        return $this->hasMany(Menu::class)->where('is_available', true)->where('stock', '>', 0);
    }

    /**
     * Mengakses seluruh ulasan pelanggan secara langsung dari semua menu milik kantin ini.
     */
    public function reviews(): \Illuminate\Database\Eloquent\Relations\HasManyThrough
    {
        return $this->hasManyThrough(Review::class, Menu::class);
    }

    /**
     * Mengembalikan rata-rata rating kantin.
     * Menggunakan cache attribute hasil eager loading 'withAvg' jika tersedia untuk optimasi query,
     * dan memberikan nilai default 5.0 agar kantin baru memiliki impresi awal yang baik.
     */
    public function getAverageRatingAttribute(): float
    {
        if (array_key_exists('reviews_avg_rating', $this->attributes)) {
            $val = (float) $this->attributes['reviews_avg_rating'];

            return $val > 0 ? $val : 5.0;
        }
        $avg = (float) $this->reviews()->avg('rating');

        return $avg > 0 ? $avg : 5.0;
    }

    /**
     * Menghitung estimasi waktu penyajian makanan berdasarkan jumlah antrean aktif di kantin.
     * Menggunakan asumsi pengerjaan rata-rata 5 menit per antrean dengan range toleransi 5 menit.
     */
    public function getEstimatedQueueTimeAttribute(): string
    {
        $activeOrdersCount = $this->orders()
            ->whereIn('status', ['menunggu', 'dimasak'])
            ->count();

        $minTime = ($activeOrdersCount + 1) * 5;
        $maxTime = $minTime + 5;

        return "{$minTime} - {$maxTime} Menit";
    }
}

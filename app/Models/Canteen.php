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
    ];

    protected $casts = [
        'is_open' => 'boolean',
    ];

    /**
     * Vendor (pemilik) kantin ini.
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Semua menu makanan yang dimiliki kantin ini.
     */
    public function menus(): HasMany
    {
        return $this->hasMany(Menu::class);
    }

    /**
     * Semua pesanan yang masuk ke kantin ini.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Hanya menu yang tersedia (is_available = true & stok > 0).
     */
    public function availableMenus(): HasMany
    {
        return $this->hasMany(Menu::class)->where('is_available', true)->where('stock', '>', 0);
    }

    /**
     * Semua ulasan untuk menu-menu di kantin ini.
     */
    public function reviews(): \Illuminate\Database\Eloquent\Relations\HasManyThrough
    {
        return $this->hasManyThrough(Review::class, Menu::class);
    }

    /**
     * Rata-rata rating kantin. Default 5.0 jika belum ada ulasan.
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
}

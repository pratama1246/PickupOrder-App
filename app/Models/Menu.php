<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        'canteen_id',
        'category',
        'name',
        'description',
        'price',
        'image',
        'stock',
        'is_available',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_available' => 'boolean',
        'stock' => 'integer',
    ];

    /**
     * Kantin pemilik menu ini.
     */
    public function canteen(): BelongsTo
    {
        return $this->belongsTo(Canteen::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Semua ulasan untuk menu ini.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Rata-rata rating menu. Default 5.0 jika belum ada ulasan.
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
     * Menghitung total ulasan menu.
     */
    public function getTotalReviewsAttribute(): int
    {
        return $this->reviews()->count();
    }

    /**
     * Cek apakah menu ini tersedia (available dan stok > 0).
     */
    public function isInStock(): bool
    {
        return $this->is_available && $this->stock > 0;
    }

    /**
     * Format harga ke rupiah tanpa desimal.
     */
    public function getFormattedPriceAttribute(): string
    {
        return 'Rp '.number_format($this->price, 0, ',', '.');
    }
}

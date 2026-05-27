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
     * Menghitung rata-rata rating menu.
     */
    public function getAverageRatingAttribute(): float
    {
        return (float) $this->reviews()->avg('rating') ?: 0.0;
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

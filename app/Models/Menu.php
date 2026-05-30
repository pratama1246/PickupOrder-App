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
     * Relasi ke Kantin pemilik menu ini.
     * Digunakan untuk memvalidasi status operasional kantin pembuat menu.
     */
    public function canteen(): BelongsTo
    {
        return $this->belongsTo(Canteen::class);
    }

    /**
     * Riwayat pembelian menu ini di seluruh item transaksi.
     * Digunakan untuk menghitung popularitas menu dan analisis penjualan vendor.
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Relasi ke seluruh ulasan rating/komentar khusus untuk menu makanan/minuman ini.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Mengembalikan rata-rata rating menu.
     * Menggunakan cache attribute hasil eager loading 'withAvg' jika tersedia untuk optimasi query,
     * dan memberikan nilai default 5.0 agar menu baru memiliki impresi awal yang baik.
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
     * Menghitung jumlah total ulasan yang telah dikirimkan oleh pengguna untuk menu ini.
     */
    public function getTotalReviewsAttribute(): int
    {
        return $this->reviews()->count();
    }

    /**
     * Memeriksa kelayakan beli menu (aktif dan stok fisik di atas 0).
     * Mencegah pembeli memasukkan menu kosong ke dalam keranjang belanja.
     */
    public function isInStock(): bool
    {
        return $this->is_available && $this->stock > 0;
    }

    /**
     * Memeriksa apakah menu siap untuk dipesan (stok tersedia & kantin sedang buka).
     */
    public function isOrderable(): bool
    {
        return $this->isInStock() && ($this->canteen && $this->canteen->is_open);
    }

    /**
     * Mengubah nominal desimal database menjadi format mata uang Rupiah (IDR).
     * Disesuaikan dengan standar pelaporan keuangan kantin Politeknik Negeri Cilacap.
     */
    public function getFormattedPriceAttribute(): string
    {
        return 'Rp '.number_format($this->price, 0, ',', '.');
    }
}

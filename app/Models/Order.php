<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'canteen_id',
        'order_code',
        'status',
        'pickup_time',
        'total_price',
        'notes',
    ];

    protected $casts = [
        'pickup_time' => 'datetime',
        'total_price' => 'decimal:2',
    ];

    /**
     * Generate order_code otomatis sebelum disimpan ke DB.
     * Format: PNC-ORD-YYYYMMDD-XXXX
     */
    protected static function booted(): void
    {
        static::creating(function (Order $order) {
            if (empty($order->order_code)) {
                $order->order_code = self::generateOrderCode();
            }
        });
    }

    private static function generateOrderCode(): string
    {
        do {
            $code = 'PNC-ORD-'.now()->format('Ymd').'-'.strtoupper(Str::random(4));
        } while (self::where('order_code', $code)->exists());

        return $code;
    }

    /**
     * Mahasiswa yang membuat pesanan ini.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Kantin tujuan pesanan ini.
     */
    public function canteen(): BelongsTo
    {
        return $this->belongsTo(Canteen::class);
    }

    /**
     * Semua item detail pesanan.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Pemetaan status DB ke label UI untuk <x-status-badge>.
     * Status DB: menunggu, dimasak, siap_diambil, selesai, dibatalkan
     * Status badge: Menunggu, Diproses, Selesai, Dibatalkan
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'menunggu' => 'Menunggu',
            'dimasak', 'siap_diambil' => 'Diproses',
            'selesai' => 'Selesai',
            'dibatalkan' => 'Dibatalkan',
            default => 'Menunggu',
        };
    }

    /**
     * Format total harga ke rupiah.
     */
    public function getFormattedTotalAttribute(): string
    {
        return 'Rp '.number_format($this->total_price, 0, ',', '.');
    }

    /**
     * Scope: filter berdasarkan status.
     */
    public function scopeWithStatus($query, string $status)
    {
        return $query->where('status', $status);
    }
}

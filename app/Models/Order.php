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
        'payment_method',
        'payment_status',
        'payment_code',
        'snap_token',
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

    public static function generateOrderCode(): string
    {
        do {
            $code = 'PNC-ORD-'.now()->format('Ymd').'-'.strtoupper(Str::random(6));
        } while (self::where('order_code', $code)->exists());

        return $code;
    }

    /**
     * Dapatkan kode pengambilan (6 karakter terakhir dari order_code)
     */
    public function getPickupCodeAttribute(): string
    {
        $parts = explode('-', $this->order_code);

        return end($parts);
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
     * Ulasan yang diberikan untuk pesanan ini.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
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
            'dimasak' => 'Diproses',
            'siap_diambil' => 'Siap Diambil',
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
     * Label ramah pengguna untuk metode pembayaran.
     */
    public function getPaymentMethodLabelAttribute(): string
    {
        return match ($this->payment_method) {
            'midtrans' => 'Online (QRIS / E-Wallet)',
            'cash' => 'Bayar di Warung',
            default => 'Bayar di Warung',
        };
    }

    /**
     * Label ramah pengguna untuk status pembayaran.
     */
    public function getPaymentStatusLabelAttribute(): string
    {
        return match ($this->payment_status) {
            'pending' => 'Belum Dibayar',
            'paid' => 'Lunas',
            'failed' => 'Gagal',
            'expired' => 'Kedaluwarsa',
            default => 'Belum Dibayar',
        };
    }

    /**
     * Cek apakah pesanan sudah lunas.
     */
    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    /**
     * Hitung posisi antrian saat ini di kantin.
     */
    public function getQueuePositionAttribute(): int
    {
        if (! in_array($this->status, ['menunggu', 'dimasak'])) {
            return 0;
        }

        // Hitung order di kantin yang sama, dengan status 'menunggu' atau 'dimasak', yang dibuat sebelum order ini
        $count = self::where('canteen_id', $this->canteen_id)
            ->whereIn('status', ['menunggu', 'dimasak'])
            ->where('id', '<', $this->id)
            ->count();

        return $count + 1;
    }

    /**
     * Hitung estimasi waktu berdasarkan antrian (asumsi 5 menit per pesanan).
     */
    public function getEstimatedTimeAttribute(): int
    {
        return $this->queue_position * 5;
    }

    /**
     * Scope: filter berdasarkan status.
     */
    public function scopeWithStatus($query, string $status)
    {
        return $query->where('status', $status);
    }
}
